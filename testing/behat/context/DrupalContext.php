<?php

namespace Drupal\degov\Behat\Context;

use Behat\Mink\Exception\ResponseTextException;
use Drupal\degov_theming\Factory\FilesystemFactory;
use Drupal\Driver\DrupalDriver;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Vocabulary;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Drupal\Core\File\FileSystem as DrupalFilesystem;

class DrupalContext extends RawDrupalContext {

  /** @var array */
  protected $trash = [];

  public function __construct() {
    $driver = new DrupalDriver(DRUPAL_ROOT, '');
    $driver->setCoreFromVersion();

    // Bootstrap Drupal.
    $driver->bootstrap();
  }

  /**
   * @Given /^I create vocabulary with name "([^"]*)" and vid "([^"]*)"$/
   */
  public function createVocabulary($name, $vid) {
    $vocabulary = \Drupal::entityQuery('taxonomy_vocabulary')
      ->condition('vid', $vid)
      ->execute();

    if (empty($vocabulary)) {
      $vocabulary = Vocabulary::create([
        'name' => $name,
        'vid'  => $vid,
      ]);
      $vocabulary->save();
    }
  }

  /**
   * @Given /^I create (\d+) nodes of type "([^"]*)"$/
   */
  public function iCreateNodesOfType($number, $type) {
    for ($i = 0; $i <= $number; $i++) {
      $node = new \stdClass();
      $node->type = $type;
      $node->title = $this->createRandomString();
      $node->body = $this->createRandomString();
      $this->nodeCreate($node);
    }
  }

  private function createRandomString($length = 10) {
    return substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", $length)), 0, $length);
  }

  /**
   * @Given Node access records are rebuild.
   */
  public function nodeAccessRecordsAreRebuild() {
    node_access_rebuild();
  }

  /**
   * @Then /^I open node edit form by node title "([^"]*)"$/
   * @param string $title
   */
  public function openNodeEditFormByTitle($title) {
    $query = \Drupal::service('database')->select('node_field_data', 'nfd')
      ->fields('nfd', ['nid'])
      ->condition('nfd.title', $title);

    $this->visitPath('/node/' . $query->execute()->fetchField() . '/edit');
  }

  /**
   * @Then /^I open node view by node title "([^"]*)"$/
   * @param string $title
   */
  public function openNodeViewByTitle($title) {
    $query = \Drupal::service('database')->select('node_field_data', 'nfd')
      ->fields('nfd', ['nid'])
      ->condition('nfd.title', $title);

    $this->visitPath('/node/' . $query->execute()->fetchField());
  }

  /**
   * @Then /^I click by CSS class "([^"]*)"$/
   * @param string $class
   */
  public function clickByCSSClass(string $class) {
    $page   = $this->getSession()->getPage();
    $button = $page->find('xpath', '//*[contains(@class, "' . $class . '")]');
    $button->click();
  }

  /**
   * @Then /^I click by CSS id "([^"]*)"$/
   */
  public function clickByCSSId(string $id)
  {
    $page   = $this->getSession()->getPage();
    $button = $page->find('xpath', '//*[contains(@id, "' . $id . '")]');
    $button->click();
  }

  /**
   * @Then /^I click by XPath "([^"]*)"$/
   * @param string $xpath
   */
  public function iClickByXpath(string $xpath)
  {
    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
    ); // runs the actual query and returns the element

    // errors must not pass silently
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', $xpath));
    }

    // ok, let's click on it
    $element->click();
  }

  /**
   * @Then /^I proof Checkbox with id "([^"]*)" has value"([^"]*)"$/
   */
  public function iProofCheckboxWithIdHasValue($id, $checkfor) {
    $Page = $this->getSession()->getPage();
    $isChecked = $Page->find('css', 'input[type="checkbox"]:checked#' . $id);
    $status = ($isChecked) ? "checked" : "unchecked";
    if (
      ($checkfor == "checked" && $isChecked == true) ||
      ($checkfor == "unchecked" && $isChecked == false)
    ) {
      return true;
    }
    else {
      throw new \Exception('Checkbox was ' . $status . ' when expecting ' . $checkfor);
      return false;
    }
  }

  /**
   * @Given /^I should see the option "([^"]*)" in "([^"]*)"$/
   */
  public function iShouldSeeTheOptionIn(string $value, string $id) {
    $page = $this->getSession()->getPage();
    /** @var $selectElement \Behat\Mink\Element\NodeElement */
    $selectElement = $page->find('xpath', '//select[@id = "' . $id . '"]');
    $element = $selectElement->find('css', 'option[value=' . $value . ']');
    if (!$element) {
      throw new \Exception("There is no option with the value '$value' in the select '$id'");
    }
  }

  /**
   * @Given /^I have an normal_page with a slideshow paragraph reference$/
   */
  public function normalPageWithSlideshow() {
    /**
     * @var FilesystemFactory $symfonyFilesystem
     */
    $filesystemFactory = \Drupal::service('degov_theming.filesystem_factory');
    /**
     * @var SymfonyFilesystem $filesystem
     */
    $symfonyFilesystem = $filesystemFactory->create();

    /**
     * @var DrupalFilesystem $drupalFilesystem
     */
    $drupalFilesystem = \Drupal::service('file_system');

    $symfonyFilesystem->copy(
      drupal_get_path('profile', 'nrwgov') . '/testing/fixtures/images/leaning-tower-of-pisa.jpg',
      $drupalFilesystem->realpath(file_default_scheme() . "://") . '/leaning-tower-of-pisa.jpg'
    );

    $imageFileEntity = File::create([
      'uid'      => 1,
      'filename' => 'leaning-tower-of-pisa.jpg',
      'uri'      => 'public://leaning-tower-of-pisa.jpg',
      'status'   => 1,
    ]);
    $imageFileEntity->save();

    $media = Media::create([
      'bundle'              => 'image',
      'field_title'         => 'Some image',
      'field_copyright'     => 'Some copyright',
      'field_image_caption' => 'Some image caption',
      'image'               => $imageFileEntity->id(),
    ]);
    $media->save();

    $paragraphSlide = Paragraph::create([
      'type'              => 'slide',
      'field_slide_media' => $media,
    ]);
    $paragraphSlide->save();

    $paragraphSlideshow = Paragraph::create([
      'type'                   => 'slideshow',
      'field_slideshow_slides' => $paragraphSlide,
      'field_slideshow_type'   => 'Typ 1',
    ]);
    $paragraphSlideshow->save();

    $node = Node::create([
      'title'                   => 'An normal page with a slideshow',
      'type'                    => 'normal_page',
      'moderation_state'        => 'published',
      'field_header_paragraphs' => [$paragraphSlideshow],
    ]);
    $node->save();
  }

  /**
   * @Given /^I created a media of type "([^"]*)" named "([^"]*)"$/
   * @When /^I create a media of type "([^"]*)" named "([^"]*)"$/
   */
  public function iCreateAMediaOfType($type, $name = null) {
    if (!$name) {
      $name = $this->createRandomString();
    }
    $mediaData = [
      'bundle'               => $type,
      'field_title'          => $name,
      'field_include_search' => true,
    ];
    switch ($type) {
      case 'video':
        $mediaData += [
          'field_media_video_embed_field' => 'https://vimeo.com/191669818',
        ];
        break;
      case 'tweet':
        $mediaData += [
          'embed_code' => 'https://twitter.com/publicplan_GmbH/status/1024935629065469958',
        ];
        break;
      case 'instagram':
        $mediaData += [
          'embed_code' => 'https://www.instagram.com/p/JUvux9iFRY',
        ];
        break;
      //ToDo: Add all media types
      default:
        throw new \InvalidArgumentException(sprintf('The media type "%s" does not exist.', $type));
        break;
    }
    $media = Media::create($mediaData);
    $media->save();
    $this->trash[$media->getEntityTypeId()][] = $media->id();

    return $media;
  }

  /**
   * Creates a page with a specific media
   * Example: Given I created a content page named "videoPage" with a media "video"
   *
   * @Given /^(?:|I )created a content page named "([^"]*)" with a media "(address|audio|citation|contact|document|gallery|image|instagram|person|some_embed|tweet|video|video_upload)"$/
   */
  public function iCreatedPageWithMedia($pageName, $mediaType) {
    $media = $this->iCreateAMediaOfType($mediaType);

    $mediaParagraph = Paragraph::create([
      'type'                        => 'media_reference',
      'field_media_reference_media' => $media,
    ]);
    $mediaParagraph->save();
    $this->trash[$mediaParagraph->getEntityTypeId()][] = $mediaParagraph->id();

    $node = Node::create([
      'type'                     => 'normal_page',
      'title'                    => $pageName,
      'moderation_state'         => 'published',
      'field_content_paragraphs' => [$mediaParagraph],
    ]);
    $node->save();
    $this->trash[$node->getEntityTypeId()][] = $node->id();
  }

  /**
   * Deletes entities created during the scenario.
   *
   * @afterScenario
   */
  public function tearDown() {
    foreach ($this->trash as $entity_type => $IDs) {
      /** @var \Drupal\Core\Entity\EntityInterface[] $entities */
      $entities = \Drupal::entityTypeManager()
        ->getStorage($entity_type)
        ->loadMultiple($IDs);

      foreach ($entities as $entity) {
        $entity->delete();
      }
    }
  }

  /**
   * @Then header has CSS class for fluid bootstrap layout
   */
  public function headerHasCssClassForFluidBootstrapLayout() : ?bool {
    $header = $this->getSession()->getPage()->findAll('css', 'header.container-fluid');
    if (\count($header) > 0) {
      return true;
    } else {
      throw new ResponseTextException('Header does not have CSS class for fluid bootstrap layout.', $this->getSession());
    }
  }

  /**
   * @Then /^I assert "(\d+)" local task tabs$/
   */
  public function assertLocalTasksTabsNumber($number) {
    if (\count($this->getSession()->getPage()->findAll('css', ".block-local-tasks-block > nav > nav > ul > li:nth-child($number)")) > 0) {
      return true;
    }

    throw new ResponseTextException(
      sprintf('Could not find "%s" local task items', $number),
      $this->getSession()
    );
  }

  /**
   * @Then /^I should see text matching "([^"]*)" in "([^"]*)" selector "([^"]*)"$/
   *
   * Example:
   *  I should see text matching "Startseite Node" in "css" selector "ol.breadcrumb"
   */
  public function assertSelectorContainsText($text, $selectorType, $selector) {
    $resultset = $this->getSession()->getPage()->findAll($selectorType, $selector);
    if (!empty($resultset) && is_numeric(strpos($resultset['0']->getText(), $text))) {
      return TRUE;
    }
    throw new ResponseTextException(
      sprintf('Could not find text "%s" by selector type "%s" and selector "%s"', $text, $selectorType, $selector),
      $this->getSession()
    );
  }

}
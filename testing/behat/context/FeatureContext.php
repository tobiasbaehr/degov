<?php

namespace Drupal\degov\Behat\Context;

use Drupal\degov_theming\Factory\FilesystemFactory;
use Drupal\Driver\DrupalDriver;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Vocabulary;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Drupal\Core\File\FileSystem as DrupalFilesystem;

class FeatureContext extends ExtendedRawDrupalContext {

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
        'vid' => $vid,
      ]);
      $vocabulary->save();
    }
  }

  /**
   * @Given /^I create (\d+) nodes of type "([^"]*)"$/
   */
  public function iCreateNodesOfType($number, $type)
  {
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
  public function nodeAccessRecordsAreRebuild()
  {
    node_access_rebuild();
  }

  /**
   * @Then /^wait (\d+) seconds$/
   */
  public function waitSeconds($secondsNumber)
  {
    $this->getSession()->wait($secondsNumber * 1000);
  }

  /**
   * @Then /^I select index (\d+) in dropdown named "([^"]*)"$/
   */
  public function selectIndexInDropdown($index, $name)
  {
    $this->getSession()->evaluateScript('document.getElementsByName("' . $name . '")[0].selectedIndex = ' . $index . ';');
  }

  /**
   * @Then /^I open node edit form by node title "([^"]*)"$/
   * @param string $title
   */
  public function openNodeEditFormByTitle($title)
  {
    $query = \Drupal::service('database')->select('node_field_data', 'nfd')
      ->fields('nfd', ['nid'])
      ->condition('nfd.title', $title);

    $this->visitPath('/node/' . $query->execute()->fetchField() . '/edit');
  }

  /**
   * @Then /^I open node view by node title "([^"]*)"$/
   * @param string $title
   */
  public function openNodeViewByTitle($title)
  {
    $query = \Drupal::service('database')->select('node_field_data', 'nfd')
      ->fields('nfd', ['nid'])
      ->condition('nfd.title', $title);

    $this->visitPath('/node/' . $query->execute()->fetchField());
  }

  /**
   * @Then /^I scroll to element with id "([^"]*)"$/
   * @param string $id
   */
  public function iScrollToElementWithId($id)
  {
    $this->getSession()->executeScript(
      "
                var element = document.getElementById('" . $id . "');
                element.scrollIntoView( true );
            "
    );
  }

  /**
   * @Then /^I check checkbox with id "([^"]*)" by JavaScript$/
   * @param string $id
   */
  public function checkCheckboxWithJS($id)
  {
    $this->getSession()->executeScript(
      "
                document.getElementById('" . $id . "').checked = true;
            "
    );
  }

  /**
   * @Then /^I check checkbox with id "([^"]*)"$/
   * @param string $id
   */
  public function checkCheckbox($id)
  {
    $page          = $this->getSession()->getPage();
    $selectElement = $page->find('xpath', '//input[@id = "' . $id . '"]');

    $selectElement->check();
  }

  /**
   * @Then /^I uncheck checkbox with id "([^"]*)"$/
   * @param string $id
   */
  public function uncheckCheckbox($id)
  {
    $page          = $this->getSession()->getPage();
    $selectElement = $page->find('xpath', '//input[@id = "' . $id . '"]');

    $selectElement->uncheck();
  }

  /**
   * @Then /^I select "([^"]*)" in "([^"]*)"$/
   */
  public function selectOption($label, $id)
  {
    $page          = $this->getSession()->getPage();
    $selectElement = $page->find('xpath', '//select[@id = "' . $id . '"]');
    $selectElement->selectOption($label);
  }

  /**
   * @Then /^I click by CSS class "([^"]*)"$/
   * @param string $class
   */
  public function clickByCSSClass($class)
  {
    $page   = $this->getSession()->getPage();
    $button = $page->find('xpath', '//input[contains(@class, ' . $class . ')]');
    $button->click();
  }

  /**
   * @Then /^I click by XPath "([^"]*)"$/
   * @param string $xpath
   */
  public function iClickByXpath($xpath)
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
  public function iProofCheckboxWithIdHasValue($id,$checkfor)
  {
    $Page = $this->getSession()->getPage();
    $isChecked = $Page->find('css', 'input[type="checkbox"]:checked#' . $id);
    $status = ($isChecked) ? "checked" : "unchecked";
    if(
      ($checkfor == "checked" && $isChecked == true)
      ||
      ($checkfor == "unchecked" && $isChecked == false)
    ){
      return true;
    }else{
      throw new \Exception('Checkbox was '.$status.' when expecting '.$checkfor);
      return false;
    }
  }

  /**
   * @Then /^I proof css selector "([^"]*)" has attribute "([^"]*)" with value "([^"]*)"$/
   */
  public function cssSelectorAttributeMatchesValue($selector, $attribute, $value)
  {
    if ($this->getSession()->evaluateScript("jQuery('$selector').css('$attribute')") == $value) {
      return TRUE;
    } else {
      throw new \Exception("CSS selector $selector does not match attribute '$attribute' with value '$value'");
    }
  }

  /**
   * @Then /^I am installing the "([^"]*)" module$/
   */
  public function iAmInstallingTheModule(string $moduleName) {
    \Drupal::service('module_installer')->install([$moduleName]);
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
   * @Given /^I should not see the option "([^"]*)" in "([^"]*)"$/
   * @param $value
   * @param $id
   *
   * @throws \Exception
   */
  public function iShouldNotSeeTheOptionIn($value, $id) {
    $page = $this->getSession()->getPage();
    /** @var $selectElement \Behat\Mink\Element\NodeElement */
    $selectElement = $page->find('xpath', '//select[@id = "' . $id . '"]');
    $element = $selectElement->find('css', 'option[value=' . $value . ']');
    if ($element) {
      throw new \Exception("There is an option with the value '$value' in the select '$id'");
    }
  }

  /**
   * @Given /^I submit a form by id "([^"]*)"$/
   */
  public function iSubmitAFormById($Id) {
    $page = $this->getSession()->getPage();
    $element = $page->find('css',"form#${Id}");
    $element->submit();
  }

  /**
   * @Then /^I scroll to bottom$/
   */
  public function iScrollToBottom(): void {
    $this->getSession()
      ->executeScript('window.scrollTo(0,document.body.scrollHeight);');
  }

  /**
   * @Then /^I scroll to top$/
   */
  public function iScrollToTop(): void {
    $this->getSession()
      ->executeScript('window.scrollTo(0,0);');
  }

  /**
   * @Given /^I click with jquery selector "([^"]*)"$/
   */
  public function IClickWithJQuerySelector(string $selector): void {
    $this->getSession()->executeScript('jQuery("'.$selector.'").click();');
  }

}

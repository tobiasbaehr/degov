<?php

namespace Drupal\degov\Behat\Context;

use Behat\Mink\Exception\ResponseTextException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\degov\Behat\Context\Traits\TranslationTrait;
use Drupal\degov_demo_content\Generator\MediaGenerator;
use Drupal\degov_demo_content\Generator\MenuItemGenerator;
use Drupal\degov_demo_content\Generator\NodeGenerator;
use Drupal\degov_theming\Factory\FilesystemFactory;
use Drupal\Driver\DrupalDriver;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\permissions_by_term\Service\AccessStorage;
use Drupal\permissions_by_term\Service\TermHandler;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Drupal\Core\File\FileSystem as DrupalFilesystem;
use WebDriver\Exception\StaleElementReference;

class DrupalContext extends RawDrupalContext {

	use TranslationTrait;

  private const MAX_DURATION_SECONDS = 1200;

  /** @var array */
  protected $trash = [];

  /**
   * @var null|int
   */
  private $dummyImageFileEntityId = null;

  /**
   * @var null|int
   */
  private $dummyDocumentFileEntityId = null;

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
   * @Then /^I open media edit form by media name "([^"]*)"$/
   * @param string $name
   */
  public function openMediaEditFormByName(string $name) {
    $query = \Drupal::service('database')->select('media_field_data', 'mfd')
      ->fields('mfd', ['mid'])
      ->condition('mfd.name', $name);

    $this->visitPath('/media/' . $query->execute()->fetchField() . '/edit');
  }

  /**
   * @Then /^I open node view by node title "([^"]*)"$/
   * @param string $title
   */
  public function openNodeViewByTitle(string $title): void {
    $query = \Drupal::service('database')->select('node_field_data', 'nfd')
      ->fields('nfd', ['nid'])
      ->condition('nfd.title', $title);

    $this->visitPath('/node/' . $query->execute()->fetchField());
  }

  /**
   * @Then /^I open address medias edit form from latest media with title "([^"]*)"$/
   */
  public function openMediaEditFormByTitle(string $title): void {
    /**
     * @var EntityTypeManagerInterface $entityTypeManager
     */
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $mediaEntityStorage = $entityTypeManager->getStorage('media');

    $mediaEntities = $mediaEntityStorage->loadByProperties([
      'field_title' => $title,
      'bundle'      => 'address',
    ]);

    $mediaEntity = \end($mediaEntities);

    if (!$mediaEntity instanceof Media) {
      throw new \Exception('Could not retrieve media entity by provided title.');
    }

    $this->visitPath('/media/' . $mediaEntity->id() . '/edit');
  }

  /**
   * @Then /^I open medias delete url by title "([^"]*)"$/
   */
  public function openMediaDeleteUrlByTitle(string $title): void {
    /**
     * @var EntityTypeManagerInterface $entityTypeManager
     */
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $mediaEntityStorage = $entityTypeManager->getStorage('media');

    $mediaEntities = $mediaEntityStorage->loadByProperties([
      'field_title' => $title,
    ]);

    $mediaEntity = \end($mediaEntities);

    if (!$mediaEntity instanceof Media) {
      throw new \Exception('Could not retrieve media entity by provided title.');
    }

    $this->visitPath('/media/' . $mediaEntity->id() . '/delete');
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
   * @Then /^I proof Checkbox with id "([^"]*)" has value "([^"]*)"$/
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
    $this->iShouldSeeTheOptionInWithStatus($value, $id);
  }

  /**
   * @Given /^I should see the option "([^"]*)" in "([^"]*)" with status "([^"]*)"$/
   */
  public function iShouldSeeTheOptionInWithStatus(string $value, string $id, string $status = null) {
    $page = $this->getSession()->getPage();
    /** @var $selectElement \Behat\Mink\Element\NodeElement */
    $selectElement = $page->find('xpath', '//select[@id = "' . $id . '"]');
    switch($status) {
      case 'enabled':
        $element = $selectElement->find('css', 'option[value=' . $value . ']:not([disabled])');
        break;
      case 'disabled':
        $element = $selectElement->find('css', 'option[value=' . $value . '][disabled]');
        break;
      default:
        $element = $selectElement->find('css', 'option[value=' . $value . ']');
    }
    if (!$element) {
      throw new \Exception("There is no option with the value '$value'" . ($status !== null ? " and status '" . $status . "'" : '') . " in the select '$id'");
    }
  }

  /**
   * @Then I should see an :arg1 element with the translated :arg2 attribute :arg3
   */
  public function iShouldSeeAnElementWithTheTranslatedAttribute(string $selector, string $attribute_name, string $attribute_value) {
    $this->assertSession()->elementExists(
      'css',
      sprintf(
        "%s[%s=%s]",
        $selector,
        $attribute_name,
        $this->translateString($attribute_value)
      )
    );
  }

  /**
   * @Given /^I have an normal_page with a slideshow paragraph reference$/
   */
  public function createNormalPageWithSlideshow(): void {
    $media = Media::create([
      'bundle'              => 'image',
      'field_title'         => 'Some image',
      'field_copyright'     => 'Some copyright',
      'field_image_caption' => 'Some image caption',
      'image'               => $this->createDummyImageFileEntity()->id(),
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
   * @Given /^I have an normal_page with a banner paragraph$/
   */
  public function createNormalPageWithBanner(): void {
    $copyrightTerm = Term::create([
      'name' => 'Some copyright',
      'vid'  => 'copyright',
    ]);

    $media = Media::create([
      'bundle'              => 'image',
      'field_title'         => 'Some image',
      'field_copyright'     => $copyrightTerm,
      'field_image_caption' => 'Some image caption',
      'image'               => $this->createDummyImageFileEntity()->id(),
    ]);
    $media->save();

    $paragraphSlideshow = Paragraph::create([
      'type'                   => 'image_header',
      'field_override_caption' => '',
      'field_header_media'     => $media,
    ]);
    $paragraphSlideshow->save();

    $node = Node::create([
      'title'                   => 'An normal page with a banner',
      'type'                    => 'normal_page',
      'moderation_state'        => 'published',
      'field_header_paragraphs' => [$paragraphSlideshow],
    ]);
    $node->save();
  }

  /**
   * @Given /^I have a restricted document media entity$/
   */
  public function createRestrictedDocument(): void {
    /**
     * @var TermHandler $termHandler
     */
    $termHandler = \Drupal::service('permissions_by_term.term_handler');
    if (empty($termHandler->getTermIdByName('Admin role only - restricted media document'))) {
      $term = Term::create([
        'name' => 'Admin role only - restricted media document',
        'vid' => 'section',
      ]);
      $term->save();

      $termId = $term->id();

      /**
       * @var AccessStorage $accessStorage
       */
      $accessStorage = \Drupal::service('permissions_by_term.access_storage');
      $accessStorage->addTermPermissionsByRoleIds(['administrator'], $termId);

      Media::create([
        'title'          => 'Restricted Word document',
        'field_title'    => 'Restricted Word document',
        'bundle'         => 'document',
        'field_section'  => [
          [
            'target_id' => $termId,
          ],
        ],
        'field_document' => [
          [
            'target_id' => $this->createPrivateDocumentFileEntity()->id(),
          ],
        ],
      ])->save();
    }

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
    self::iCreatedTypePageWithMedia('normal_page', $pageName, $mediaType);
  }

  /**
   * Creates a page with given entity type and with a specific media
   * Example: Given I created a content page of type "event" named "videoPage" with a media "video"
   *
   * @Given /^(?:|I )created a content page of type "([^"]*)" named "([^"]*)" with a media "(address|audio|citation|contact|document|gallery|image|instagram|person|some_embed|tweet|video|video_upload)"$/
   */
  public function iCreatedTypePageWithMedia($entityType, $pageName, $mediaType) {
    $media = $this->iCreateAMediaOfType($mediaType);

    $mediaParagraph = Paragraph::create([
      'type'                        => 'media_reference',
      'field_media_reference_media' => $media,
    ]);
    $mediaParagraph->save();
    $this->trash[$mediaParagraph->getEntityTypeId()][] = $mediaParagraph->id();

    $node = Node::create([
      'type'                     => $entityType,
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

  /**
   * @Then /^I should not see text matching "([^"]*)" via translated text in "([^"]*)" selector "([^"]*)"$/
   *
   * Example:
   *  I should not see text matching "Homepage node" via translated in "css" selector "ol.breadcrumb"
   */
  public function assertSelectorNotContainsTranslatedText($text, $selectorType, $selector) {
    $resultset = $this->getSession()->getPage()->findAll($selectorType, $selector);
    $translatedText = $this->translateString($text);
    $isFound = FALSE;
    if (!empty($resultset)) {
      foreach($resultset as $resultRow) {
        if (is_numeric(stripos($resultRow->getText(), $translatedText))) {
          $isFound = TRUE;
          break;
        }
      }
    }
    if (!$isFound) {
      return TRUE;
    }
    throw new ResponseTextException(
      sprintf('Found the text "%s" by selector type "%s" and selector "%s"', $translatedText, $selectorType, $selector),
      $this->getSession()
    );
  }

  /**
   * @Given /^I run the cron$/
   */
  public function iRunTheCron() {
    if (TRUE !== \Drupal::service('cron')->run()) {
      throw new \Exception('Cron did not run successfully.');
    }
  }

	/**
	 * @Then /^I should see text matching "([^"]*)" via translated text$/
	 */
	public function assertPageMatchesText(string $text)
	{
		if (ctype_upper($text)) {
			$translatedText = mb_strtoupper($this->translateString($text));
		} else {
			$translatedText = $this->translateString($text);
		}

		$this->assertSession()->pageTextMatches('"' . $translatedText . '"');
	}

  /**
   * @Then /^I should not see text matching "([^"]*)" via translated text$/
   */
  public function assertPageNotMatchesText(string $text)
  {
    if (ctype_upper($text)) {
      $translatedText = mb_strtoupper($this->translateString($text));
    } else {
      $translatedText = $this->translateString($text);
    }

    $content = $this->getSession()->getPage()->getText();
    if (substr_count($content, $translatedText) === 0) {
      return true;
    } else {
      throw new \Exception("Text '$translatedText' found on page.");
    }
  }


  /**
	 * @Then /^I should see text matching "([^"]*)" via translated text in uppercase$/
	 */
	public function assertPageMatchesTextUppercase(string $text)
	{
		$this->assertSession()->pageTextMatches('"' . mb_strtoupper($this->translateString($text)) . '"');
	}

  /**
   * @Then /^I should not see text matching "([^"]*)" via translated text in uppercase$/
   */
  public function assertPageNotMatchesTextUppercase(string $text)
  {
    $this->assertSession()->pageTextNotMatches('"' . mb_strtoupper($this->translateString($text)) . '"');
  }

	/**
	 * @Then /^I should see text matching "([^"]*)" via translation after a while$/
	 */
	public function iShouldSeeTranslatedTextAfterAWhile(string $text): bool
	{
		try {
			$startTime = time();
			do {
				$content = $this->getSession()->getPage()->getText();
				$translatedText = $this->translateString($text);
				if (substr_count($content, $translatedText) > 0) {
					return true;
				}
			} while (time() - $startTime < self::MAX_DURATION_SECONDS);
			throw new ResponseTextException(
				sprintf('Could not find text %s after %s seconds', $translatedText, self::MAX_DURATION_SECONDS),
				$this->getSession()
			);
		} catch (StaleElementReference $e) {
			return true;
		}
	}

	/**
	 * @When I click :link via translation
	 */
	public function assertClickViaTranslate(string $link): void {
		$this->getSession()->getPage()->clickLink($this->translateString($link));
	}

  /**
   * @Then I should see the fields list with exactly :arg1 entries
   */
  public function iShouldSeeTheFieldsListWithExactlyEntries($numberOfEntries)
  {
    $this->iShouldSeeTheElementWithTheSelectorXWithExactlyNInstances(2, "table#field-overview tbody > tr");
  }


  /**
   * @Then I should see exactly :arg1 instances of the element with the selector :arg2
   */
  public function iShouldSeeTheElementWithTheSelectorXWithExactlyNInstances($numberOfInstances, $elementSelector)
  {
    $this->assertSession()->elementExists('css', $elementSelector);
    $this->assertSession()->elementsCount('css', $elementSelector, $numberOfInstances);
  }

  /**
   * @Given I have dismissed the cookie banner if necessary
   */
  public function iHaveDismissedTheCookieBannerIfNecessary()
  {

    $this->getSession()->visit($this->locatePath('/'));
    if($this->getSession()->getPage()->has('css', '.eu-cookie-compliance-buttons .agree-button')) {
      $this->getSession()->getPage()->find('css', '.eu-cookie-compliance-buttons .agree-button')->click();
    }
  }

  /**
   * @Given I should see the details container titled :arg1 with entries after a while
   */
  public function iShouldSeeTheDetailsContainerTitledWithEntriesAfterAWhile($title)
  {
    $title = mb_strtoupper($title);

    try {
      $startTime = time();
      do {
        $details_array = $this->getSession()->getPage()->findAll('css', 'details');

        foreach($details_array as $details_element) {
          if($details_element->find('css', 'summary')->getText() != $title) {
            continue;
          }
          if($details_element->has('css', '.item-container')) {
            return true;
          }
        }
      } while (time() - $startTime < self::MAX_DURATION_SECONDS);
      throw new ResponseTextException(
        sprintf('Could not find element titled %s with entries within %s seconds.', $title, self::MAX_DURATION_SECONDS),
        $this->getSession()
      );
    } catch (StaleElementReference $e) {
      return true;
    }
  }

  /**
   * @Then I should see :number_of_elements form element with the label :label and a required input field
   */
  public function iShouldSeeAFormElementWithTheLabelAndARequiredInputField(int $number_of_elements, string $label_text)
  {
    $matching_elements_count = $this->countFormElementsWithLabelMatchingSelector($label_text, 'css', '.required');
    if($number_of_elements === $matching_elements_count) {
      return true;
    }
    throw new \Exception(sprintf('Expected %s elements, found %s.', $number_of_elements, $matching_elements_count));
  }

  /**
   * @Then I should see :number_of_elements form element with the label :arg2 and a :arg3 field
   */
  public function iShouldSeeAFormElementWithTheLabelAndAField(int $number_of_elements, string $label_text, string $input_type)
  {
    $matching_elements_count = $this->countFormElementsWithLabelMatchingSelector($label_text, 'xpath', sprintf('//input[@type="%s"]', $input_type));
    if($number_of_elements === $matching_elements_count) {
      return true;
    }
    throw new \Exception(sprintf('Expected %s elements, found %s.', $number_of_elements, $matching_elements_count));
  }

  /**
   * @Then I should see :number_of_elements form element with the label :label and the value :field_value
   */
  public function iShouldSeeAFormElementWithTheLabelAndTheValue(int $number_of_elements, string $label_text, string $field_value)
  {
    $selector_value = '//*[@value="' . $field_value . '"]';
    if(empty($field_value)) {
      $selector_value = '//*[@value and string-length(@value) = 0]';
    }
    $matching_elements_count = $this->countFormElementsWithLabelMatchingSelector($label_text, 'xpath', $selector_value);
    if($number_of_elements === $matching_elements_count) {
      return true;
    }
    throw new \Exception(sprintf('Expected %s elements, found %s.', $number_of_elements, $matching_elements_count));
  }

  /**
   * @Then I should see :number_of_elements elements with name matching :name_pattern and a not empty value
   */
  public function iShouldSeeElementsWithNameMatchingPatternAndANotEmptyValue(int $number_of_elements, string $name_pattern)
  {
    $selector_value = '//*[contains(@name, "' . $name_pattern . '") and @value and string-length(@value) > 0]';
    $matches = $this->getSession()->getPage()->findAll('xpath', $selector_value);
    $matching_elements_count = count($matches);
    if($number_of_elements === $matching_elements_count) {
      return true;
    }
    throw new \Exception(sprintf('Expected %s elements, found %s.', $number_of_elements, $matching_elements_count));
  }

  private function countFormElementsWithLabelMatchingSelector(string $label_text, string $selector_type, string $selector_value): int {
    // Get all form items with labels matching the supplied text.
    $form_items_with_matching_labels = $this->getElementWithClassContainingLabelWithText('form-item', $label_text);

    $matching_elements_count = 0;
    foreach($form_items_with_matching_labels as $form_item) {
      if(count($form_item->findAll($selector_type, $selector_value)) > 0) {
        $matching_elements_count++;
      }
    }

    return $matching_elements_count;
  }

  private function getElementWithClassContainingLabelWithText($class_name, $label_text) {
    return $this->getSession()->getPage()->findAll(
      'xpath',
      sprintf('//label[contains(text(), "%s")]/ancestor::*[contains(@class, "%s")]', $label_text, $class_name)
    );
  }

  private function createPrivateDocumentFileEntity(): File {
    $documentFileEntity = null;

    if (is_numeric($this->dummyDocumentFileEntityId)) {
      /**
       * @var File $documentFileEntity
       */
      $documentFileEntity = File::load($this->dummyDocumentFileEntityId);
    }

    if (!($documentFileEntity instanceof File)) {
      $documentFileEntity = $this->createFileEntity('word-document.docx', 'private');
      $this->dummyDocumentFileEntityId = $documentFileEntity->id();
    }

    return $documentFileEntity;
  }

  private function createDummyImageFileEntity(): File {
    $imageFileEntity = null;

    if (is_numeric($this->dummyImageFileEntityId)) {
      /**
       * @var File $imageFileEntity
       */
      $imageFileEntity = File::load($this->dummyImageFileEntityId);
    }

    if (!($imageFileEntity instanceof File)) {
      $imageFileEntity = $this->createFileEntity('vladimir-riabinin-1058013-unsplash.jpg');
      $this->dummyImageFileEntityId = $imageFileEntity->id();
    }

    return $imageFileEntity;
  }

  private function createFileEntity(string $filename, string $fileSchemeMode = 'public'): File {
    $fileEntity = null;

    if (!($fileEntity instanceof File)) {
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

      if ($fileSchemeMode === 'public') {
        $drupalFilePath = 'public://';

        $symfonyFilesystem->copy(
          drupal_get_path('module', 'degov_demo_content') . '/fixtures/' . $filename,
          $drupalFilesystem->realpath($drupalFilePath . '/' . $filename)
        );
      } else {
        $drupalFilePath = 'private://media/document/file';

        $documentFilesUri = $drupalFilesystem->realpath('private://') . '/media/document/file';

        if (!$symfonyFilesystem->exists($documentFilesUri)) {
          $symfonyFilesystem->mkdir($documentFilesUri);
        }

        $symfonyFilesystem->copy(
          drupal_get_path('module', 'degov_demo_content') . '/fixtures/' . $filename,
          $documentFilesUri . '/' . $filename
        );
      }

      $fileEntity = File::create([
        'uid'      => 1,
        'filename' => $filename,
        'uri'      => $drupalFilePath . '/' . $filename,
        'status'   => 1,
      ]);
      $fileEntity->save();

      $this->dummyImageFileEntityId = $fileEntity->id();
    }

    return $fileEntity;
  }

  /**
   * @Then /^I have created an node normal page entity with a content reference in "([^"]*)" view mode$/
   */
  public function createNormalPageEntityWithContentReferenceInViewMode(string $viewMode): void {
    $media = Media::create([
      'bundle'              => 'image',
      'field_title'         => 'Some image',
      'field_copyright'     => 'Some copyright',
      'field_image_caption' => 'Some image caption',
      'image'               => $this->createDummyImageFileEntity()->id(),
    ]);
    $media->save();

    $nodeForContentReference = Node::create([
      'title'                   => 'Some node for reference',
      'type'                    => 'normal_page',
      'moderation_state'        => 'published',
      'field_teaser_text'       => 'My nice teaser text.',
      'field_teaser_image'      => [
        [
          'target_id' => $media->id()
        ],
      ]
    ]);
    $nodeForContentReference->save();

    $contentReferenceParagraph = Paragraph::create([
      'type'                          => 'node_reference',
      'field_node_reference_nodes'    => [
        [
          'target_id' => $nodeForContentReference->id()
        ],
      ],
      'field_node_reference_viewmode' => $viewMode,
    ]);
    $contentReferenceParagraph->save();

    $node = Node::create([
      'title'                    => 'An normal page with a content reference',
      'type'                     => 'normal_page',
      'moderation_state'         => 'published',
      'field_content_paragraphs' => [
        $contentReferenceParagraph
      ],
    ]);
    $node->save();
  }

  /**
   * @Then /^I have created an unused file entity$/
   */
  public function iHaveCreatedAnUnusedFileEntity() {
    $this->createDummyImageFileEntity();
  }

  /**
   * @Then /^I visit the delete form for the unused file entity$/
   */
  public function iVisitTheDeleteFormForTheUnusedFileEntity() {
    if(preg_match("/^\d+$/", $this->dummyImageFileEntityId)) {
      $this->getSession()->visit($this->locatePath('/file/' . $this->dummyImageFileEntityId . '/delete'));
    }
  }

  /**
   * @Then /^I visit an normal page entity with content reference$/
   */
  public function visitNormalPageEntityWithContentReference(): void {
    $this->openNodeViewByTitle('An normal page with a content reference');
  }

  /**
   * @Given I set the privacy policy page for all languages
   */
  public function setThePrivacyPolicyPageForAllLanguages() {
    $degov_simplenews_settings = \Drupal::service('config.factory')
      ->getEditable('degov_simplenews.settings');
    $all_languages = \Drupal::service('language_manager')->getLanguages();
    $page_with_all_teasers_nid = \Drupal::entityQuery('node')
      ->execute();
    if (!empty($page_with_all_teasers_nid)) {
      $page_with_all_teasers_nid = reset($page_with_all_teasers_nid);

      $privacy_policies = [];
      foreach ($all_languages as $language) {
        $privacy_policies[$language->getId()] = $page_with_all_teasers_nid;
      }
      $degov_simplenews_settings->set('privacy_policy', $privacy_policies)
        ->save();
    }
  }

  /**
   * @Then I should see an :selector element with the content :content
   */
  public function iShouldSeeAnElementWithTheContent($selector, $content) {
    $elements = $this->getSession()->getPage()->findAll('css', $selector);

    if (!empty($elements)) {
      foreach ($elements as $element) {
        if ($element->getHtml() === $content) {
          return TRUE;
        }
      }

      throw new \Exception(sprintf('Could not find any elements matching "%s" with the content "%s"', $selector, $content));
    }

    throw new \Exception(sprintf('Could not find any elements matching "%s"', $selector));
  }

  /**
   * @Then I should see an :selector element with the content :content via translation
   */
  public function iShouldSeeAnElementWithTheContentViaTranslation($selector, $content) {
    $elements = $this->getSession()->getPage()->findAll('css', $selector);
    $translatedContent = $this->translateString($content);

    if (!empty($elements)) {
      foreach ($elements as $element) {
        if ($element->getHtml() === $translatedContent) {
          return TRUE;
        }
      }

      throw new \Exception(sprintf('Could not find any elements matching "%s" with the content "%s"', $selector, $translatedContent));
    }

    throw new \Exception(sprintf('Could not find any elements matching "%s"', $selector));
  }

  /**
   * @Given /^I rebuild the "([^"]*)" index$/
   */
  public function iRebuildTheIndex($indexId) {
    $index_storage = \Drupal::entityTypeManager()
      ->getStorage('search_api_index');
    /** @var \Drupal\search_api\IndexInterface $index */
    $index = $index_storage->load($indexId);
    $index->reindex();
    $index->indexItems();
  }

  /**
   * @Given /^I clear the cache$/
   */
  public function iClearTheCache() {
    drupal_flush_all_caches();
  }

  /**
   * @Given /^I reset the demo content$/
   */
  public function resetDemoContent() {
    /** @var \Drupal\degov_demo_content\Generator\MediaGenerator $mediaGenerator */
    $mediaGenerator = \Drupal::service('degov_demo_content.media_generator');
    $mediaGenerator->resetContent();

    /** @var \Drupal\degov_demo_content\Generator\NodeGenerator $nodeGenerator */
    $nodeGenerator = \Drupal::service('degov_demo_content.node_generator');
    $nodeGenerator->resetContent();

    /** @var \Drupal\degov_demo_content\Generator\MenuItemGenerator $menuItemGenerator */
    $menuItemGenerator = \Drupal::service('degov_demo_content.menu_item_generator');
    $menuItemGenerator->resetContent();

    /** @var \Drupal\degov_demo_content\Generator\BlockContentGenerator $blockContentGenerator */
    $blockContentGenerator = \Drupal::service('degov_demo_content.block_content_generator');
    $blockContentGenerator->resetContent();
  }

  /**
   * @Given /^I should see the "([^"]*)" in "([^"]*)"$/
   */
  public function iShouldSeeTheImageIn($selector1, $selector2) {
    $elements = $this->getSession()->getPage()->findAll('css', $selector2);

    if (!empty($elements)) {
      foreach ($elements as $element) {
        if (!$element->has('css', $selector1)) {
          throw new \Exception(sprintf('Could not find "%s" element within "%s" element(s)' . "\r\n" . $element->getHtml(), $selector1, $selector2));
        }

      }
    }
    else {
      throw new \Exception(sprintf('Could not find any elements matching "%s"', $selector2));
    }
    return TRUE;
  }

  /**
   * @Then I set newsletter privacy policy page
   */
  public function setNewsletterPrivacyPolicyPage() {
    \Drupal::configFactory()
      ->getEditable('degov_simplenews.settings')
      ->set('privacy_policy', ['de' => '1'])
      ->save();
  }

  /**
   * @Given I should not see the element with css selector :selector
   */
  public function iShouldNotSeeTheElementWithCssSelector($selector) {
    $elements = $this->getSession()->getPage()->findAll('css', $selector);
    foreach ($elements as $element) {
      if ($element->isVisible()) {
        throw new \Exception("The element with selector \"$selector\" is visible.");
      }
    }
  }

  /**
   * @Given I should see the element with css selector :selector
   */
  public function iShouldSeeTheElementWithCssSelector($selector) {
    $elements = $this->getSession()->getPage()->findAll('css', $selector);
    foreach ($elements as $element) {
      if (!$element->isVisible()) {
        throw new \Exception("The element with selector \"$selector\" is not visible.");
      }
    }
  }

  /**
   * @Then I fill in the autocomplete :autocomplete with :label via javascript
   */
  public function fillInDrupalAutocomplete($autocomplete, string $text) {
    try {
      $this->getSession()->evaluateScript(sprintf("jQuery('%s').val('%s').trigger('keydown');", $autocomplete, $text));
      $startTime = time();
      do {
        $page = $this->getSession()->getPage();
        $node = $page->find('css', '.ui-menu li a');
        if ($node) {
          // Fixed selector usage, can be swapped by a selector in case necessary later on.
          $this->getSession()->evaluateScript("jQuery('.ui-menu li a').click();");
          return true;
        }
      } while (time() - $startTime < self::MAX_DURATION_SECONDS);
      throw new ResponseTextException(
        sprintf('Could not find autocomplete after %s seconds',  self::MAX_DURATION_SECONDS),
        $this->getSession()
      );
    } catch (StaleElementReference $e) {
      return false;
    }
  }

  /**
   * @Then each HTML content element with css selector :selector is unique
   */
  public function eachHtmlContentElementWithCssSelectorIsUnique($selector) {
    $elements = $this->getSession()->getPage()->findAll('css', $selector);
    $elementText = '';
    $duplicate = FALSE;
    $values = [];
    foreach ($elements as $element) {
      $elementText = $element->getText();
      if (isset($values[$elementText])) {
        $duplicate = TRUE;
        break;
      }
      $values[$elementText] = $elementText;
    }
    if ($duplicate) {
      throw new \Exception(sprintf('Found duplicate HTML content "%s" elements with CSS selector "%s"', $elementText, $selector));
    }
  }

  /**
   * Counts the amount of open windows.
   * @Then there should be a total of :number window(s)
   */
  public function thereShouldBeATotalOfWindow($total) {
    $totalWindows = count($this->getSession()->getWindowNames());
    if ($totalWindows == $total) {
      return TRUE;
    }
    throw new \Exception($totalWindows . ' windows are found on the page, but should be ' . $total);
  }
}

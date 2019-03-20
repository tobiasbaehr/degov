@api @drupal
Feature: deGov - Content creation

  Background:
    Given I am installing the following Drupal modules:
      | degov_demo_content          |
    Given I proof that the following Drupal modules are installed:
      | degov_node_press            |
      | degov_node_event            |
      | degov_node_blog             |
      | degov_node_normal_page      |
      | degov_simplenews_references |
      | media_file_links            |

  Scenario: I create a press entity and check that the header section is being displayed as expected
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/press"
    And I fill in "Titel" with "Test1234"
    And I fill in Textarea with "Test1234"
    And I fill in "edit-field-press-date-0-value-date" with "01012018"
    And I scroll to bottom
    And I press button with label "Save" via translated text
    Then I should see HTML content matching "01.01.2018" after a while
    And I should see "Test1234" in the ".press__header-paragraphs h2" element
    And I should see "Test1234" in the ".press__header-paragraphs .press__teaser-text" element

  Scenario: I see all form fields in normal_page content type
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page"
    And I should see "Titel"
    And I should see "Interner Titel"
    And I should see "Vorschau Titel"
    And I should see "Vorschau Untertitel"
    And I should see "Vorschau Text"
    And I choose "Allgemein" from tab menu
    And I should see "Schlagworte"
    And I should see "Sprache"
    And I should see "Thema"
    And I should see "Inhaltstyp"
    And I should see "Bereich"
    And I should see "Ansichtssteuerung"
    And I choose "Header" via translation from tab menu
    And I should see "KOPFBEREICH"
    And I should see HTML content matching "Paragraph hinzufügen"
    And I choose "Seitenleiste rechts" from tab menu
    And I should see "Seitenleiste rechts"
    And I should see HTML content matching "Paragraph hinzufügen"
    And I choose "Content" via translation from tab menu
    And I should see "INHALTSBEREICH"
    And I should see HTML content matching "Paragraph hinzufügen"

  Scenario: I see all form fields in blog content type
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/blog"
    And I should see "Titel"
    And I should see "Interner Titel"
    And I should see "Untertitel"
    And I should see "Datum"
    And I should see "Vorschau Titel"
    And I should see "Vorschau Untertitel"
    And I should see "Vorschau Text"
    And I choose "Allgemein" from tab menu
    And I should see "Schlagworte"
    And I should see "Social Media Buttons anzeigen"
    And I choose "Header" via translation from tab menu
    And I should see "KOPFBEREICH"
    And I should see HTML content matching "Paragraph hinzufügen"
    And I choose "Seitenleiste rechts" from tab menu
    And I should see "Seitenleiste rechts"
    And I should see HTML content matching "Paragraph hinzufügen"
    And I choose "Content" via translation from tab menu
    And I should see "INHALTSBEREICH"
    And I should see HTML content matching "Paragraph hinzufügen"

  Scenario: I see all form fields in faq content type
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/faq"
    And I should see "Titel"
    And I should see "Interner Titel"
    And I choose "Description" via translation from tab menu
    And I should see "Beschreibung"
    And I choose "Content" via translation from tab menu
    And I should see "INHALTSBEREICH"
    And I should see HTML content matching "Paragraph hinzufügen"
    And I should see "VERWANDTE FAQ"
    And I choose "Allgemein" from tab menu
    And I should see "Bereich"

  Scenario: I see all form fields in newsletter content type
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/simplenews_issue"
    And I should see "Titel"
    And I should see "Textkörper"
    And I should see "Newsletter"

  Scenario: I see all form fields in press content type
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/press"
    And I should see "Titel"
    And I should see "Interner Titel"
    And I should see "Datum"
    And I should see "Vorschau Titel"
    And I should see "Vorschau Untertitel"
    And I should see "Vorschau Text"
    And I choose "Allgemein" from tab menu
    And I should see "Schlagworte"
    And I should see "Social Media Buttons anzeigen"
    And I should see "Sprache"
    And I should see "Thema"
    And I should see "Inhaltstyp"
    And I should see "Bereich"
    And I should see "Ansichtssteuerung"
    And I choose "Header" via translation from tab menu
    And I should see "KOPFBEREICH"
    And I should see HTML content matching "Paragraph hinzufügen"
    And I choose "Seitenleiste rechts" from tab menu
    And I should see "Seitenleiste rechts"
    And I should see HTML content matching "Paragraph hinzufügen"
    And I choose "Content" via translation from tab menu
    And I should see "INHALTSBEREICH"
    And I should see HTML content matching "Paragraph hinzufügen"

  Scenario: I see all form fields in event content type
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/event"
    And I should see "Titel"
    And I should see "Interner Titel"
    And I should see "DATUM"
    And I should see "ENDDATUM"
    And I should see "Anzeigezeit"
    And I should see "ADRESSE"
    And I should see "Land"
    And I should see "Firma"
    And I should see text matching "Postal code" via translated text
    And I should see text matching "City" via translated text
    And I should see "Vorschau Titel"
    And I should see "Vorschau Untertitel"
    And I should see "Vorschau Text"
    And I choose "Allgemein" from tab menu
    And I should see "Schlagworte"
    And I should see "Social Media Buttons anzeigen"
    And I should see "Sprache"
    And I should see "Thema"
    And I should see "Inhaltstyp"
    And I should see "Bereich"
    And I should see "Ansichtssteuerung"
    And I choose "Header" via translation from tab menu
    And I should see "KOPFBEREICH"
    And I should see HTML content matching "Paragraph hinzufügen"
    And I choose "Seitenleiste rechts" from tab menu
    And I should see "Seitenleiste rechts"
    And I should see HTML content matching "Paragraph hinzufügen"
    And I choose "Content" via translation from tab menu
    And I should see "INHALTSBEREICH"
    And I should see HTML content matching "Paragraph hinzufügen"

  Scenario: I can select a view mode for views reference paragraphs
    Given I am logged in as a user with the "administrator" role
    And I am installing the "degov_paragraph_view_reference" module
    And I am on "/node/add/normal_page"
    And I choose "Content" via translation from tab menu
    And I press the "edit-field-content-paragraphs-add-more-add-modal-form-area-add-more" button
    And I wait 2 seconds
    And I click by CSS id "field-content-paragraphs-view-reference-add-more"
    Then I should see text matching "Ansichtsreferenz" via translation after a while
    And I should see 1 ".viewreference_target_id" element
    And I set the value of element ".viewreference_target_id" to "latest_events" via JavaScript
    And wait 2 seconds
    And I should see 1 ".viewsreference_view_mode:visible" elements via jQuery
    Then I assert dropdown named "field_content_paragraphs[0][subform][field_view_reference_view][0][options][view_mode]" contains the following text-value pairs:
      | text                   | value       |
      | As defined in the view |             |
      | Teaser kleines Bild    | small_image |
      | Teaser langer Text     | long_text   |
      | Teaser schmal          | slim        |
      | Teaser Preview         | preview     |

  Scenario: I verify that Media file link placeholders in text get transformed into actual links
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I am on "/degov-demo-content/page-text-paragraph"
    And I should not see HTML content matching "/sites/default/files/degov_demo_content/dummy.pdf"
    Then I open node edit form by node title "Page with text paragraph"
    And I should see HTML content matching "node-normal-page-edit-form" after a while
    And I enter the placeholder for a "document" media file in textarea
    And I scroll to the "#edit-submit" element
    And I press button with label "Save" via translated text
    Then I should see HTML content matching "/sites/default/files/degov_demo_content/dummy.pdf" after a while

  Scenario: I verify that I can enter Media file links using linkit
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I am on "/node/add/normal_page"
    And I wait 5 seconds
    And I click by selector ".cke_button__linkit" via JavaScript
    Then I should see 1 "#linkit-editor-dialog-form" elements via jQuery after a while
    And I fill in "Link" with "dummy"
    And I trigger the "keydown" event on ".form-linkit-autocomplete"
    Then I should see HTML content matching "linkit-result" after a while
    And I click by selector ".linkit-result" via JavaScript
    Then I verify that field value of ".form-linkit-autocomplete" matches "\[media:file:[\d]+\]"

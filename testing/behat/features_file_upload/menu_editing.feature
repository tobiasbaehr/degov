@api @drupal @entities
Feature: deGov - Menu editing

  Background:
    Given I proof that the following Drupal modules are installed:
      | link_attributes             |
      | media_file_links            |

  Scenario: I check that changing the file in a media item updates the referencing menu items
    Given I have dismissed the cookie banner if necessary
    And I am on the homepage
    Then I should see HTML content matching "dummy.pdf"
    And I should not see HTML content matching "word-document-2.docx"
    Then I am logged in as a user with the "administrator" role
    And I open media edit form by media name "sadipscing elitr sed diam äöüÄÖ"
    And I trigger the "mousedown" event on "#edit-field-document-0-remove-button"
    And I wait 3 seconds
    And I attach the file "word-document-2.docx" to "files[field_document_0]"
    And I wait 3 seconds
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I am on the homepage
    And I should see HTML content matching "word-document-2.docx"
@api @drupal
Feature: deGov - Content creation

  Background:
    Given I proof that the following Drupal modules are installed:
      | degov_node_press      |

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

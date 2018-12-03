@api @drupal @javascript
Feature: deGov - block layout publish date

  Background:
    Given I am installing the following Drupal modules:
      | degov_demo_content |
    Given I proof that the following Drupal modules are installed:
      | degov_search_media      |

  Scenario: I verify that the publish date block is set to only show in the mediathek
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/block"
    Then I should see text matching "Veröffentlichungsdatum" after a while
    And I click on Configbutton "Veröffentlichungsdatum"
    Then I should see HTML content matching "/mediathek" after a while

  Scenario: I verify that a node without sidebar paragraphs is displayed in one column
    Given I am on "/degov-demo-content/page-all-teasers"
    Then I should see HTML content matching "col-sm-12"

  Scenario: I verify that a node with sidebar paragraphs is displayed in two columns
    Given I am on "/degov-demo-content/page-text-paragraph-sidebar"
    Then I should see HTML content matching "col-sm-9"

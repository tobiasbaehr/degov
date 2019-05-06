@api @drupal @javascript
Feature: deGov - Toolbar

  Scenario: Verify that clicking on a toolbar toggle does not open a URL
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    And I am on the homepage
    Then I click "Manage" via translation
    And I should be on the homepage
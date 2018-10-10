@api @drupal
Feature: deGov - Search

  Scenario: Verify that search is configured for partial word matching
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/config/search/search-api/server/database/edit"
    Then I should see the input with the name "backend_config[matching]" and the value "partial" checked

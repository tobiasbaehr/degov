@api @drupal
Feature: deGov - Search facet

  Scenario: I check if a facet for Thema exists
    Given I am on "/suche"
    Then I should see text matching "Thema"

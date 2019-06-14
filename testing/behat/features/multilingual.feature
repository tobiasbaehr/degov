@api @drupal @entities
Feature: deGov - Multilingual

  Scenario: I am on paragraphs type page and homepage breadcrumb is translated
    Given I am logged in as a user with the "administrator" role
    Then I am installing the "degov_breadcrumb" module
    And I proof that Drupal module "degov_breadcrumb" is installed
    And I am on "/admin/structure/paragraphs_type"
    And I should see text matching "Startseite"
    Then I am on "/admin/config/regional/language/add"
    And I select "en" in "edit-predefined-langcode"
    And I press button with label "Add language" via translated text
    Then I should see text matching "Add language" via translation after a while
    And I am on "/en/admin/structure/paragraphs_type"
    And I should see text matching "Home"
    And I am on "/admin/config/regional/language/delete/en"
    And I press button with label "Delete" via translated text
    And I should see text matching "Die Sprache English (en) wurde entfernt." after a while

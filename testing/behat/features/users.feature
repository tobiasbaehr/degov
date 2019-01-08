@api @drupal
Feature: deGov - Users

  Background:
    Given I proof that Drupal module "degov_users_roles" is installed

  Scenario: I am on the frontpage
    Given I am on "/"
    Then I should not see text matching "Error"
    Then I should not see text matching "Warning"

  Scenario: I am installing the degov user roles module
    Given I am logged in as a user with the "Administrator" role
    Then I am installing the "degov_users_roles" module
    Then I am on "/admin/people/roles"
    And I should see "Chefredakteur"
    And I should see "Redakteur"
    And I should see "Benutzerverwaltung"

  Scenario: As a system configurator I should be able to only access language blocks
    Given I am logged in as a user with the "Systemkonfigurator" role
    And I am on "/admin/structure/block/"
    Then I should not see text matching "Access denied" via translated text
    And I should see text matching "deGov Theme"
    And I click "deGov Theme"
    And I should see 1 "table#blocks" elements
    And I should see 0 "div.dropbutton-widget" elements
    And I should not see text matching "Language switcher" via translated text
    Then I click by selector "a#edit-blocks-region-top-header-title" via JavaScript
    And I should see text matching "Language switcher" via translated text after a while
    And I should see 1 "table.block-add-table tbody tr" elements
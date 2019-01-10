@api @drupal
Feature: deGov - Users

  Background:
    Given I proof that Drupal module "degov_users_roles" is installed

  Scenario: I am on the frontpage
    Given I am on "/"
    Then I should not see text matching "Error"
    Then I should not see text matching "Warning"

  Scenario: I am installing the degov user roles module
    Given I have dismissed the cookie banner if necessary
    Given I am logged in as a user with the "Administrator" role
    Then I am installing the "degov_users_roles" module
    Then I am on "/admin/people/roles"
    And I should see "Chefredakteur"
    And I should see "Redakteur"
    And I should see "Benutzerverwaltung"
@api @drupal
Feature: deGov - Users

  Background:
    Given I proof that Drupal module "degov_users_roles" is installed
    Given I proof that Drupal module "degov_simplenews_references" is installed


  Scenario: I am on the frontpage
    Given I am on "/"
    Then I should not see text matching "Error"
    Then I should not see text matching "Warning"

  Scenario: I am installing the degov user roles module
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I am installing the "degov_users_roles" module
    Then I am on "/admin/people/roles"
    And I should see "Chefredakteur"
    And I should see "Redakteur"
    And I should see "Benutzerverwaltung"

  Scenario: I try to create a new user with simplenews abonnement
    And I am logged in as a user with the "administrator" role
    And I am on "/admin/people/create"
    And I fill in "Username" via translated text with "test"
    And I fill in "Password" via translated text with "test"
    And I fill in "Confirm password" via translated text with "test"
    And I press button with label "Create new account" via translated text
    Then I should be on "/admin/people/create"

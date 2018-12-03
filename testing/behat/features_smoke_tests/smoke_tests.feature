@api @drupal
Feature: deGov - Smoke tests

  Scenario: Check foo
    Given I am on "/user/login"
    And I fill in "edit-name" with "Peter Majmesku"
    And I fill in "edit-pass" with "eri4t4z"
    And I click by selector ".user-login-form #edit-submit" via JavaScript


@api @drupal
Feature: deGov - File management

  Background:
    Given I proof that the following Drupal modules are installed:
      | degov_file_management   |
    Given I have created an unused file entity

  Scenario: An unauthenticated user must not be able to delete a file
    Given I have dismissed the cookie banner if necessary
    Then I visit the delete form for the unused file entity
    And I should see text matching "You are not authorized to access this page." via translated text

  Scenario: A logged in user with insufficient permissions must not be able to delete a file
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "usermanager" role
    Then I visit the delete form for the unused file entity
    And I should see text matching "You are not authorized to access this page." via translated text

  Scenario: A user with the editor role should be able to delete a file
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "editor" role
    Then I visit the delete form for the unused file entity
    And I should not see text matching "You are not authorized to access this page." via translated text

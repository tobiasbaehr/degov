@api @drupal @performance @performance_editor @content
Feature: nrwGOV - Static pages performance without measuring

  Background:
    Given I am installing the following Drupal modules:
      | degov_behat_extension |

  Scenario: I visit all static pages
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "editor" role
    And I visit static pages

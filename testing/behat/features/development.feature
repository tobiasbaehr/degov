@api @drupal
Feature: deGov - Development

  Background:
    Given I am installing the following Drupal modules:
      | degov_devel |

  Scenario: Check that the webprofiler is visible
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    And I proof that Drupal module "degov_devel" is installed
    Then I am on the homepage
    And I should see 1 ".sf-toolbar" elements

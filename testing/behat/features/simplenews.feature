@api @drupal
Feature: deGov Simplenews
  In order to ensure we have a GDPR-compliant newsletter setup
  As an administrator
  I check that we have safeguards and settings in place for GDPR-compliance

  Background:
    Given I am installing the following Drupal modules:
      | simplenews                        |
      | degov_simplenews                  |
    Given I proof that the following Drupal modules are installed:
      | simplenews                        |
      | degov_simplenews                  |

  Scenario: Status page contains an entry for deGov Simplenews
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/reports/status"
    Then I should see text matching "DEGOV - SIMPLENEWS" after a while
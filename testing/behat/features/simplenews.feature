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

  Scenario: When I create a newsletter I only see safe settings options
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/config/services/simplenews"
    Then I click "Newsletter hinzufügen"
    Then I should see text matching "Subscription settings" via translated text in uppercase
    Then I should see an "optgroup" element with the translated "label" attribute "Silent"
    Then I should see an "optgroup" element with the translated "label" attribute "Hidden"
    Then I should see an "optgroup" element with the translated "label" attribute "Single"
    Then I fill in "Name" with "My great newsletter"
    Then I fill in "Maschinenlesbarer Name" with "my_great_newsletter"
    Then I select "Doppelt" in "edit-opt-inout"
    And I press button with label "Save" via translated text
    Then I should see "My great newsletter" in the "td" element

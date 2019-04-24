@api @drupal
Feature: deGov Simplenews
  In order to ensure we have a GDPR-compliant newsletter setup
  As an administrator
  I check that we have safeguards and settings in place for GDPR-compliance

  Background:
    Given I am installing the following Drupal modules:
      | simplenews                        |
      | degov_simplenews                  |
      | degov_demo_content                |
    Given I proof that the following Drupal modules are installed:
      | simplenews                        |
      | degov_simplenews                  |
      | degov_demo_content                |

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
    And I trigger the "change" event on "#edit-name"
    Then I select "Doppelt" in "edit-opt-inout"
    And I press button with label "Save" via translated text
    Then I should see "My great newsletter" in the "td" element

  Scenario: I can set a custom consent message
    Given I configure and place the Simplenews signup block
    And I set the privacy policy page for all languages
    And I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I am on "/admin/config/degov/simplenews"
    And I fill in "Consent message (de)" with "ConsentTest1234"
    And I scroll to element with id "edit-submit"
    And I click by CSS id "edit-submit"
    Then I should see text matching "The configuration options have been saved." via translation after a while
    Then I am on "/degov-demo-content/page-all-teasers"
    And I should see text matching "ConsentTest1234"

  Scenario: Consent messages for multiple languages can be saved
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I am on "/admin/config/regional/language/add"
    And I select "Englisch" in "edit-predefined-langcode"
    And I press button with label "Add language" via translated text
    Then I should see text matching "Add language" via translation after a while
    Then I set the privacy policy page for all languages
    Then I am on "/admin/config/degov/simplenews"
    And I fill in "Consent message (de)" with "ConsentTest1234"
    And I fill in "Consent message (en)" with "ConsentTest5678"
    And I scroll to element with id "edit-submit"
    And I click by CSS id "edit-submit"
    Then I should see text matching "The configuration options have been saved." via translation after a while
    And I should see HTML content matching "ConsentTest1234"
    And I should see HTML content matching "ConsentTest5678"

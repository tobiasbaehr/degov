@api @drupal @javascript
Feature: deGov Social Media Sharing
  To ensure that user's privacy is protected
  As an administrator
  I check that we offer privacy-conscious sharing

  Background:
    Given I am installing the following Drupal modules:
      | degov_demo_content                |
    Given I proof that the following Drupal modules are installed:
      | degov_demo_content                |

  Scenario: I check that 2-click-sharing is enabled by default
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I open node edit form by node title "Page with all teasers"
    And I choose "General" via translation from tab menu
    Then I check checkbox with id "edit-field-social-media-value"
    And I click by CSS id "edit-submit"
    Then I should see HTML content matching "alert-status" after a while
    And I should see 3 "li.shariff-button" elements via JavaScript
    And I wait 1 seconds
    And I should see 3 "div.sharing-overlay" elements via JavaScript

  Scenario: I check that I can disable 2-click-sharing
    Given I have dismissed the cookie banner if necessary
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/config/services/shariff"
    And I check checkbox with id "edit-enable-1-click-sharing"
    And I click by CSS id "edit-submit"
    Then I should see text matching "The configuration options have been saved." via translation after a while
    Then I open node edit form by node title "Page with all teasers"
    And I choose "General" via translation from tab menu
    Then I check checkbox with id "edit-field-social-media-value"
    And I click by CSS id "edit-submit"
    Then I should see HTML content matching "alert-status" after a while
    And I should see 3 "li.shariff-button" elements via JavaScript
    And I wait 1 seconds
    And I should see 0 "div.sharing-overlay" elements via JavaScript

  Scenario: I see no error on updates page for degov_tweets
    Given I am logged in as a user with the "administrator" role
    Given I am on "/admin/reports/updates"
    And I should not see HTML content matching "Unbekannt"
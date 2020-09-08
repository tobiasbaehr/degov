@api @drupal @form
Feature: deGov - Webforms

  Background:
    Given I am installing the "degov_honeypot" module
    Given I proof that the following Drupal modules are installed:
      | webform  |
      | honeypot |

  Scenario: Verify that no Webform warnings are shown on the status page
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    And I am on "/admin/reports/status"
    Then I should not see text matching "Webform: HTML email support" via translated text in "css" selector ".system-status-report__entry--warning summary"
    And I should not see text matching "Webform: Private files" via translated text in "css" selector ".system-status-report__entry--warning summary"
    And I should not see text matching "Webform: Bootstrap integration" via translated text in "css" selector ".system-status-report__entry--warning summary"
    And I should not see text matching "Webform: SPAM protection" via translated text in "css" selector ".system-status-report__entry--warning summary"
    And I should not see text matching "Webform: External libraries" via translated text in "css" selector ".system-status-report__entry--warning summary"
    And I should not see text matching "Webform: Source" via translated text in "css" selector ".system-status-report__entry--warning summary"
    And I should not see text matching "Webform: Test" via translated text in "css" selector ".system-status-report__entry--warning summary"
    And I should not see text matching "Webform: API" via translated text in "css" selector ".system-status-report__entry--warning summary"
    And I should not see text matching "Webform: Translate" via translated text in "css" selector ".system-status-report__entry--warning summary"
    And I should not see text matching "Webform: Contribute" via translated text in "css" selector ".system-status-report__entry--warning summary"
    And I should not see text matching "Webform: Source entity" via translated text in "css" selector ".system-status-report__entry--warning summary"

  Scenario: Verify that the default contact form has a honeypot installed
    Given I have dismissed the cookie banner if necessary
    And I am on "/form/contact"
    Then I should see 1 "#edit-url" elements
    And I should see 1 "[name=honeypot_time]" elements

@api @drupal @javascript
  Feature: Scheduled moderation
    Scenario: Creating node with schedued publish - Timeout
      Given I am logged in as a user with the "editor" role
      And I am installing the "degov_scheduled_updates" module
      And I am on "/node/add/normal_page"
      And I fill in "Test" for "Titel"
      And I fill in "01/01/2018" for "edit-field-scheduled-publish-0-value-date"
      And I fill in "01:00:00 AM" for "edit-field-scheduled-publish-0-value-time"
      And I select "published" in "edit-field-scheduled-publish-0-moderation-state"
      And I select "draft" in "edit-moderation-state-0-state"
      And I press the "Speichern" button
      And I run the cron
      And I am on "/admin/content"
      And I click "Test"
      And I proof content with title "Test" has moderation state "published"


@api @drupal @javascript @access
  Feature: deGov Scheduled moderation

    Scenario: Creating node with scheduled publish - Needs update
      Given I am logged in as a user with the "administrator" role
      And I proof that Drupal module "degov_scheduled_updates" is installed
      And I am on "/node/add/normal_page"
      And I fill in "Creating node with scheduled publish" for "Titel"
      And I select "published" in "edit-field-scheduled-publish-0-moderation-state"
      And I fill in "01012018" for "edit-field-scheduled-publish-0-value-date"
      And I fill in "010000AM" for "edit-field-scheduled-publish-0-value-time"
      And I select "draft" in "edit-moderation-state-0-state"
      And I press button with label "Save" via translated text

      And I dump the HTML

      And I am on "/admin/content"
      And I run the cron
      And I click "Creating node with scheduled publish"
      And I should see HTML content matching "Creating node with scheduled publish" after a while
      And I proof content with title "Creating node with scheduled publish" has moderation state "published"

    Scenario: deGov Creating node with scheduled publish - No update
      Given I am logged in as a user with the "administrator" role
      And I proof that Drupal module "degov_scheduled_updates" is installed
      And I am on "/node/add/normal_page"
      And I fill in "Test" for "Titel"
      And I select "published" in "edit-field-scheduled-publish-0-moderation-state"
      And I fill in "01012118" for "edit-field-scheduled-publish-0-value-date"
      And I fill in "010000AM" for "edit-field-scheduled-publish-0-value-time"
      And I select "draft" in "edit-moderation-state-0-state"
      And I press button with label "Save" via translated text
      And I run the cron
      And I am on "/admin/content"
      And I click "Test"
      And I proof content with title "Test" has moderation state "draft"

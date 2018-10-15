@api @drupal @javascript
Feature: deGov - Paragraphs

  Scenario: Banner paragraph should contain expected fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/paragraphs_type/image_header/fields"
    Then I should see the fields list with exactly 2 entries
    And I should see text matching "field_override_caption"
    And I should see text matching "field_header_media"

  Scenario: Block Types given
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page#edit-group-content"
    And I fill in "testblockreferenz" for "Titel"
    And I click on togglebutton
    And I press "edit-field-content-paragraphs-add-more-add-more-button-block-reference"
    And I click by selector "select[name=\'field_content_paragraphs[0][subform][field_block_plugin][0][plugin_id]\']" via JavaScript
    And I should see HTML content matching "degov_twitter_block"
    And I should see HTML content matching "degov_social_media_instagram"
    And I should see HTML content matching "degov_social_media_youtube"
    And I should see HTML content matching "views_block:press_latest_content-latest_press"
    And I should see HTML content matching "simplenews_subscription_block"
    And I select "simplenews_subscription_block" from "field_content_paragraphs[0][subform][field_block_plugin][0][plugin_id]"
    And I should see text matching "Newsletter" after a while
    And I check checkbox by value "default" via JavaScript
    And I select "published" from "edit-moderation-state-0-state"
    And I press button with label "Save" via translated text
    And I should see text matching "Simplenews Abonnement"


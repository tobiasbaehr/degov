@api @drupal
Feature: deGov - Paragraphs

  Scenario: Banner paragraph should contain expected fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/paragraphs_type/image_header/fields"
    Then I should see the fields list with exactly 2 entries
    And I should see text matching "field_override_caption"
    And I should see text matching "field_header_media"

  Scenario: Block Types given
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page"
    And I fill in "testblockreferenz" for "Titel"
    And I click "Inhalt"
    And I choose "Inhalt" from tab menu
    And I click on togglebutton
    And I click "Block Referenz"
    And I should see HTML content matching "degov_twitter_block"
    And I should see HTML content matching "degov_social_media_instagram"
    And I should see HTML content matching "degov_social_media_youtube"
    And I should see HTML content matching "views_block:press_latest_content-latest_press"
    And I should see HTML content matching "menu_block:main"
    And I should see HTML content matching "simplenews_subscription_block"
    And I select "simplenews_subscription_block" in "field_content_paragraphs[0][subform][field_block_plugin][0][plugin_id]"
    And I check the box "deGov - Newsletter"
    And I should be on "/testblockreferenz"
    And I should see text matching "Simplenews Abonnement"


@api @drupal
Feature: deGov - Paragraphs

  Scenario: Banner paragraph should contain expected fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/paragraphs_type/image_header/fields"
    Then I should see the fields list with exactly 2 entries
    And I should see text matching "field_override_caption"
    And I should see text matching "field_header_media"

  Scenario: Paragraph bock reference has correct blocks and can create an instance
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page#edit-group-righ"
    And I fill in "testblockreferenz" for "Titel"
    And I click on togglebutton
    And I press "field_sidebar_right_paragraphs_block_reference_add_more"
    And I should see text matching "Block Referenz" after a while
    Given Select "field_sidebar_right_paragraphs[0][subform][field_block_plugin][0][plugin_id]" has following options "views_block:press_latest_content-latest_press simplenews_subscription_block"
    And I select "simplenews_subscription_block" from "field_sidebar_right_paragraphs[0][subform][field_block_plugin][0][plugin_id]"
    And I should see text matching "Newsletter" after a while
    And I check checkbox by value "default" via JavaScript
    And I select "published" from "edit-moderation-state-0-state"
    And I press button with label "Save" via translated text
    And I should see text matching "Simplenews Abonnement"

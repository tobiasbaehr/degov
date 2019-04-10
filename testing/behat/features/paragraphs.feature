@api @drupal
Feature: deGov - Paragraphs

  Scenario: Banner paragraph should contain expected fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/paragraphs_type/image_header/fields"
    Then I should see the fields list with exactly 2 entries
    And I should see text matching "field_override_caption"
    And I should see text matching "field_header_media"

  Scenario: Paragraph block reference has correct blocks and can create an instance
    Given I set newsletter privacy policy page
    And I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page#edit-group-righ"
    And I fill in "testblockreferenz" for "Titel"
    And I click on togglebutton
    And I press the "edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" button
    And I press "field_sidebar_right_paragraphs_block_reference_sidebar_add_more"
    And I should see text matching "Block Referenz Seitenleiste" after a while
    Given Select "field_sidebar_right_paragraphs[0][subform][field_block_plugin][0][plugin_id]" has following options "views_block:press_latest_content-latest_press simplenews_subscription_block"
    And I select "simplenews_subscription_block" from "field_sidebar_right_paragraphs[0][subform][field_block_plugin][0][plugin_id]"
    And I should see text matching "Newsletter" after a while
    And I check checkbox by value "default" via JavaScript
    And I select "published" from "edit-moderation-state-0-state"
    And I press button with label "Save" via translated text
    And I should see HTML content matching "simplenews-subscriber-form"

  Scenario: Blocks in sidebar block reference have reduced title options
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page"
    And I click "Seitenleiste rechts"
    And I press the "edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" button
    And I press "field_sidebar_right_paragraphs_block_reference_sidebar_add_more"
    And I should see text matching "Block Referenz Seitenleiste" after a while
    Given Select "field_sidebar_right_paragraphs[0][subform][field_block_plugin][0][plugin_id]" has following options "views_block:press_latest_content-latest_press simplenews_subscription_block"
    And I select "views_block:press_latest_content-latest_press" from "field_sidebar_right_paragraphs[0][subform][field_block_plugin][0][plugin_id]"
    And I should not see text matching "Titel anzeigen" after a while
    And I should not see text matching "Titel übersteuern" after a while
    And I should not see HTML content matching "edit-field-sidebar-right-paragraphs-0-subform-field-block-plugin-0-settings-views-label-fieldset"

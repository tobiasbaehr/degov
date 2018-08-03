@api @drupal
Feature: deGov - Social media Instagram

  Background:
    Given I delete any existing blocks with comma separated ids "social_media_settings_block, instagramfeedblock"
    Given I am installing the "degov_social_media_settings" module
    Given I am installing the "degov_social_media_instagram" module
    Given I configure and place the deGov social media settings block
    Given I configure and place the Instagram feed block

  Scenario: I see the Instagram feed block after enabling it in the social media settings
    Given I am logged in as an "Administrator"
    Then I should see HTML content matching "Social Media Settings" after a while
    Then I am on "/"
    And I should see text matching "Instagram feed block"
    And I should see text matching "Quelle ist deaktiviert"
    And I should not see text matching "Pause"
    And I should not see text matching "Wiedergabe"
    And I should not see HTML content matching "slick-slide"
    Then I click "Social Media Settings"
    And I check checkbox by value "instagram" via JavaScript
    And I click by selector ".social-media-settings__save" via JavaScript
    Then I should see HTML content matching "slick-slide" after a while
    And I should see text matching "Pause" after a while
    And I should see text matching "Wiedergabe" after a while
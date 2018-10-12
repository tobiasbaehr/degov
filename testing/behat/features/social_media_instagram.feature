@api @drupal
Feature: deGov - Social media Instagram

  Background:
    Given I am installing the "degov_social_media_instagram" module
    Given I proof that Drupal module "degov_social_media_settings" is installed
    Given I delete any existing blocks with comma separated ids "social_media_settings_block, instagramfeedblock"
    Given I configure and place the deGov social media settings block
    Given I configure and place the Instagram feed block

  Scenario: I see the Instagram feed block after enabling it in the social media settings
    Given I am on "/"
    Then I dump the HTML
#    Then I should see HTML content matching "Social Media Settings" after a while
    And I should see HTML content matching "Instagram feed block"
    And I should not see HTML content matching "slick-slide"
    Then I click by selector ".social-media-settings--menu-item" via JavaScript
    And I check checkbox by value "instagram" via JavaScript
    And I click by selector ".social-media-settings__save" via JavaScript
    And I should not see HTML content matching "This social media source is disabled. After accepting our cookie policy, you can enable it."
    Then I should see HTML content matching "slick-slide" after a while


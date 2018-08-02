@api @drupal
Feature: deGov - Social media Instagram

  Background:
    Given I am installing the "degov_social_media_settings" module
    And I am installing the "degov_social_media_instagram" module
    And I configure and place the deGov social media settings block
#  Scenario: I am fulfilling the settings
#    Given I am logged in as an "Administrator"
#    And I am on "/admin/config/degov_social_media_instagram"
#    Then I fill in "user" with "ig_bundestag"
#    And I submit the form
#    Then I should see HTML content matching "ig_bundestag" after a while

  Scenario: I am creating an Instagram feed block
    Given I am logged in as an "Administrator"
    And wait 20 seconds
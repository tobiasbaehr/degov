@api @degov_media_usage @entities
Feature: deGov - Media usage

  Background:
    Given I proof that the following Drupal modules are installed:
      | degov_media_usage |
    Given I am logged in as a user with the "administrator" role
    Given I have dismissed the cookie banner if necessary

  Scenario: Check for new views field
    And I am on "/admin/structure/views/nojs/add-handler/media/media_page_list/field"
    Then I should see "Media usage count"

  Scenario: Check new "used in" column on media overview
    And I am on "/admin/content/media"
    Then I should see HTML content matching "Used in" via translated text

  Scenario: Check media entity detail page
    And I created a content page named "Test1234" with a media "image"
    And I am on "/admin/content/media"
    Then I should see the text "Some image" in the "Some image" row
    When I click "1" in the "Some image" row
    Then I should see text matching 'Browse media "Some image" references' after a while

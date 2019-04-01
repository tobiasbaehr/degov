@api @drupal
Feature: deGov - Search

  Background:
    Given I proof that the following Drupal modules are installed:
      | degov_search_content       |
      | degov_search_media         |
    And I am installing the following Drupal modules:
      | degov_search_media_manager |
      | degov_demo_content         |
    And I reset the demo content

  Scenario: Verify that search is configured for partial word matching
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/config/search/search-api/server/database/edit"
    Then I should see the input with the name "backend_config[matching]" and the value "partial" checked

  Scenario: Check if media search is working properly
    Given I am logged in as a user with the "administrator" role
    And I have dismissed the cookie banner if necessary
    And I open media edit form by media name "demo image with a fixed title"
    And I choose "Allgemein" from tab menu
    And I check the box "edit-field-include-search-value"
    And I scroll to element with id "edit-submit"
    And I press "Speichern"
    And I am on "/mediathek"
    Then I should see HTML content matching "demo image with a fixed title" after a while
    And I open media edit form by media name "demo image with a fixed title"
    And I choose "Allgemein" from tab menu
    And I uncheck the box "edit-field-include-search-value"
    And I scroll to element with id "edit-submit"
    And I press "Speichern"
    And I am on "/mediathek"
    And I should not see "demo image with a fixed title"

  Scenario: I verify that the node content type filter shows labels, not machine names
    Then I am logged in as a user with the "administrator" role
    And I have dismissed the cookie banner if necessary
    And I rebuild the "search_content" index
    And I clear the cache
    Then I am on "/suche"
    And I should see an ".facet-item__value" element with the content "Inhaltsseite"

  Scenario: I verify that the media bundle filter shows labels, not machine names
    Then I am logged in as a user with the "administrator" role
    And I have dismissed the cookie banner if necessary
    And I open media edit form by media name "et dolore magna aliquyam erat"
    And I choose "Allgemein" from tab menu
    And I check the box "edit-field-include-search-value"
    And I scroll to element with id "edit-submit"
    And I press "Speichern"
    And I open media edit form by media name "ipsum dolor sit amet consetetur"
    And I choose "Allgemein" from tab menu
    And I check the box "edit-field-include-search-value"
    And I scroll to element with id "edit-submit"
    And I press "Speichern"
    And I rebuild the "search_media" index
    And I clear the cache
    Then I am on "/mediathek"
    And I should see HTML content matching "Video Upload" after a while
    And I should see HTML content matching "Bild" after a while
    And I should see an ".facet-item__value" element with the content "Bild"
    And I should see an ".facet-item__value" element with the content "Video Upload"

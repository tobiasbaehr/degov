@api @drupal @entities
Feature: deGov - Entity browser

  Background:
    Given I proof that the following Drupal modules are installed:
      | degov_node_normal_page      |

  Scenario: Entity browser has only expected tabs
    Given I have dismissed the cookie banner if necessary
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page"
    And I press the "edit-field-teaser-image-entity-browser-entity-browser-open-modal" button
    And I wait 2 seconds
    And I focus on the Iframe with ID "entity_browser_iframe_media_browser"
    And I should see 6 ".eb-tabs > ul > li" elements
    And I should see text matching "Library"
    And I should see text matching "Bilder Hochladen"
    And I should see text matching "Upload"
    And I should see text matching "Dokumente Hochladen"
    And I should see text matching "Create embed"
    And I should see text matching "Audio hochladen"

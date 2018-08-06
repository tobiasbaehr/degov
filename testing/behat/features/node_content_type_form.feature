@api @drupal
Feature: deGov - Content type form

  Background:
    Given I proof that the following Drupal modules are installed:
      | machine_name           |
      | degov_node_normal_page |
      | pathauto               |
      | degov_pathauto         |

  Scenario: Check if all vertical tabs and advanced widgets are available
    Given I am logged in as a user with the "administrator" role
    Then I am on "/node/add/normal_page"
    And I should see text matching "Titel"
    And I should see text matching "Vorschau"
    And I should see text matching "Allgemein"
    And I should see text matching "Kopfbereich"
    And I should see text matching "Seitenleiste rechts"
    And I should see text matching "Inhalt"
    And I should see text matching "Vorschau Titel"
    And I should see text matching "Vorschau Text"
    And I should see text matching "Textformat"
    And I should see text matching "VORSCHAU BILD"
    And I should see text matching "Vorschau Untertitel"
    And I should see text matching "Speichern unter"
    And I should see text matching "Zuletzt gespeichert"
    And I should see text matching "Protokollnachricht der Version"
    And I should see text matching "MENÜEINSTELLUNGEN"
    And I should see text matching "URL-ALIAS-EINSTELLUNGEN"
    And I should see text matching "INFORMATIONEN ZUM AUTOR"
    And I should see text matching "HERVORHEBUNGSOPTIONEN"

  Scenario: Add content to normal page
    Given I am logged in as a user with the "administrator" role
    Then I am on "/node/add/normal_page"
    And I fill in "title[0][value]" with "aabbcc"
    And I fill in "field_teaser_title[0][value]" with "preview__aaabbcc"
    And I fill in Textarea with "TEST TEST TEST"
    And I choose "Allgemein" from tab menu
    Then I should see text matching "SCHLAGWORTE" after a while
    And I choose "Kopfbereich" from tab menu
    And I choose "Seitenleiste rechts" from tab menu
    And I choose "Inhalt" from tab menu
    And I click on togglebutton
    Then I should see text matching "URL-ALIAS-EINSTELLUNGEN" after a while
    And I select "URL-ALIAS-EINSTELLUNGEN" from rightpane
    Then I should see text matching "Automatischen URL-Alias erzeugen" after a while
    And I fill in "path[0][alias]" with "/aabbcc"
    And I choose "Veröffentlicht" in selectModerationBox
    And I press the "Speichern" button
    Then I should be on "/aabbcc"

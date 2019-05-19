@api @drupal @node_content_type_form
Feature: deGov - Content type form

  Background:
    Given I proof that the following Drupal modules are installed:
      | degov_node_normal_page |
      | pathauto               |
      | degov_pathauto         |

  Scenario: Check if all vertical tabs and advanced widgets are available
    Given I am logged in as a user with the "administrator" role
    Then I am on "/node/add/normal_page"
    And I should see text matching "Title" via translated text
    And I should see text matching "Preview" via translated text
    And I should see text matching "General" via translated text
    And I should see text matching "Header" via translated text
    And I should see text matching "Seitenleiste rechts"
    And I should see text matching "Content" via translated text
    And I should see text matching "Vorschau Titel"
    And I should see text matching "Vorschau Text"
    And I should see text matching "Text format" via translated text
    And I should see text matching "VORSCHAU BILD"
    And I should see text matching "Vorschau Untertitel"
    And I should see text matching "Save as" via translated text
    And I should see text matching "Last saved" via translated text
    And I should see text matching "Revision log message" via translated text
    And I should see text matching "Menu settings" via translated text in uppercase
    And I should see text matching "URL alias" via translated text in uppercase
    And I should see text matching "Authoring information" via translated text in uppercase
    And I should see text matching "Promotion options" via translated text in uppercase

  Scenario: Add content to normal page
    Given I am logged in as a user with the "administrator" role
    Then I am on "/node/add/normal_page"
    And I fill in "title[0][value]" with "aabbcc"
    And I fill in "field_teaser_title[0][value]" with "preview__aaabbcc"
    And I fill in Textarea with "TEST TEST TEST"
    And I choose "General" via translation from tab menu
    Then I should see text matching "Schlagworte"
    And I choose "Header" via translation from tab menu
    And I choose "Seitenleiste rechts" from tab menu
    And I choose "Inhalt" from tab menu
    And I click on togglebutton
    Then I should see text matching "URL alias" via translated text in uppercase after a while
    And I select "URL alias" via translation in uppercase from rightpane
    Then I should see text matching "Generate automatic URL alias" via translated text after a while
    And I fill in "path[0][alias]" with "/aabbcc"
    And I choose "Published" via translation in selectModerationBox
    And I press the "edit-submit" button
    Then I should be on "/aabbcc"

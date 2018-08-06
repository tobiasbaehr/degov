@api @drupal
Feature: deGov - Media creation

  Background:
    Given I proof that the following Drupal modules are installed:
      | machine_name                    |
      | degov_media_video               |
      | degov_node_overrides            |
      | degov_node_normal_page          |
      | degov_paragraph_text            |
      | degov_paragraph_media_reference |

  Scenario: I am creating a address media entity
    Given I am logged in as a user with the "Administrator" role
    And I am on "/media/add/address"
    And I should see text matching "Adresse hinzufügen"
    Then I fill in "Example address" for "Name"
    And I fill in "Example address public" for "Öffentlicher Titel"
    And I should see HTML content matching "Straße" after a while
    And I fill in "Bilker Straße 29" for "Straße"
    And I fill in "40213" for "Postleitzahl"
    And I fill in "Düsseldorf" for "Stadt"
    And I click "Allgemein"
    And I check the box "Mediathek"
    Then I scroll to bottom
    And I press the "Speichern" button
    Then I should see text matching "Example address" after a while
    Then I am on "/node/add/normal_page"
    And I fill in "Example normal page title" for "Titel"
    And I click by selector ".vertical-tabs__menu-item.last a" via JavaScript
    And I click by selector "#edit-field-content-paragraphs button" via JavaScript
    Then I scroll to bottom
    And I press the "Speichern" button
    And I am on "/admin/content"
    Then I should see text matching "Example normal page title" after a while

  Scenario: I am creating a quote media entity
    Given I am logged in as a user with the "Administrator" role
    And I am on "media/add/citation"
    And I click "Beschreibung"
    Then I should see text matching "Öffentlicher Titel" after a while
    And I fill in the following:
      | Name               | Example quote              |
      | Öffentlicher Titel | Example quote public       |
      | Text               | Example text. Lorem ipsum. |
    Then I scroll to bottom
    And I press the "Speichern" button
    And I am on "/admin/content/media"
    Then I should see text matching "Example quote" after a while

  Scenario: I am creating a person media entity
    Given I am logged in as a user with the "Administrator" role
    And I am on "media/add/person"
    And I click "Beschreibung"
    Then I should see text matching "Öffentlicher Titel" after a while
    And I fill in the following:
      | Name               | Example person |
      | Öffentlicher Titel | Example person public |
    Then I scroll to bottom
    And I press the "Speichern" button
    And I am on "/admin/content/media"
    Then I should see text matching "Example person" after a while

  Scenario: I am creating a video media entity
    Given I am logged in as an "Administrator"
    When I go to "/media/add/video"
    Then I should see text matching "Quelle" after a while
    And I fill in the following:
      | Öffentlicher Titel  | Example video                               |
      | Name                | Example video public                        |
      | Video-URL           | https://www.youtube.com/watch?v=qREKP9oijWI |
      | Quelle              | youtube                                     |
    And I scroll to element with id "edit-submit"
    And I press "Speichern"
    And I should not see text matching "Es konnte kein Video-Provider gefunden werden, der den angegeben URL verarbeiten kann."
    And I should see "Video Example video public wurde erstellt."

  Scenario: I am creating an instagram media entity
    Given I am logged in as an "Administrator"
    When I go to "media/add/instagram"
    Then I should see text matching "Öffentlicher Titel" after a while
    And I fill in the following:
      | Name               | Example Instagram                      |
      | Öffentlicher Titel | Example Instagram public               |
      | Instagram post     | https://www.instagram.com/p/JUvux9iFRY |
    And I scroll to element with id "edit-submit"
    And I press "Speichern"
    Then I should not see "ist erforderlich."
    And I should see "Example Instagram wurde erstellt."

@api @drupal
Feature: deGov - Media creation

  Scenario: I am creating media address entity
    Given I am logged in as a user with the "Administrator" role
    And I am on "/media/add/address"
    And I should see text matching "Adresse hinzufügen"
    Then I fill in "test1234" for "Name"
    And I fill in "Bilker Straße 29" for "Straße"
    And I fill in "40213" for "Postleitzahl"
    And I fill in "Düsseldorf" for "Stadt"
    And I click "Allgemein"
    And I check the box "Mediathek"
    And I submit a form by id "media-address-add-form"
    And I should not see "Error"
    And I should not see "Warning"

  Scenario: I am creating media video entity
    Given I am logged in as an "Administrator"
    When I go to "/media/add/video"
    And I fill in the following:
      | Name                | fooVideo                    |
      | Öffentlicher Titel  | fooVideoOeffi               |
      | Video-URL           | https://vimeo.com/191669818 |
    And I scroll to element with id "edit-submit"
    And I press "Speichern"
    Then I should not see "ist erforderlich."
    And I should see "Video fooVideo wurde erstellt."

  Scenario: I am creating media instagram entity
    Given I am logged in as an "Administrator"
    When I go to "media/add/instagram"
    And I fill in the following:
      | Name               | fooInstagram                           |
      | Öffentlicher Titel | fooInstagramOeffi                      |
      | Instagram post     | https://www.instagram.com/p/JUvux9iFRY |
    And I scroll to element with id "edit-submit"
    And I press "Speichern"
    Then I should not see "ist erforderlich."
    And I should see "Instagram fooInstagram wurde erstellt."

  Scenario: I visit a media instagram
    Given I am logged in as an "Administrator"
    And I press the "Ich stimme zu" button
    When I create a media of type "instagram" named "fooInstagram"
    And I go to "/admin/content/media"
    And I click "duesseldorf"
    And I click "Social Media Settings"
    And I wait 1 second
    And I check checkbox with value "all"
    And I press button by selector ".modal-button.social-media-settings__save"
    And I wait 5 seconds
    Then I should see "fooInstagram"
    And I should see HTML content matching "EmbeddedMediaImage"

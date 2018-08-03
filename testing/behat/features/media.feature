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
    And I submit a form by id "media-video-add-form"
    Then I should not see "Error"
    And I should not see "Warning"

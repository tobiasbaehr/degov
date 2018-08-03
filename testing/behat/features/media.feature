@api @drupal @javascript @hase
Feature: deGov - Media creation

  Scenario: I am creating media address entity
    Given I am logged in as a user with the "Administrator" role
    And I press the "Ich stimme zu" button
    And I am on "/media/add/address"
    And I should see text matching "Adresse hinzufügen"
    Then I fill in "testAddress1234" for "Name"
    And I fill in "testAddress1234" for "Öffentlicher Titel"
    And I fill in "Bilker Straße 29" for "Straße"
    And I fill in "40213" for "Postleitzahl"
    And I fill in "Düsseldorf" for "Stadt"
    And I click "Allgemein"
    And I check the box "Mediathek"
    Then I scroll to bottom
    And I press the "Speichern" button
    Then I should be on "/testaddress1234"

    And I am on "/node/add/normal_page"
    And I fill in "testAddress1234" for "Titel"
    And I click with jquery selector ".vertical-tabs__menu-item.last a"
    And I click with jquery selector "#edit-field-content-paragraphs button"
    Then I click by XPath "//*[@id='edit-field-content-paragraphs-add-more-add-more-button-map']"
    Then I scroll to bottom
    And I press the "Speichern" button
    And I am on "/admin/content"
    Then I should see text matching "testAddress1234" after a while

  Scenario: I am creating quote
    Given I am logged in as a user with the "Administrator" role
    And I am on "media/add/citation"
    And I click "Beschreibung"
    And I fill in the following:
      | Name               | testZitat1234    |
      | Öffentlicher Titel | testZitat1234    |
      | Text               | das ist ein test |
    Then I scroll to bottom
    And I press the "Speichern" button
    And I am on "/admin/content/media"
    Then I should see text matching "testZitat1234" after a while

  Scenario: I am creating a person
    Given I am logged in as a user with the "Administrator" role
    And I am on "media/add/person"
    And I click "Beschreibung"
    And I fill in the following:
      | Name               | testPerson1234 |
      | Öffentlicher Titel | testPerson1234 |
    Then I scroll to bottom
    And I press the "Speichern" button
    And I am on "/admin/content/media"
    Then I should see text matching "testPerson1234" after a while

  Scenario: I am creating an instagram post
    Given I am logged in as a user with the "Administrator" role
    And I am on "media/add/instagram"
    And I fill in the following:
      | Name               | testInstagram1234 |
      | Öffentlicher Titel | testInstagram1234 |
      | Instagram post     | testInstagram1234 |
    Then I scroll to bottom
    And I press the "Speichern" button
    And I am on "/admin/content/media"
    Then I should see text matching "testInstagram1234" after a while



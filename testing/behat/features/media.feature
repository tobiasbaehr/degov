@api @drupal
Feature: deGov - Media creation

  Scenario: I am creating media address entity
    Given I am logged in as a user with the "Administrator" role
    And I am on "/media/add/address"
    And I fill in "test1234" for "Name"
    And I fill in "Bilker Straße 29" for "Street address"
    And I fill in "40213" for "Postal code"
    And I fill in "Düsseldorf" for "City"
    And I click "Allgemein"
    And I check the box "Mediathek"
    And I submit a form by id "media-address-add-form"
    And I should not see "Error"
    And I should not see "Warning"
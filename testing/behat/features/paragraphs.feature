@api @drupal
Feature: deGov - Paragraphs

  Scenario: Banner paragraph should contain expected fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/paragraphs_type/image_header/fields"
    Then I should see the fields list with exactly 2 entries
    And I should see text matching "field_override_caption"
    And I should see text matching "field_header_media"

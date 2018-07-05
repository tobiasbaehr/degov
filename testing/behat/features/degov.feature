@api @drupal
Feature: Test deGov

  Scenario: I am on the frontpage
    Given I am on "/"
    Then I should not see text matching "Error"
    Then I should not see text matching "Warning"

  Scenario: I am installing the degov user roles module
    Given I am logged in as a user with the "Administrator" role
    Then I am on "/admin/modules"
    And I check checkbox with id "edit-modules-degov-users-roles-enable"
    Then I press the "Installieren" button
    And I wait 5 seconds
    Then I should be on "admin/modules/list/confirm"
    And I click "#edit-submit"
    And I wait 5 seconds
    Then I am on "/admin/people/roles"
    And I should see "Chefredakteur"
    And I should see "Redakteur"
    And I should see "Benutzerverwaltung"



@api @drupal
Feature: Test deGov

  Scenario: I am on the frontpage
    Given I am on "/"
    Then I should not see text matching "Error"
    Then I should not see text matching "Warning"

  Scenario: I am installing the degov user roles module
    Given I am logged in as "administator"
    Then I am on "/admin/modules"
    And I check checkbox with id "edit-modules-degov-users-roles-enable"
    Then I press the "Installieren" button
    Then I press the "Weiter" button
    Then I am on "/admin/people/roles"
    And I should see "Chefredakteur"
    And I should see "Redakteur"
    And I should see "Benutzerverwaltung"



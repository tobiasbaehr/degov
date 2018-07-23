@api @drupal
Feature: Test deGov

  Scenario: I am on the frontpage
    Given I am on "/"
    Then I should not see text matching "Error"
    Then I should not see text matching "Warning"

  Scenario: I am installing the degov user roles module
    Given I am logged in as a user with the "Administrator" role
    Then I am installing the "degov_users_roles" module
    Then I am on "/admin/people/roles"
    And I should see "Chefredakteur"
    And I should see "Redakteur"
    And I should see "Benutzerverwaltung"

  Scenario: "I am creating content with role 'Redakteur' and publishing it with the 'Chefredakteur' role"
    Given I am logged in as a user with the "Redakteur" role
    Then I am on "/node/add/normal_page"
    And I fill in "Test1234" for "Titel"
    And I should see the option "draft" in "edit-moderation-state-0-state"
    And I should see the option "needs_review" in "edit-moderation-state-0-state"
    And I should not see the option "publish" in "edit-moderation-state-0-state"
    And I should not see the option "archiv" in "edit-moderation-state-0-state"
    And I should not see the option "restore" in "edit-moderation-state-0-state"
    And I select "Draft" in "Save as:"
    And I press the "Speichern" button
    Then I am logged in as a user with the "Chefredakteur" role
    And I am on "/node/add/"
    And I click "Test1234"
    And I click "Neuer Entwurf"
    And I should see the option "draft" in "edit-moderation-state-0-state"
    And I should see the option "needs_review" in "edit-moderation-state-0-state"
    And I should see the option "publish" in "edit-moderation-state-0-state"
    And I should not see the option "archiv" in "edit-moderation-state-0-state"
    And I should not see the option "restore" in "edit-moderation-state-0-state"
    And I select "publish" in "edit-moderation-state-0-state"
    And I press the "Speichern" button
    Then I am on "/node/add/"
    And I click "Test1234"
    And I click "Neuer Entwurf"
    And I should see the option "draft" in "edit-moderation-state-0-state"
    And I should see the option "needs_review" in "edit-moderation-state-0-state"
    And I should see the option "publish" in "edit-moderation-state-0-state"
    And I should see the option "archiv" in "edit-moderation-state-0-state"
    And I should see the option "restore" in "edit-moderation-state-0-state"


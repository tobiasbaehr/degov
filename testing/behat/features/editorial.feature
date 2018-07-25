@api @drupal
Feature: Test deGov - Content moderation and workflow

  Background:
    Given users:
      | name    | mail               | pass     | roles   |
      | editor  | editor@example.com | password | editor  |
      | manager | editor@example.com | password | manager |

  Scenario: "I am creating content with role 'Redakteur' and publisheding it with the 'Chefredakteur' role"
    Given I am logged in as a user with the "Administrator" role
    Then I am installing the "degov_node_normal_page" module
    Then I am on "/admin/config/workflow/workbench_access/sections/section/users"
    And I fill in "edit-editors-add" with "editor"
    And I press the "Hinzufügen" button
    And I fill in "edit-editors-add" with "manager"
    And I press the "Hinzufügen" button
    Then I am logged in as "editor"
    And I am on "/node/add/normal_page"
    And I fill in "Titel" with "Test1234"
    And I should see the option "draft" in "edit-moderation-state-0-state"
    And I should see the option "needs_review" in "edit-moderation-state-0-state"
    And I should not see the option "published" in "edit-moderation-state-0-state"
    And I should not see the option "archiv" in "edit-moderation-state-0-state"
    And I should not see the option "restore" in "edit-moderation-state-0-state"
    And I select "draft" in "edit-moderation-state-0-state"
    And I press the "Speichern" button
    And I open node edit form by node title "Test1234"
    And I should see the option "draft" in "edit-moderation-state-0-state"
    And I should see the option "needs_review" in "edit-moderation-state-0-state"
    And I should not see the option "published" in "edit-moderation-state-0-state"
    And I should not see the option "archiv" in "edit-moderation-state-0-state"
    And I should not see the option "restore" in "edit-moderation-state-0-state"
    And I select "needs_review" in "edit-moderation-state-0-state"
    And I press the "Speichern" button
    Then I am logged in as "manager"
    And I open node edit form by node title "Test1234"
    And I should see the option "draft" in "edit-moderation-state-0-state"
    And I should not see the option "needs_review" in "edit-moderation-state-0-state"
    And I should see the option "published" in "edit-moderation-state-0-state"
    And I should not see the option "archiv" in "edit-moderation-state-0-state"
    And I should not see the option "restore" in "edit-moderation-state-0-state"
    And I select "published" in "edit-moderation-state-0-state"
    And I press the "Speichern" button
    And I open node edit form by node title "Test1234"
    And I should see the option "draft" in "edit-moderation-state-0-state"
    And I should not see the option "needs_review" in "edit-moderation-state-0-state"
    And I should see the option "published" in "edit-moderation-state-0-state"
    And I should see the option "archived" in "edit-moderation-state-0-state"


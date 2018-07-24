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

  Scenario: I am visiting the media edit pages #2986289
    Given I am on "/admin/structure/media/manage/address"
    Then I am on "/admin/structure/media/manage/gallery"
    Then I am on "/admin/structure/media/manage/video_upload"
    Then I am on "/admin/structure/media/manage/person"
    Then I am on "/admin/structure/media/manage/audio"

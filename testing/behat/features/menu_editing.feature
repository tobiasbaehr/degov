@api @drupal @menu_editing
Feature: deGov - Menu editing

  Background:
    Given I proof that Drupal module "link_attributes" is installed

  Scenario: Checking CSS class specification in menu items
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/menu/manage/main/add"
    Then I should see HTML content matching "link[0][options][attributes][class]"

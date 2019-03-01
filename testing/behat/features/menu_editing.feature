@api @drupal
Feature: deGov - Menu editing

  Background:
    Given I proof that Drupal module "link_attributes" is installed

  Scenario: Checking CSS class specification in menu items
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/menu/manage/main/add"
    Then I should see HTML content matching "link[0][options][attributes][class]"

  Scenario: I check that menu items can contain Media file links and that they are resolved
    Given I have dismissed the cookie banner if necessary
    And I am on the homepage
    Then I should not see HTML content matching "/sites/default/files/degov_demo_content/dummy.pdf"
    Then I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/menu/manage/main/add"
    And I fill in "edit-title-0-value" with "Dummy"
    And I enter the menu placeholder for a "document" media file in "#edit-link-0-uri"
    And I scroll to the "#edit-submit" element
    And I press button with label "Save" via translated text
    And I am on the homepage
    Then I should see HTML content matching "/sites/default/files/degov_demo_content/dummy.pdf" after a while

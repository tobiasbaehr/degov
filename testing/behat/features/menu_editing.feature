@api @drupal
Feature: deGov - Menu editing

  Background:
    Given I proof that the following Drupal modules are installed:
      | link_attributes             |
      | media_file_links            |

  Scenario: Checking CSS class specification in menu items
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/menu/manage/main/add"
    Then I should see HTML content matching "link[0][options][attributes][class]"

  Scenario: I check that menu items can contain Media file links and that they are resolved
    Given I configure and place the main menu block
    And I have dismissed the cookie banner if necessary
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

  Scenario: I check that menu item autocomplete suggestions include Media entities
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/menu/manage/main/add"
    And I should not see HTML content matching "clita kasd gubergren no sea [Dokument, dummy.pdf]"
    And I should not see HTML content matching "fa-file-pdf"
    And I fill in "edit-link-0-uri" with "sea"
    And I trigger the "keydown" event on "#edit-link-0-uri"
    Then I should see HTML content matching "clita kasd gubergren no sea [Dokument, dummy.pdf]" after a while
    And I should see HTML content matching "fa-file-pdf"

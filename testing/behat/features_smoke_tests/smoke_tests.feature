@api @drupal
Feature: deGov - Smoke tests

#  Scenario: Content administration overview contains necessary items
#    Given I am logged in as user with the account details from Behat config file
#    And I should be on "/user/1"
#    And I am on "/admin/content"
#    Then I should see text matching "Title" via translated text
#    And I should see text matching "Content type" via translated text
#    And I should see text matching "Published status" via translated text
#    And I should see text matching "Language" via translated text
#    And I should see text matching "Action" via translated text
#    And I should see text matching "Content" via translated text
#    And I should see text matching "Files" via translated text
#    And I should see text matching "Newsletter issues" via translated text
#    And I should see text matching "Media" via translated text
#
#  Scenario: Block administration page contains necessary items
#    Given I am logged in as user with the account details from Behat config file
#    And I am on "/admin/structure/block"
#    And I should see text matching "Header" via translated text
#    And I should see text matching "Top navigation"
#    And I should see text matching "Facets"
#
#  Scenario: User administration page contains necessary items
#    Given I am logged in as user with the account details from Behat config file
#    And I am on "/admin/people"
#    And I should see text matching "Header" via translated text
#    And I should see text matching "Top navigation" via translated text
#    And I should see text matching "Facets" via translated text
#
#  Scenario: Paragraphs types administration page contains necessary items
#    Given I am logged in as user with the account details from Behat config file
#    And I am on "/admin/structure/paragraphs_type"
#    And I should see HTML content matching "Icon"
#    And I should see text matching "Label" via translated text
#    And I should see text matching "Machine name" via translated text
#    And I should see text matching "Description" via translated text
#    And I should see text matching "Operations" via translated text
#
#  Scenario: Media types administration page contains necessary items
#    Given I am logged in as user with the account details from Behat config file
#    And I am on "/admin/structure/media"
#    And I should see HTML content matching "Name"
#    And I should see text matching "Description" via translated text
#    And I should see text matching "Operations" via translated text
#    And I should see text matching "Add media type" via translated text

  Scenario: Taxonomy administration page contains necessary items
    Given I am on "/admin/structure/taxonomy"
    And I should see text matching "Area" via translated text
    And I should see text matching "Topic" via translated text
    And wait 10 seconds
    And I should see HTML content matching "Copyright"
    And I should see HTML content matching "Content type"
    And I should see HTML content matching "Medien Barrierefreiheit"
    And I should see HTML content matching "Medien Sprachen"
    And I should see HTML content matching "Schlagworte"

#  Scenario: Content types administration page contains necessary items
#    Given I am on "/admin/structure/types"
#    And I should see HTML content matching "Blog-Artikel"
#    And I should see HTML content matching "FAQ"
#    And I should see HTML content matching "Inhaltsseite"
#    And I should see HTML content matching "Pressemitteilung"
#    And I should see HTML content matching "Veranstaltung"
#    And I should see text matching "Newsletter Issue" via translated text

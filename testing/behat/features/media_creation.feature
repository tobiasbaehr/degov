@api @drupal @javascript @lala
Feature: deGov - Media creation

  Background:
    Given I proof that the following Drupal modules are installed:
      | degov_media_video      |
      | degov_node_overrides   |
      | degov_node_normal_page |
      | degov_paragraph_text   |
      | degov_media_image      |
      | degov_media_gallery      |
    Given I am installing the "degov_paragraph_media_reference" module

#  Scenario: I am creating a address media entity
#    Given I am logged in as a user with the "Administrator" role
#    And I am on "/media/add/address"
#    And I should see text matching "Adresse hinzufügen"
#    Then I fill in "Example address" for "Name"
#    And I fill in "Example address public" for "Öffentlicher Titel"
#    And I should see HTML content matching "Straße" after a while
#    And I fill in "Bilker Straße 29" for "Straße"
#    And I fill in "40213" for "Postleitzahl"
#    And I fill in "Düsseldorf" for "Stadt"
#    And I click "General" via translation
#    And I check the box "Mediathek"
#    Then I scroll to bottom
#    And I press button with label "Save" via translated text
#    Then I should see text matching "Example address" after a while
#
#  Scenario: I am creating a quote media entity
#    Given I am logged in as a user with the "Administrator" role
#    And I am on "media/add/citation"
#    And I click "Beschreibung"
#    Then I should see text matching "Öffentlicher Titel" after a while
#    And I fill in the following:
#      | Name               | Example quote              |
#      | Öffentlicher Titel | Example quote public       |
#      | Text               | Example text. Lorem ipsum. |
#    Then I scroll to bottom
#    And I press button with label "Save" via translated text
#    And I am on "/admin/content/media"
#    Then I should see text matching "Example quote" after a while
#
#  Scenario: I am creating a person media entity
#    Given I am logged in as a user with the "Administrator" role
#    And I am on "media/add/person"
#    And I click "Beschreibung"
#    Then I should see text matching "Öffentlicher Titel" after a while
#    And I fill in the following:
#      | Name               | Example person        |
#      | Öffentlicher Titel | Example person public |
#    Then I scroll to bottom
#    And I press button with label "Save" via translated text
#    And I am on "/admin/content/media"
#    Then I should see text matching "Example person" after a while
#
#  Scenario: I am creating a video media entity
#    Given I am logged in as an "Administrator"
#    When I go to "/media/add/video"
#    And I fill in the following:
#      | Öffentlicher Titel | Example video                               |
#      | Name               | Example video public                        |
#      | Video-URL          | https://www.youtube.com/watch?v=qREKP9oijWI |
#      | Quelle             | youtube                                     |
#    And I scroll to element with id "edit-submit"
#    And I press button with label "Save" via translated text
#    And I should not see text matching "Es konnte kein Video-Provider gefunden werden, der den angegeben URL verarbeiten kann."
#    And I should see "Video Example video public wurde erstellt."
#
#  Scenario: I am creating an Instagram media entity
#    Given I am logged in as an "Administrator"
#    When I go to "media/add/instagram"
#    Then I should see text matching "Öffentlicher Titel" after a while
#    And I fill in the following:
#      | Name               | Example Instagram                      |
#      | Öffentlicher Titel | Example Instagram public               |
#      | Instagram post     | https://www.instagram.com/p/JUvux9iFRY |
#    And I scroll to element with id "edit-submit"
#    And I press button with label "Save" via translated text
#    Then I should not see "ist erforderlich."
#    And I should see "Example Instagram wurde erstellt."
#
#  Scenario: I am creating an media image entity
#    Given I am logged in as an "Administrator"
#    And I am on "/media/add/image"
#    And I fill in "Name" with "Test1234"
#    And I fill in "Öffentlicher Titel" with "Test1234"
#    And I attach the file "/opt/atlassian/pipelines/agent/build/degov-project/docroot/profiles/contrib/degov/testing/fixtures/images/dummy.png" to "edit-image-0-upload"
#    And I should see HTML content matching "Alternative Bildbeschreibung" after a while
#    And I fill in "Alternative Bildbeschreibung" with "Test1234"
#    And I scroll to element with id "edit-submit"
#    And I press button with label "Save" via translated text
#    Then I should not see "ist erforderlich."
#    And I should see "wurde erstellt."
  Scenario: I am creating an media gallery entity
    Given I am logged in as an "Administrator"
    And I am on "/media/add/gallery"
    And I press the "Ich stimme zu" button
    And I fill in "Name" with "Test1234"
    And I fill in "Öffentlicher Titel" with "Test1234"
    And I switch to the "entity_browser_iframe_media_browser" frame
    And I click "Hochladen"
    And wait 2 seconds
    And I attach the file "/opt/atlassian/pipelines/agent/build/degov-project/docroot/profiles/contrib/degov/testing/fixtures/images/dummy.png" to "Datei"
    And I should see HTML content matching "Alternative Bildbeschreibung" after a while
    And I fill in "entity[field_title][0][value]" with "Test1234"
    And I fill in "entity[name][0][value]" with "Test1234"
    And I fill in "entity[image][0][alt]" with "Test1234"
    And I scroll to bottom
    And I press the "Auswählen" button
    And I scroll to top
    And I press the "Use selected" button
    And I return to the window
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I should not see "ist erforderlich."



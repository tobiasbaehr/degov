@api @drupal @javascript
Feature: deGov - Media creation

  Background:
    Given I proof that the following Drupal modules are installed:
      | degov_media_video      |
      | degov_node_overrides   |
      | degov_node_normal_page |
      | degov_paragraph_text   |
      | degov_media_image      |
      | degov_media_gallery    |
    Given I am installing the "degov_paragraph_media_reference" module

  Scenario: I am creating a address media entity
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "Administrator" role
    And I am on "/media/add/address"
    Then I fill in "Example address" for "Name"
    And I fill in "Example address public" for "Öffentlicher Titel"
    And I should see HTML content matching "Straße" after a while
    And I fill in "Bilker Straße 29" for "Straße"
    And I fill in "40213" for "Postleitzahl"
    And I fill in "Düsseldorf" for "Stadt"
    And I click "General" via translation
    And I check the box "Mediathek"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text

  Scenario: I proof that longitude and latitude has been generated automatically
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "Administrator" role
    And I open address medias edit form from latest media with title "Example address public"
    And I should see HTML content matching "51.220793"
    And I should see HTML content matching "6.772623"

  Scenario: I am creating a quote media entity
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "Administrator" role
    And I am on "media/add/citation"
    And I click "Beschreibung"
    Then I should see text matching "Öffentlicher Titel" after a while
    And I fill in the following:
      | Name               | Example quote              |
      | Öffentlicher Titel | Example quote public       |
      | Text               | Example text. Lorem ipsum. |
    Then I scroll to bottom
    And I press button with label "Save" via translated text
    And I am on "/admin/content/media"
    Then I should see text matching "Example quote" after a while

  Scenario: I am creating a person media entity
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "Administrator" role
    And I am on "media/add/person"
    And I click "Beschreibung"
    Then I should see text matching "Öffentlicher Titel" after a while
    And I fill in the following:
      | Name               | Example person        |
      | Öffentlicher Titel | Example person public |
    Then I scroll to bottom
    And I press button with label "Save" via translated text
    And I am on "/admin/content/media"
    Then I should see text matching "Example person" after a while

  Scenario: I am creating a video media entity
    Given I have dismissed the cookie banner if necessary
    And I am logged in as an "Administrator"
    When I go to "/media/add/video"
    And I fill in the following:
      | Öffentlicher Titel     | Example video                               |
      | Name                   | Example video public                        |
      | Video-URL              | https://www.youtube.com/watch?v=qREKP9oijWI |
      | Quelle                 | youtube                                     |
    And I fill in "edit-field-media-publish-date-0-value-date" with "111118"
    And I fill in "edit-field-media-publish-date-0-value-time" with "000000AM"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    And I should not see text matching "Es konnte kein Video-Provider gefunden werden, der den angegeben URL verarbeiten kann."
    And I should see "Video Example video public wurde erstellt."

  Scenario: I am creating an Instagram media entity
    Given I have dismissed the cookie banner if necessary
    And I am logged in as an "Administrator"
    When I go to "media/add/instagram"
    Then I should see text matching "Öffentlicher Titel" after a while
    And I fill in the following:
      | Name               | Example Instagram                      |
      | Öffentlicher Titel | Example Instagram public               |
      | Instagram post     | https://www.instagram.com/p/JUvux9iFRY |
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I should not see "ist erforderlich."
    And I should see "Example Instagram wurde erstellt."

  Scenario: I try to create an image just to check if the copyright field is emptied when I set the image to be royalty free
    Given I have dismissed the cookie banner if necessary
    And I am logged in as an "Administrator"
    And I am on "/media/add/image"
    And I choose "Beschreibung" from tab menu
    And I fill in "Copyright" with "Test1234"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I should see "ist erforderlich."
    And I choose "Beschreibung" from tab menu
    And I should see 1 form element with the label "Copyright" and the value "Test1234"
    And I check checkbox with id "edit-field-royalty-free-value"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I should see "ist erforderlich."
    And I choose "Beschreibung" from tab menu
    And I should see 0 form element with the label "Copyright" and the value "Test1234"

  Scenario: Check if media full display is working if field_include_search is unchecked
    Given I am installing the "degov_demo_content" module
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    And I open media edit form by media name "demo image with a fixed title"
    And I choose "Allgemein" from tab menu
    And I uncheck the box "edit-field-include-search-value"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    And I am on "/demo-image-fixed-title"
    And I should not see "Mitglied seit"
    And I should see HTML content matching "image--full"

  Scenario: I verify that a deleted Media's file is actually gone
    Given I am installing the "degov_demo_content" module
    And I have dismissed the cookie banner if necessary
    Given I am on "/"
    And I am logged in as a user with the "administrator" role
    Then I am on "/admin/content/media"
    Then I am on "/image-will-be-deleted"
    And I should see HTML content matching "/sites/default/files/degov_demo_content/taneli-lahtinen-1058552-unsplash.jpg"
    Then I am on "/sites/default/files/degov_demo_content/taneli-lahtinen-1058552-unsplash.jpg"
    Then I open medias delete url by title "This image will be deleted"
    And I click by CSS id "edit-submit"
    Then I am on "/sites/default/files/degov_demo_content/taneli-lahtinen-1058552-unsplash.jpg?1"
    And I should see HTML content matching "404 Not Found"

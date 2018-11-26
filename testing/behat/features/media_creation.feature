@api @drupal
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
    Given I am logged in as a user with the "Administrator" role
    And I am on "/media/add/address"
    And I should see text matching "Adresse hinzufügen"
    Then I fill in "Example address" for "Name"
    And I fill in "Example address public" for "Öffentlicher Titel"
    And I should see HTML content matching "Straße" after a while
    And I fill in "Bilker Straße 29" for "Straße"
    And I fill in "40213" for "Postleitzahl"
    And I fill in "Düsseldorf" for "Stadt"
    And I click "General" via translation
    And I check the box "Mediathek"
    Then I scroll to bottom
    And I press button with label "Save" via translated text
    Then I should see text matching "Example address" after a while
    Then I am on "/node/add/normal_page"
    And I fill in "Example normal page title" for "Titel"
    And I click by selector ".vertical-tabs__menu-item.last a" via JavaScript
    And I click by selector "#edit-field-content-paragraphs button" via JavaScript
    Then I scroll to bottom
    And I press button with label "Save" via translated text
    And I am on "/admin/content"
    Then I should see text matching "Example normal page title" after a while

  Scenario: I am creating a quote media entity
    Given I am logged in as a user with the "Administrator" role
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
    Given I am logged in as a user with the "Administrator" role
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

  Scenario: I am creating a video upload media entity
    Given I am logged in as an "Administrator"
    And I am on "/media/add/video_upload"
    And I fill in the following:
      | Name               | Video Example |
      | Öffentlicher Titel | Video Example |
    And I choose "Allgemein" from tab menu
    And I check the box "edit-field-include-search-value"
    And I choose "Medien" from tab menu
    And I attach the file "bokeh-video-of-leaves.mp4" to "files[field_video_upload_mp4_0]"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    And I dump the HTML
    And I should see text matching "Video Upload Video Example wurde erstellt."

  Scenario: I am creating a video media entity
    Given I am logged in as an "Administrator"
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
    Given I am logged in as an "Administrator"
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

  Scenario: I am creating an media image entity with copyright
    Given I am logged in as an "Administrator"
    And I have dismissed the cookie banner if necessary
    And I am on "/media/add/image"
    And I fill in "Name" with "Test1234"
    And I fill in "edit-field-media-publish-date-0-value-date" with "111118"
    And I fill in "edit-field-media-publish-date-0-value-time" with "000000AM"
    And I fill in "Öffentlicher Titel" with "Test1234"
    And I attach the file "humberto-chavez-1058365-unsplash.jpg" to "edit-image-0-upload"
    And I should see HTML content matching "Alternative Bildbeschreibung" after a while
    And I fill in "Alternative Bildbeschreibung" with "Test1234"
    And I choose "Beschreibung" from tab menu
    And I fill in "Copyright" with "Test1234"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I should not see "ist erforderlich."
    And I should see "wurde erstellt."

  Scenario: I try and fail to create a licensed image without copyright info
    Given I am logged in as an "Administrator"
    And I have dismissed the cookie banner if necessary
    And I am on "/media/add/image"
    And I fill in "Name" with "Test1234"
    And I fill in "edit-field-media-publish-date-0-value-date" with "111118"
    And I fill in "edit-field-media-publish-date-0-value-time" with "000000AM"
    And I fill in "Öffentlicher Titel" with "Test1234"
    And I attach the file "humberto-chavez-1058365-unsplash.jpg" to "edit-image-0-upload"
    And I should see HTML content matching "Alternative Bildbeschreibung" after a while
    And I fill in "Alternative Bildbeschreibung" with "Test1234"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I should see "ist erforderlich."

  Scenario: I try to create an image just to check if the copyright field is emptied when I set the image to be royalty free
    Given I am logged in as an "Administrator"
    And I have dismissed the cookie banner if necessary
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
    And I should see 1 form element with the label "Copyright" and the value ""

  Scenario: I am creating an media image entity without copyright
    Given I am logged in as an "Administrator"
    And I have dismissed the cookie banner if necessary
    And I am on "/media/add/image"
    And I fill in "Name" with "Test1234"
    And I fill in "edit-field-media-publish-date-0-value-date" with "111118"
    And I fill in "edit-field-media-publish-date-0-value-time" with "000000AM"
    And I fill in "Öffentlicher Titel" with "Test1234"
    And I attach the file "humberto-chavez-1058365-unsplash.jpg" to "edit-image-0-upload"
    And I should see HTML content matching "Alternative Bildbeschreibung" after a while
    And I fill in "Alternative Bildbeschreibung" with "Test1234"
    And I choose "Beschreibung" from tab menu
    And I check checkbox with id "edit-field-royalty-free-value"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I should not see "ist erforderlich."
    And I should see "wurde erstellt."

  Scenario: I am creating an media gallery entity
    Given I am on "/"
    And I have dismissed the cookie banner if necessary
    And I am logged in as an "Administrator"
    And I am on "/media/add/gallery"
    And I fill in "Name" with "Test1234"
    And I fill in "edit-field-media-publish-date-0-value-date" with "111118"
    And I fill in "edit-field-media-publish-date-0-value-time" with "000000AM"
    And I fill in "Öffentlicher Titel" with "Test1234"
    And I focus on the Iframe with ID "entity_browser_iframe_media_browser"
    And I should see HTML content matching "Hochladen" after a while
    And I click "Hochladen"
    And I attach the file "humberto-chavez-1058365-unsplash.jpg" to "edit-input-file"
    And I should see HTML content matching "Alternative Bildbeschreibung" after a while
    And I fill in "entity[field_title][0][value]" with "Test1234"
    And I fill in "entity[name][0][value]" with "Test1234"
    And I fill in "entity[image][0][alt]" with "Test1234"
    And I fill in "entity[field_copyright][0][target_id]" with "Test1234"
    And I press the "Auswählen" button
    And I press the "Use selected" button
    And I go back to the main window
    And I should see the details container titled "Current selections" with entries after a while
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I should not see "ist erforderlich."

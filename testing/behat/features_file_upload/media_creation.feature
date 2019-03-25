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

  Scenario: I am creating a video upload media entity
    Given I have dismissed the cookie banner if necessary
    And I am logged in as an "Administrator"
    And I am on "/media/add/video_upload"
    And I fill in the following:
      | Name               | Video Example |
      | Öffentlicher Titel | Video Example |
    And I choose "Allgemein" from tab menu
    And I check the box "edit-field-include-search-value"
    And I choose "Medien" from tab menu
    And I attach the file "/opt/atlassian/pipelines/agent/build/modules/degov_demo_content/fixtures/bokeh-video-of-leaves.mp4" to "files[field_video_upload_mp4_0]"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    And I should see text matching "Video Upload Video Example wurde erstellt."

  Scenario: I am creating an media image entity with copyright
    Given I have dismissed the cookie banner if necessary
    And I am logged in as an "Administrator"
    And I am on "/media/add/image"
    And I fill in "Name" with "Test1234"
    And I fill in "edit-field-media-publish-date-0-value-date" with "111118"
    And I fill in "edit-field-media-publish-date-0-value-time" with "000000AM"
    And I fill in "Öffentlicher Titel" with "Test1234"
    And I should see text matching "320x320"
    And I attach the file "humberto-chavez-1058365-unsplash.jpg" to "edit-image-0-upload"
    And I should see text matching "Alternative text" via translation after a while
    And I fill in "Alternative text" via translated text with "Test1234"
    And I choose "Beschreibung" from tab menu
    And I fill in "Copyright" with "Test1234"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I should not see "ist erforderlich."
    And I should see "wurde erstellt."

  Scenario: I try and fail to create a licensed image without copyright info
    Given I have dismissed the cookie banner if necessary
    And I am logged in as an "Administrator"
    And I am on "/media/add/image"
    And I fill in "Name" with "Test1234"
    And I fill in "edit-field-media-publish-date-0-value-date" with "111118"
    And I fill in "edit-field-media-publish-date-0-value-time" with "000000AM"
    And I fill in "Öffentlicher Titel" with "Test1234"
    And I should see text matching "320x320"
    And I attach the file "humberto-chavez-1058365-unsplash.jpg" to "edit-image-0-upload"
    And I should see text matching "Alternative text" via translation after a while
    And I fill in "Alternative text" via translated text with "Test1234"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I should see "ist erforderlich."

  Scenario: I am creating an media image entity without copyright
    Given I have dismissed the cookie banner if necessary
    And I am logged in as an "Administrator"
    And I am on "/media/add/image"
    And I fill in "Name" with "Test1234"
    And I fill in "edit-field-media-publish-date-0-value-date" with "111118"
    And I fill in "edit-field-media-publish-date-0-value-time" with "000000AM"
    And I fill in "Öffentlicher Titel" with "Test1234"
    And I should see text matching "320x320"
    And I attach the file "humberto-chavez-1058365-unsplash.jpg" to "edit-image-0-upload"
    And I should see text matching "Alternative text" via translation after a while
    And I fill in "Alternative text" via translated text with "Test1234"
    And I choose "Beschreibung" from tab menu
    And I check checkbox with id "edit-field-royalty-free-value"
    And I scroll to element with id "edit-submit"
    And I press button with label "Save" via translated text
    Then I should not see "ist erforderlich."
    And I should see "wurde erstellt."

  Scenario: I try to create an image from the CKEditor entity embed dialog to check if the copyright field is present and can be emptied
    Given I have dismissed the cookie banner if necessary
    And I am logged in as an "Administrator"
    And I am on "/node/add/faq"
    And I click by CSS class "cke_button__media_browser"
    Then I should see HTML content matching "medien zum Einbetten auswählen" after a while
    And I focus on the Iframe with ID "entity_browser_iframe_media_browser"
    And I click "Hochladen"
    Then I should see HTML content matching "Datei" after a while
    And I attach the file "humberto-chavez-1058365-unsplash.jpg" to "edit-input-file"
    Then I should see HTML content matching "Name" after a while
    And I fill in "Copyright" with "Test1234"
    And I scroll to element with id "edit-submit"
    And I press the "Place" button
    Then I should see text matching "ist erforderlich." after a while
    And I should see 1 form element with the label "Copyright" and the value "Test1234"
    And I click by selector "input[data-drupal-selector=edit-entity-field-royalty-free-value]" via JavaScript
    And I scroll to element with id "edit-submit"
    And I press the "Place" button
    Then I should see "ist erforderlich."
    And I verify that field "#edit-entity-field-copyright-0-target-id" has the value ""

  Scenario: I am creating an media gallery entity
    Given I have dismissed the cookie banner if necessary
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
    And I should see text matching "Alternative text" via translation after a while
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
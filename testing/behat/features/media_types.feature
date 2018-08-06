@api @drupal
Feature: nrwGOV media types
  Scenario: Checking available Media Types
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media"
    Then I should see text matching "Adresse"
    And I should see text matching "Audio"
    And I should see text matching "Bild"
    And I should see text matching "Bildergalerie"
    And I should see text matching "Dokument"
    And I should see text matching "Instagram"
    And I should see text matching "Kontakt"
    And I should see text matching "Person"
    And I should see text matching "Tweet"
    And I should see text matching "Video"
    And I should see text matching "Video Upload"
    And I should see text matching "Zitat"

  Scenario: Media type Adress has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media/manage/address/fields"
    Then I should see text matching "field_address_address"
    And I should see text matching "field_address_email"
    And I should see text matching "field_address_fax"
    And I should see text matching "field_media_generic"
    And I should see text matching "field_address_location"
    And I should see text matching "field_include_search"
    And I should see text matching "field_media_in_library"
    And I should see text matching "field_tags"
    And I should see text matching "field_address_phone"
    And I should see text matching "field_address_title"
    And I should see text matching "field_title"

  Scenario: Media type Audio has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media/manage/audio/fields"
    Then I should see text matching "field_audio_caption"
    And I should see text matching "field_description"
    And I should see text matching "field_copyright"
    And I should see text matching "field_media_generic_1"
    And I should see text matching "field_allow_download"
    And I should see text matching "field_media_duration"
    And I should see text matching "field_include_search"
    And I should see text matching "field_audio_mp3"
    And I should see text matching "field_audio_ogg"
    And I should see text matching "field_media_in_library"
    And I should see text matching "field_tags"
    And I should see text matching "field_media_transcription"
    And I should see text matching "field_audio_preview"
    And I should see text matching "field_title"

  Scenario: Media type Image has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media/manage/image/fields"
    Then I should see text matching "field_description"
    And I should see text matching "field_image_caption"
    And I should see text matching "field_image_width"
    And I should see text matching "field_copyright"
    And I should see text matching "field_image_date"
    And I should see text matching "field_allow_download"
    And I should see text matching "field_image_height"
    And I should see text matching "image"
    And I should see text matching "field_include_search"
    And I should see text matching "field_image_mime"
    And I should see text matching "field_media_in_library"
    And I should see text matching "field_tags"
    And I should see text matching "field_subtitle"
    And I should see text matching "field_title"

  Scenario: Media type Gallery has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media/manage/gallery/fields"
    Then I should see text matching "field_description"
    And I should see text matching "field_media_generic_5"
    And I should see text matching "field_gallery_images"
    And I should see text matching "field_include_search"
    And I should see text matching "field_tags"
    And I should see text matching "field_gallery_title"
    And I should see text matching "field_media_in_library"
    And I should see text matching "field_title"

  Scenario: Media type Document has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media/manage/document/fields"
    Then I should see text matching "field_document"
    And I should see text matching "field_include_search"
    And I should see text matching "field_media_in_library"
    And I should see text matching "field_tags"
    And I should see text matching "field_title"

  Scenario: Media type Instragram has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media/manage/instagram/fields"
    Then I should see text matching "embed_code"
    And I should see text matching "field_include_search"
    And I should see text matching "field_tags"
    And I should see text matching "field_media_in_library"
    And I should see text matching "field_title"

  Scenario: Media type Kontakt has all requried fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media/manage/contact/fields"
    Then I should see text matching "field_contact_email"
    And I should see text matching "field_contact_fax"
    And I should see text matching "field_media_generic_3"
    And I should see text matching "field_contact_image"
    And I should see text matching "field_include_search"
    And I should see text matching "field_contact_title"
    And I should see text matching "field_contact_position"
    And I should see text matching "field_media_in_library"
    And I should see text matching "field_tags"
    And I should see text matching "field_contact_tel"
    And I should see text matching "field_title"

  Scenario: Media Type Person has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media/manage/person/fields"
    Then I should see text matching "field_person_image"
    And I should see text matching "field_media_generic_6"
    And I should see text matching "field_person_info"
    And I should see text matching "field_include_search"
    And I should see text matching "field_media_in_library"
    And I should see text matching "field_tags"
    And I should see text matching "field_title"

  Scenario: Media Type Tweet has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media/manage/tweet/fields"
    Then I should see text matching "field_include_search"
    And I should see text matching "field_tags"
    And I should see text matching "embed_code"
    And I should see text matching "field_media_in_library"
    And I should see text matching "field_title"

  Scenario: Media type video has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media/manage/video/fields"
    Then I should see text matching "field_description"
    And I should see text matching "field_video_caption"
    And I should see text matching "field_copyright"
    And I should see text matching "field_media_duration"
    And I should see text matching "field_include_search"
    And I should see text matching "field_media_in_library"
    And I should see text matching "field_tags"
    And I should see text matching "field_media_transcription"
    And I should see text matching "field_media_video_embed_field"
    And I should see text matching "field_video_preview"
    And I should see text matching "field_title"

   Scenario: Media type Video-Upload has all required fields
     Given I am logged in as a user with the "administrator" role
     And I am on "/admin/structure/media/manage/video_upload/fields"
     Then I should see text matching "field_description"
     And I should see text matching "field_copyright"
     And I should see text matching "field_media_generic_8"
     And I should see text matching "field_allow_download"
     And I should see text matching "field_media_duration"
     And I should see text matching "field_include_search"
     And I should see text matching "field_video_upload_mp4"
     And I should see text matching "field_video_upload_ogg"
     And I should see text matching "field_media_in_library"
     And I should see text matching "field_tags"
     And I should see text matching "field_media_transcription"
     And I should see text matching "field_video_upload_subtitle"
     And I should see text matching "field_video_upload_caption"
     And I should see text matching "field_video_upload_preview"
     And I should see text matching "field_video_upload_webm"
     And I should see text matching "field_title"

  Scenario: Media type Citation has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/media/manage/citation/fields"
    Then I should see text matching "field_citation_date"
    And I should see text matching "field_media_generic_2"
    And I should see text matching "field_citation_image"
    And I should see text matching "field_include_search"
    And I should see text matching "field_media_in_library"
    And I should see text matching "field_tags"
    And I should see text matching "field_citation_text"
    And I should see text matching "field_citation_title"
    And I should see text matching "field_title"

@api @drupal @javascript
Feature: deGov - Content types

  Background:
    Given I proof that the following Drupal modules are installed:
      | degov_node_overrides      |
      | degov_node_normal_page    |
      | degov_paragraph_webform   |
      | degov_paragraph_slideshow |
      | degov_paragraph_header    |
    Given I am installing the following Drupal modules:
      | degov_node_event          |

  Scenario: Content type normal_page has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/types/manage/normal_page/fields"
    Then I should see text matching "field_header_paragraphs"
    And I should see text matching "field_tags"
    And I should see text matching "field_sidebar_right_paragraphs"
    And I should see text matching "field_social_media"
    And I should see text matching "field_teaser_image"
    And I should see text matching "field_teaser_text"
    And I should see text matching "field_teaser_title"
    And I should see text matching "field_teaser_sub_title"

  Scenario: Content type event has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/types/manage/event/fields"
    Then I should see text matching "field_event_date_end"
    And I should see text matching "field_content_paragraphs"
    And I should see text matching "field_internal_title"
    And I should see text matching "field_header_paragraphs"
    And I should see text matching "field_tags"
    And I should see text matching "field_sidebar_right_paragraphs"
    And I should see text matching "field_social_media"
    And I should see text matching "field_teaser_image"
    And I should see text matching "field_teaser_text"
    And I should see text matching "field_teaser_title"
    And I should see text matching "field_teaser_sub_title"

  Scenario: Content type normal page references specified content types in field_content_paragraphs
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page"
    And I press the "Add Paragraph" button
    And I assert dropbutton actions with css selector ".paragraphs-add-dialog" contains the following name-value pairs:
      | value                            | name                                              |
      | FAQ hinzufügen                   | field_content_paragraphs_faq_add_more             |
      | FAQ / Akkordion Liste hinzufügen | field_content_paragraphs_faq_list_add_more        |
      | Banner hinzufügen                | field_content_paragraphs_image_header_add_more    |
      | Medienreferenz hinzufügen        | field_content_paragraphs_media_reference_add_more |
      | Inhaltsreferenz hinzufügen       | field_content_paragraphs_node_reference_add_more  |
      | Slide hinzufügen                 | field_content_paragraphs_slide_add_more           |
      | Slideshow hinzufügen             | field_content_paragraphs_slideshow_add_more       |
      | Inhaltsreferenz hinzufügen       | field_content_paragraphs_node_reference_add_more  |
      | Text hinzufügen                  | field_content_paragraphs_text_add_more            |
      | Untertitel hinzufügen            | field_content_paragraphs_video_subtitle_add_more  |
      | Webform hinzufügen               | field_content_paragraphs_webform_add_more         |

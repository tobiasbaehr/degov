@api @drupal
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
    Given I proof content type "normal_page" has set the following fields for display:
      | field_teaser_title             |
      | field_teaser_text              |
      | content_moderation_control     |
      | field_header_paragraphs        |
      | field_sidebar_right_paragraphs |
      | title                          |

#  Scenario: Content type normal_page has all required fields
#    Given I am logged in as a user with the "administrator" role
#    And I am on "/admin/structure/types/manage/normal_page/fields"
#    Then I should see text matching "field_header_paragraphs"
#    And I should see text matching "field_tags"
#    And I should see text matching "field_sidebar_right_paragraphs"
#    And I should see text matching "field_social_media"
#    And I should see text matching "field_teaser_image"
#    And I should see text matching "field_teaser_text"
#    And I should see text matching "field_teaser_title"
#    And I should see text matching "field_teaser_sub_title"
#
#  Scenario: Content type event has all required fields
#    Given I am logged in as a user with the "administrator" role
#    And I am on "/admin/structure/types/manage/event/fields"
#    Then I should see text matching "field_event_date_end"
#    And I should see text matching "field_content_paragraphs"
#    And I should see text matching "field_internal_title"
#    And I should see text matching "field_header_paragraphs"
#    And I should see text matching "field_tags"
#    And I should see text matching "field_sidebar_right_paragraphs"
#    And I should see text matching "field_social_media"
#    And I should see text matching "field_teaser_image"
#    And I should see text matching "field_teaser_text"
#    And I should see text matching "field_teaser_title"
#    And I should see text matching "field_teaser_sub_title"

  Scenario: Content type normal page references specified content types in field_content_paragraphs
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page"
    And I assert dropbutton actions with css selector "#edit-field-content-paragraphs-wrapper ul.dropbutton" contains the following name-value pairs:
      | value                            | name                                              |
      | FAQ / Akkordion Liste hinzufügen | field_content_paragraphs_faq_list_add_more        |
      | Inhaltsreferenz hinzufügen       | field_content_paragraphs_node_reference_add_more  |
      | Ansichtsreferenz hinzufügen      | field_content_paragraphs_view_reference_add_more  |
      | Medienreferenz hinzufügen        | field_content_paragraphs_media_reference_add_more |
      | Downloads hinzufügen             | field_content_paragraphs_downloads_add_more       |
      | Text hinzufügen                  | field_content_paragraphs_text_add_more            |
      | Webform hinzufügen               | field_content_paragraphs_webform_add_more         |
      | Block Referenz hinzufügen        | field_content_paragraphs_block_reference_add_more |
      | Iframe hinzufügen                | field_content_paragraphs_iframe_add_more          |
      | Link-Liste hinzufügen            | field_content_paragraphs_links_add_more           |
      | Map hinzufügen                   | field_content_paragraphs_map_add_more             |

#  Scenario: Admin Content page
#    Given I am logged in as a user with the "administrator" role
#    And I am on "/admin/content"
#    And I see the button "Filter"
#    And I press button with label "Show all columns" via translated text
#    And I should see "Titel"
#    And I should see "Inhaltstyp"
#    And I should see "Autor"
#    And I should see "Aktualisiert"
#    And I should see "Interner Titel"
#    And I should see "Aktionen"
#    And I should not see the text "Undefined index"

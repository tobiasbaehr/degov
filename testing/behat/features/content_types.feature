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
      | degov_node_event |
    Given I proof content type "normal_page" has set the following fields for display:
      | field_teaser_title             |
      | field_teaser_text              |
      | content_moderation_control     |
      | field_header_paragraphs        |
      | field_sidebar_right_paragraphs |
      | title                          |

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

  Scenario: Admin Content page
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/content"
    And I see the button "Filter"
    And I press button with label "Show all columns" via translated text
    And I should see "Titel"
    And I should see "Inhaltstyp"
    And I should see "Autor"
    And I should see "Aktualisiert"
    And I should see "Interner Titel"
    And I should see "Aktionen"
    And I should not see the text "Undefined index"

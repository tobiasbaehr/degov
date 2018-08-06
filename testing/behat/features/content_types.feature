@api @drupal @javascript
Feature: deGov - Content types

  Background:
    Given I proof that the following Drupal modules are installed:
      | machine_name           |
      | degov_node_overrides   |
      | degov_node_normal_page |
      | degov_node_event       |

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

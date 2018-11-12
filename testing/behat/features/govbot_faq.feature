@api @drupal
Feature: deGov - GovBot FAQ

  Background:
    Given I am installing the following Drupal modules:
      | degov_govbot_faq |
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

@api @drupal @javascript
Feature: NRWGov content types
  Scenario: Checking available node content types
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/types"
    Then I should see text matching "Blog-Artikel"
    And I should see text matching "FAQ"
    And I should see text matching "Inhaltsseite"
    And I should see text matching "Veranstaltung"

  Scenario: Content type normal_page has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/types/manage/normal_page/fields"
    Then I should see text matching "field_section"
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

  Scenario: Content type blog has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/types/manage/blog/fields"
    Then I should see text matching "field_section"
    And I should see text matching "field_blog_author"
    And I should see text matching "field_blog_date"
    And I should see text matching "field_content_paragraphs"
    And I should see text matching "field_internal_title"
    And I should see text matching "field_header_paragraphs"
    And I should see text matching "field_tags"
    And I should see text matching "field_sidebar_right_paragraphs"
    And I should see text matching "field_social_media"
    And I should see text matching "field_blog_sub_title"
    And I should see text matching "field_teaser_image"
    And I should see text matching "field_teaser_text"
    And I should see text matching "field_teaser_title"
    And I should see text matching "field_teaser_sub_title"

  Scenario: Content type faq has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/types/manage/faq/fields"
    Then I should see text matching "field_section"
    And I should see text matching "field_faq_description"
    And I should see text matching "field_content_paragraphs"
    And I should see text matching "field_internal_title"
    And I should see text matching "field_faq_related"

  Scenario: Content type event has all required fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/types/manage/event/fields"
    Then I should see text matching "field_event_location"
    And I should see text matching "field_event_display_time"
    And I should see text matching "field_section"
    And I should see text matching "field_event_date"
    And I should see text matching "field_event_date_end"
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

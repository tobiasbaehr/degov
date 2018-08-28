@api @drupal
Feature: deGov - Teaser view modes

  Background:
    Given I am installing the "degov_paragraph_node_reference" module

  Scenario: I proof paragraph type node reference offers the specified view modes
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page"
    Then I choose "Inhalt" from tab menu
    And I should see text matching "Inhaltsbereich"
    And I press the "edit-field-content-paragraphs-add-more-add-modal-form-area-add-more" button
    And I should see text matching "Inhaltsreferenz" after a while
    And I press button with label "Inhaltsreferenz" via translated text
    Then I assert dropdown named "field_content_paragraphs[0][subform][field_node_reference_viewmode]" contains the following text-value pairs:
      | text                | value       |
      | Teaser kleines Bild | small_image |
      | Teaser langer Text  | long_text   |
      | Teaser schmal       | slim        |
      | Vorschau            | preview     |

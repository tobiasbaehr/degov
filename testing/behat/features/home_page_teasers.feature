@api @drupal @homePageTeasers
Feature: deGov - home page teasers

  Background:
    Given I am installing the following Drupal modules:
      | degov_demo_content |
    Given I proof that the following Drupal modules are installed:
      | degov_demo_content |
    Given I reset the demo content
    Given I clear the cache
    Given I am on homepage

  Scenario: Verify that "Teaser - Small Image" exists and contain needed elements
    And I should see an ".paragraph-node-reference-node-view-mode-small-image .paragraph__header h2" element with the content "Teaser - Small Image"
    And I should see an ".paragraph-node-reference-node-view-mode-small-image .paragraph__header h3" element with the content "Lorem ipsum dolor"
    And I should see exactly 15 instances of the element with the selector ".paragraph-node-reference-node-view-mode-small-image .paragraph__content .small-image"
    And I should not see "degov_demo_content" in the ".paragraph-node-reference-node-view-mode-small-image" element

  Scenario: Verify that "Teaser - Long Text" exists and contain needed elements
    And I should see an ".paragraph-node-reference-node-view-mode-long-text .paragraph__header h2" element with the content "Teaser - Long Text"
    And I should see an ".paragraph-node-reference-node-view-mode-long-text .paragraph__header h3" element with the content "sit amet Lorem"
    And I should see exactly 15 instances of the element with the selector ".paragraph-node-reference-node-view-mode-long-text .paragraph__content .long-text"
    And I should not see "degov_demo_content" in the ".paragraph-node-reference-node-view-mode-long-text" element

  Scenario: Verify that "Teaser - Preview" exists and contain needed elements
    And I should see an ".paragraph-node-reference-node-view-mode-preview .paragraph__header h2" element with the content "Teaser - Preview"
    And I should see an ".paragraph-node-reference-node-view-mode-preview .paragraph__header h3" element with the content "ipsum dolor sit"
    And I should see exactly 15 instances of the element with the selector ".paragraph-node-reference-node-view-mode-preview .paragraph__content .preview"
    And I should not see "degov_demo_content" in the ".paragraph-node-reference-node-view-mode-preview" element

  Scenario: Verify that "Teaser - Slim" exists and contain needed elements
    And I should see an ".paragraph-node-reference-node-view-mode-slim .paragraph__header h2" element with the content "Teaser - Slim"
    And I should see an ".paragraph-node-reference-node-view-mode-slim .paragraph__header h3" element with the content "amet consetetur sadipscing"
    And I should see exactly 15 instances of the element with the selector ".paragraph-node-reference-node-view-mode-slim .paragraph__content .slim"
    And I should not see "degov_demo_content" in the ".paragraph-node-reference-node-view-mode-slim" element


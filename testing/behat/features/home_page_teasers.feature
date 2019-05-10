@api @drupal @homePageTeasers
Feature: deGov - home page teasers

  Background:
    Given I am installing the following Drupal modules:
      | degov_demo_content |
    Given I clear the cache
    Given I am on homepage

  Scenario: Confirm that Homepage teasers and slider are in place
    And I should see the "div#degov-slider" in ".block-block-content"
    And I should see the "img" in "div#degov-slider div.carousel-item"
    And I should see 2 "div#degov-slider a.carousel-control" elements

    And I should see the "h2" in ".paragraph-node-reference-node-view-mode-small-image .paragraph__header"
    And I should see the "h3" in ".paragraph-node-reference-node-view-mode-small-image .paragraph__header"
    And I should see the "h3" in ".paragraph-node-reference-node-view-mode-small-image .paragraph__content .teaser-content"
    And I should see the "[class*=teaser-text]" in ".paragraph-node-reference-node-view-mode-small-image .paragraph__content .teaser-content .teaser-content-inner"
    And I should see the ".learn-more.bg-blue" in ".paragraph-node-reference-node-view-mode-small-image .paragraph__content"
    And I should see the "img" in ".paragraph-node-reference-node-view-mode-small-image .teaser-image"
    And I should not see "degov_demo_content" in the ".paragraph-node-reference-node-view-mode-small-image" element

    And I should see the "h2" in ".paragraph-node-reference-node-view-mode-long-text .paragraph__header"
    And I should see the "h3" in ".paragraph-node-reference-node-view-mode-long-text .paragraph__header"
    And I should see the "h3" in ".paragraph-node-reference-node-view-mode-long-text .paragraph__content .teaser-content"
    And I should see the "[class*=teaser-text]" in ".paragraph-node-reference-node-view-mode-long-text .paragraph__content .teaser-content .teaser-content-inner"
    And I should see the ".learn-more.bg-blue" in ".paragraph-node-reference-node-view-mode-long-text .paragraph__content"
    And I should see the "img" in ".paragraph-node-reference-node-view-mode-long-text .teaser-image"
    And I should not see "degov_demo_content" in the ".paragraph-node-reference-node-view-mode-long-text" element

    And I should see the "h2" in ".paragraph-node-reference-node-view-mode-preview .paragraph__header"
    And I should see the "h3" in ".paragraph-node-reference-node-view-mode-preview .paragraph__header"
    And I should see the "h3" in ".paragraph-node-reference-node-view-mode-preview .paragraph__content .teaser-content"
    And I should see the "[class*=teaser-text]" in ".paragraph-node-reference-node-view-mode-preview .paragraph__content .teaser-content"
    And I should see the ".learn-more.bg-blue" in ".paragraph-node-reference-node-view-mode-preview .paragraph__content"
    And I should see the "img" in ".paragraph-node-reference-node-view-mode-preview .teaser-image"
    And I should not see "degov_demo_content" in the ".paragraph-node-reference-node-view-mode-preview" element

    And I should see the "h2" in ".paragraph-node-reference-node-view-mode-slim .paragraph__header"
    And I should see the "h3" in ".paragraph-node-reference-node-view-mode-slim .paragraph__header"
    And I should see the "h3" in ".paragraph-node-reference-node-view-mode-slim .paragraph__content .teaser-content"
    And I should see the ".learn-more.bg-blue" in ".paragraph-node-reference-node-view-mode-slim .paragraph__content"
    And I should see the "img" in ".paragraph-node-reference-node-view-mode-slim .teaser-image"
    And I should not see "degov_demo_content" in the ".paragraph-node-reference-node-view-mode-slim" element

@api @drupal @content
Feature: deGov - deGov Theme

  Background:
    Given I am installing the following Drupal modules:
      | degov_demo_content |
    Given I am on homepage

  Scenario: Verify that "Sitemap" block exists in the Footer region
    Then I should see the "#block-footer" in "footer"
    And I should see "Sitemap" in the "#block-footer h2" element
    And I should see the "a" in "#block-footer > ul.row > li"

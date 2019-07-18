@api @drupal @content
Feature: deGov - deGov Theme

  Background:
    Given I am installing the following Drupal modules:
      | degov_demo_content |
    Given I am on homepage

  Scenario: Verify that Searchbox exists in the nav
    And I should not see the element with css selector ".search-form-wrapper"
    And I should see the "i.fas.fa-search" in ".top-header-wrapper nav .search-form-icon button"
    When I click by selector ".top-header-wrapper nav .search-form-icon button" via JavaScript
    Then I should see "div.search-form-wrapper" element visible on the page
    When I click by selector ".top-header-wrapper nav .search-form-icon button" via JavaScript
    Then I should not see the element with css selector ".search-form-wrapper"

  Scenario: Verify that "Sitemap" block exists in the Footer region
    And I should see the "#block-footer" in "footer.site-footer"
    And I should see "Sitemap" in the "footer.site-footer #block-footer h2" element
    And I should see the "a" in "footer.site-footer #block-footer > ul.row > li"

  Scenario: Verify that "Below Footer Menu" block exists in the Footer Bottom region
    Then I should see the "#block-belowfootermenu" in ".footer-bottom-wrapper"
    And I should see 2 "#block-belowfootermenu > ul > li > a" elements

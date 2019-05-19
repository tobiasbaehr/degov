@api @drupal @demo
Feature: deGov - Demo Content

  Background:
    Given I am installing the following Drupal modules:
      | degov_demo_content |

  Scenario: Check if all teasers will be displayed
    Given I am logged in as a user with the "administrator" role
    And I am on "/degov-demo-content/page-all-teasers"
    And I should see "Page with text paragraph"
    And I should see "Page with download paragraph"
    And I should see "Page with iframe paragraph"
    And I should see "Page with map paragraph"
    And I should see "Page with FAQ-List paragraph"
    And I should see "Page with video header"
    And I should see "Page with slideshow"
    And I should see "Page with banner"
    And I should see "TEASER - SMALL IMAGE"
    And I should see "TEASER - LONG TEXT"
    And I should see "TEASER - SLIM"
    And I should see "TEASER - PREVIEW"
    And I should see 56 ".paragraph__content article .image" elements

  Scenario: Check for missing fields
    Given I am logged in as a user with the "administrator" role
    And I am on "/degov-demo-content/page-banner"
    And I should see "Page with banner"
    And I should see "A page with an image header"
    And I should see "degov_demo_content"

  Scenario: Check page with video mobile
    Given I am logged in as a user with the "administrator" role
    And I am on "/degov-demo-content/page-responsive-video"
    Then I should see text matching "Page with responsive video"
    And I should see text matching "Choose quality:" via translated text
    And I should see text matching "Download" via translated text


@api @drupal
Feature: deGov - Media access

  Background:
    Given I proof that the following Drupal modules are installed:
      | permissions_by_term   |
      | permissions_by_entity |
      | degov_media_document  |
    Given I have a restricted document media entity

  Scenario: I am accessing a restricted media document file as anonymous user
    Given I am on "/system/files/media/document/file/word-document.docx"
    And I should see text matching "You are not authorized to access this page." via translated text

  Scenario: I am accessing a restricted media document file as anonymous user
    Given I am logged in as a user with the "administrator" role
    And I am on "/system/files/media/document/file/word-document.docx"
    And I should not see text matching "You are not authorized to access this page." via translated text

  Scenario: As an unauthenticated user I cannot access Media that is not in the library
    Given I have dismissed the cookie banner if necessary
    And I am on "/image-cannot-be-accessed-directly"
    Then I should be on the homepage

  Scenario: As an admin I can access Media that is not in the library
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    And I am on "/image-cannot-be-accessed-directly"
    Then I should be on "/image-cannot-be-accessed-directly"

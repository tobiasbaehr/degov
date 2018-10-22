@api @drupal
Feature: deGov - Media document access

  Background:
    Given I proof that the following Drupal modules are installed:
      | permissions_by_term   |
      | permissions_by_entity |
      | degov_media_document  |
    Given I have a restricted document media entity

  Scenario: I am accessing a restricted media document file as anonymous user
    Given I am on "/system/files/media/document/file/word-document.docx"
    And I dump the HTML
    And I should see text matching "Sie haben keine Zugriffsberechtigung für diese Seite." via translated text

  Scenario: I am accessing a restricted media document file as anonymous user
    Given I am logged in as a user with the "administrator" role
    And I am on "/system/files/media/document/file/word-document.docx"
    And I should not see text matching "Sie haben keine Zugriffsberechtigung für diese Seite." via translated text

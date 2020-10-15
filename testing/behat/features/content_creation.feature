@api @drupal @content_creation @javascript
Feature: deGov - Content creation

  Background:
    Given I am installing the following Drupal modules:
      | degov_demo_content          |
    Given I proof that the following Drupal modules are installed:
      | degov_node_press            |
      | degov_node_event            |
      | degov_node_blog             |
      | degov_node_normal_page      |
      | degov_simplenews_references |
      | filter_disallow             |
      | media_file_links            |
      | permissions_by_term         |

  Scenario: I create a press entity and check that the header section is being displayed as expected
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/press"
    And I fill in "Titel" with "Test1234"
    And I fill in Textarea with "Test1234"
    And I fill in "edit-field-press-date-0-value-date" with "01012018"
    And I scroll to bottom
    And I press button with label "Save" via translated text
    Then I should see HTML content matching "01.01.2018" after a while
    And I should see "Test1234" in the ".press__header-paragraphs" element

  Scenario: I see all form fields in normal_page content type
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page"
    And I should see "Titel"
    And I should see "Interner Titel"
    And I should see "Vorschau Titel"
    And I should see "Vorschau Untertitel"
    And I should see "Vorschau Text"
    And I choose "Allgemein" from tab menu
    And I should see "Schlagworte"
    And I should see "Sprache"
    And I should see "Thema"
    And I should see "Inhaltstyp"
    And I should see "Bereich"
    And I should see "Ansichtssteuerung"
    And I choose "Header" via translation from tab menu
    And I should see "KOPFBEREICH"
    And I should see 1 "#edit-field-content-paragraphs-add-more-add-modal-form-area-add-more" elements after a while
    And I should see HTML content matching "Paragraph" via translated text
    And I choose "Seitenleiste rechts" from tab menu
    And I should see "Seitenleiste rechts"
    And I should see 1 "#edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" elements after a while
    And I choose "Content" via translation from tab menu
    And I should see "INHALTSBEREICH"
    And I should see 1 "#edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" elements after a while

  Scenario: I see all form fields in blog content type
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/blog"
    And I should see "Titel"
    And I should see "Interner Titel"
    And I should see "Untertitel"
    And I should see "Datum"
    And I should see "Vorschau Titel"
    And I should see "Vorschau Untertitel"
    And I should see "Vorschau Text"
    And I choose "Allgemein" from tab menu
    And I should see "Schlagworte"
    And I should see "Social Media Buttons anzeigen"
    And I choose "Header" via translation from tab menu
    And I should see "KOPFBEREICH"
    And I should see 1 "#edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" elements after a while
    And I choose "Seitenleiste rechts" from tab menu
    And I should see "Seitenleiste rechts"
    And I should see 1 "#edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" elements after a while
    And I choose "Content" via translation from tab menu
    And I should see "INHALTSBEREICH"
    And I should see 1 "#edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" elements after a while

  Scenario: I see all form fields in faq content type
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/faq"
    And I should see "Titel"
    And I should see "Interner Titel"
    And I choose "Description" via translation from tab menu
    And I should see "Beschreibung"
    And I choose "Content" via translation from tab menu
    And I should see "INHALTSBEREICH"
    And I scroll to bottom
    And I should see "VERWANDTE FAQ"
    And I choose "Allgemein" from tab menu
    And I should see "Bereich"

  Scenario: I see all form fields in newsletter content type
    Given I have dismissed the cookie banner if necessary
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/simplenews_issue"
    And I should see text matching "Title" via translated text
    And I should see text matching "Newsletter" via translated text
    And I should see text matching "Body" via translated text

  Scenario: I see all form fields in press content type
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/press"
    And I should see "Titel"
    And I should see "Interner Titel"
    And I should see "Datum"
    And I should see "Vorschau Titel"
    And I should see "Vorschau Untertitel"
    And I should see "Vorschau Text"
    And I choose "Allgemein" from tab menu
    And I should see "Schlagworte"
    And I should see "Social Media Buttons anzeigen"
    And I should see "Sprache"
    And I should see "Thema"
    And I should see "Inhaltstyp"
    And I should see "Bereich"
    And I should see "Ansichtssteuerung"
    And I choose "Header" via translation from tab menu
    And I should see "KOPFBEREICH"
    And I should see 1 "#edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" elements after a while
    And I choose "Seitenleiste rechts" from tab menu
    And I should see "Seitenleiste rechts"
    And I should see 1 "#edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" elements after a while
    And I choose "Content" via translation from tab menu
    And I should see "INHALTSBEREICH"
    And I should see 1 "#edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" elements after a while

  Scenario: I see all form fields in event content type
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/event"
    And I should see "Titel"
    And I should see "Interner Titel"
    And I should see "DATUM"
    And I should see "ENDDATUM"
    And I should see "Anzeigezeit"
    And I should see "ADRESSE"
    And I should see "Land"
    And I should see "Firma"
    And I should see text matching "Postal code" via translated text
    And I should see text matching "City" via translated text
    And I should see "Vorschau Titel"
    And I should see "Vorschau Untertitel"
    And I should see "Vorschau Text"
    And I choose "Allgemein" from tab menu
    And I should see "Schlagworte"
    And I should see "Social Media Buttons anzeigen"
    And I should see "Sprache"
    And I should see "Thema"
    And I should see "Inhaltstyp"
    And I should see "Bereich"
    And I should see "Ansichtssteuerung"
    And I choose "Header" via translation from tab menu
    And I should see "KOPFBEREICH"
    And I should see 1 "#edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" elements after a while
    And I choose "Seitenleiste rechts" from tab menu
    And I should see "Seitenleiste rechts"
    And I should see 1 "#edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" elements after a while
    And I choose "Content" via translation from tab menu
    And I should see "INHALTSBEREICH"
    And I should see 1 "#edit-field-sidebar-right-paragraphs-add-more-add-modal-form-area-add-more" elements after a while

  Scenario: I can select a view mode for views reference paragraphs
    Given I am logged in as a user with the "administrator" role
    And I am installing the "degov_paragraph_view_reference" module
    And I am on "/node/add/normal_page"
    And I choose "Content" via translation from tab menu
    And I press the "edit-field-content-paragraphs-add-more-add-modal-form-area-add-more" button
    And I wait 2 seconds
    And I trigger the "mousedown" event on ".ui-dialog [name=field_content_paragraphs_view_reference_add_more]"
    Then I should see text matching "Ansichtsreferenz" via translation after a while
    And I should see 1 ".viewreference_target_id" element
    And I set the value of element ".viewreference_target_id" to "latest_events" via JavaScript
    And wait 2 seconds
    And I click by selector ".field--type-viewsreference summary" via JavaScript
    And I should see 1 ".viewsreference_view_mode:visible" elements via jQuery
    Then I assert dropdown named "field_content_paragraphs[0][subform][field_view_reference_view][0][options][view_mode]" contains the following text-value pairs:
      | text                          | value       |
      | Wie in der Ansicht definiert  |             |
      | Teaser kleines Bild           | small_image |
      | Teaser langer Text            | long_text   |
      | Teaser schmal                 | slim        |
      | Teaser Preview                | preview     |

  Scenario: I verify that script tags are removed from output
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I open node edit form by node title "Page with text paragraph"
    And I choose "Content" via translation from tab menu
    And I press the "edit-field-content-paragraphs-add-more-add-modal-form-area-add-more" button
    And I wait 2 seconds
    And I trigger the "mousedown" event on ".ui-dialog [name=field_content_paragraphs_text_add_more]"
    Then I should see text matching "Text format" via translated text after a while
    And I set the value of element '[data-drupal-selector="edit-field-content-paragraphs-2-subform-field-text-text-0-format"]' to "rich_html" via JavaScript
    And I wait 1 seconds
    And I set the value of element ".form-textarea-wrapper:eq(1) .cke_source" to "<script>document.write(\'scripttest1234\');</script>" via JavaScript
    And I scroll to bottom
    And I press button with label "Save" via translated text
    And I should not see text matching "scripttest1234"

  Scenario: I verify that font tags are removed from output
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I open node edit form by node title "Page with text paragraph"
    And I choose "Content" via translation from tab menu
    And I press the "edit-field-content-paragraphs-add-more-add-modal-form-area-add-more" button
    And I wait 2 seconds
    And I trigger the "mousedown" event on ".ui-dialog [name=field_content_paragraphs_text_add_more]"
    Then I should see text matching "Text format" via translated text after a while
    And I set the value of element '[data-drupal-selector="edit-field-content-paragraphs-3-subform-field-text-text-0-format"]' to "rich_html" via JavaScript
    And I wait 1 seconds
    And I set the value of element ".form-textarea-wrapper:eq(1) .cke_source" to "<font>BehatFont</font>" via JavaScript
    And I scroll to bottom
    And I press button with label "Save" via translated text
    And I should not see text matching "BehatFont"

  Scenario: I verify that I can enter Media file links using linkit
    Given I am logged in as a user with the "administrator" role
    And I have dismissed the cookie banner if necessary
    When I am on "/node/add/normal_page"
    And I fill in "Titel" with "media_file_link"
    Then I should see 1 ".cke" elements via jQuery after a while
    When I set value "rich_html" by name "field_teaser_text[0][format]"
    And I wait 1 seconds
    And I click by selector ".cke_button__drupallink_icon" via JavaScript
    Then I should see 1 ".form-linkit-autocomplete" elements via jQuery after a while
    When I fill in "URL" with "dummy"
    And I trigger the "keydown" event on ".form-linkit-autocomplete"
    Then I should see HTML content matching "linkit-result-line" after a while
    When I click by selector ".linkit-result-line" via JavaScript
    Then I verify that field value of ".form-linkit-autocomplete" matches "\[media\/file\/[\d]+\]"
    When I click by selector ".ui-dialog-buttonpane .button" via JavaScript
    And I select "draft" by name "moderation_state[0][state]"
    And I scroll to bottom
    And I click by selector "#edit-submit" via JavaScript
    Then I should see text matching "Inhaltsseite media_file_link wurde erstellt." after a while
    And I should be on "/mediafilelink"
    And I should see 1 'a[href$="/sites/default/files/degov_demo_content/dummy.pdf"]' elements via jQuery after a while
    When I open medias delete url by title "A document with a PDF"
    Then I should see the element with css selector "div.messages.messages--warning"
    When I open node delete form by node title "media_file_link"
    And I press button with label "Delete" via translated text
    Then I should be on the homepage
    And I should see text matching "Der Inhaltsseite media_file_link wurde gelöscht." after a while
    When I open medias delete url by title "A document with a PDF"
    Then I should not see the element with css selector "div.messages.messages--warning"

  Scenario: I verify that I can use linkit to set links for internal pages
    Given I am logged in as a user with the "administrator" role
    And I have dismissed the cookie banner if necessary
    When I am on "/node/add/normal_page"
    And I fill in "Titel" with "linkit"
    Then I should see 1 ".cke" elements via jQuery after a while
    And I set value "rich_html" by name "field_teaser_text[0][format]"
    And I wait 1 seconds
    When I click by selector ".cke_button__drupallink_icon" via JavaScript
    Then I should see 1 ".form-linkit-autocomplete" elements via jQuery after a while
    When I fill in "URL" with "blog"
    And I trigger the "keydown" event on ".form-linkit-autocomplete"
    Then I should see HTML content matching "linkit-result-line" after a while
    When I click by selector ".linkit-result-line" via JavaScript
    And I click by selector ".ui-dialog-buttonpane .button" via JavaScript
    And I select "draft" by name "moderation_state[0][state]"
    And I scroll to bottom
    And I click by selector "#edit-submit" via JavaScript
    Then I should see text matching "Inhaltsseite linkit wurde erstellt." after a while
    And I should be on "/linkit"
    And I should see 1 '.normal-page__teaser-text a[href$="/degov-demo-content/blog-post"]' elements via jQuery after a while
    When I open node delete form by node title "linkit"
    And I press button with label "Delete" via translated text
    Then I should be on the homepage
    And I should see text matching "Der Inhaltsseite linkit wurde gelöscht." after a while


  Scenario: I verify that the selected views reference values are preserved in the form
    Given I reset the demo content
    And I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I open node edit form by node title "Page with views references"
    And I choose "Content" via translation from tab menu
    And I trigger the "mousedown" event on "#field-content-paragraphs-1-edit--2"
    Then I should see text matching "Options" via translated text in uppercase after a while
    And I wait for AJAX to finish
    And I click by selector ".field--type-viewsreference .js-form-wrapper > summary" via JavaScript
    Then I should see text matching "Views row view mode" via translated text after a while
    And I verify that field ".viewsreference_view_mode" has the value "small_image"
    And I set the value of element ".viewsreference_view_mode" to "small_image" via JavaScript
    Then I fill in the autocomplete ".form-item-field-content-paragraphs-1-subform-field-view-reference-view-0-options-argument-0 input" with "degov_demo_content" via javascript
    And I scroll to bottom
    And I press button with label "Save" via translated text
    Then I open node edit form by node title "Page with views references"
    And I choose "Content" via translation from tab menu
    And I trigger the "mousedown" event on "#field-content-paragraphs-1-edit--2"
    Then I should see text matching "Options" via translated text in uppercase after a while
    And I wait for AJAX to finish
    And I click by selector ".field--type-viewsreference .js-form-wrapper > summary" via JavaScript
    Then I should see text matching "Views row view mode" via translated text after a while
    And I verify that field ".viewsreference_view_mode" has the value "small_image"
    And I verify that field value of ".form-item-field-content-paragraphs-1-subform-field-view-reference-view-0-options-argument-0 input" matches "degov_demo_content"

  Scenario: I verify that the taxonomy filter is working in the views reference paragraph
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    And I created a content page of type "press" named "A Press release without a tag" with a media "tweet"
    Then I open node view by node title "Page with views references"
    Then I should not see text matching "A press release without a tag" via translated text in "css" selector ".paragraph.view-reference:nth-child(2)"

#  Scenario: I confirm there are no duplicates in the block layout selection table
#    Given I have dismissed the cookie banner if necessary
#    And I am logged in as a user with the "administrator" role
#    And I am on "/admin/structure/block"
#    And I click by selector "a#edit-blocks-region-content-title" via JavaScript
#    Then I should see text matching "Place block" via translation after a while
#    And each HTML content element with css selector ".block-filter-text-source" is unique

  Scenario: Verify that rich text editor does not show duplicate buttons
    And I am logged in as a user with the "administrator" role
    Given I proof that the following Drupal modules are installed:
      | degov_rich_text_format_settings            |
      | degov_node_normal_page                     |
    And I am on "/node/add/normal_page"
    And I select "rich_text" by name "field_teaser_text[0][format]"
    And I wait 2 seconds
    And I should see 1 ".cke_top.cke_reset_all" elements via jQuery
    And I should see 1 ".cke_button_icon.cke_button__bold_icon" elements via jQuery
    And I should see 1 ".cke_button.cke_button__source.cke_button_off" elements via jQuery

  Scenario: Verify that a new node has the right url alias and a node with a menu link has the right alias
    And I am logged in as a user with the "administrator" role
    Given I proof that the following Drupal modules are installed:
      | degov_pathauto |
    And I am on "/node/add/blog"
    And fill in "title[0][value]" with "Behat Blog"
    And fill in "field_teaser_title[0][value]" with "Behat Blog"
    And I select "published" by name "moderation_state[0][state]"
    And I press button with label "Save" via translated text
    And I should be on "/behat-blog"
    And I am on "/node/add/blog"
    And fill in "title[0][value]" with "Behat Blog with menu link"
    And fill in "field_teaser_title[0][value]" with "Behat Blog with menu link"
    And I press button with label "Menüeinstellungen" via translated text
    And I check "menu[enabled]"
    And I select "-- FAQ-List paragraph" by name "menu[menu_parent]"
    And I select "published" by name "moderation_state[0][state]"
    And I press button with label "Save" via translated text
    And I should be on "/faq-list-paragraph/behat-blog-menu-link"

  Scenario: Verify that a new media node has the right url alias
    And I am logged in as a user with the "administrator" role
    Given I proof that the following Drupal modules are installed:
      | degov_pathauto |
    And I am on "/media/add/person"
    And I fill in "name[0][value]" with "Behat Person"
    And I fill in "field_title[0][value]" with "Behat Person"
    And I press button with label "Accept" via translated text
    And I press button with label "Save" via translated text
    And I should see text matching "Person Behat Person wurde erstellt." after a while
    And I am on "/person/behat-person"
    And I should not see text matching "Die angeforderte Seite konnte nicht gefunden werden." after a while

  Scenario: I verify that sidebar blocks are displayed in preview mode
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I open node edit form by node title "Page with text paragraph in sidebar"
    And I scroll to bottom
    And I press the "edit-preview" button
    Then I should see 1 "#block-sidebarparagraphsfromnodeentity" elements

  Scenario: I verify that content marked for deletion is still accessible
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/normal_page"
    And fill in "title[0][value]" with "To be deleted"
    Then I scroll to bottom
    And I select "published" by name "moderation_state[0][state]"
    And I press button with label "Save" via translated text
    Then I should be on "/be-deleted"
    Then I am on "/user/logout"
    And I am on "/be-deleted"
    And I should see HTML content matching "To be deleted"
    And I am logged in as a user with the "editor" role
    And I open node edit form by node title "To be deleted"
    Then I scroll to bottom
    And I select "to_be_deleted" by name "moderation_state[0][state]"
    And I press button with label "Save" via translated text
    Then I am on "/user/logout"
    And I am on "/be-deleted"
    And I should see HTML content matching "To be deleted"

  Scenario: I verify that taxonomy term restriction is enabled for the section vocabulary only
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I am on "/admin/permissions-by-term/settings"
    And the "target_bundles[section]" checkbox should be checked

  Scenario: I verify the options of the status filter on the content overview
    Given I have dismissed the cookie banner if necessary
    And I am logged in as a user with the "administrator" role
    Then I am on "/admin/content"
    And I should see HTML content matching '<option value="1">Veröffentlicht</option>'
    And I should see HTML content matching '<option value="2">Nicht veröffentlicht</option>'
    And I should see 3 "#edit-status > option" elements

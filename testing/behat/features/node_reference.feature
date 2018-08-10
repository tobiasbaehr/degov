@api @drupal
Feature: deGov - Node reference

  Background:
    Given I am installing the following Drupal modules:
      | degov_paragraph_node_reference |
      | degov_node_press               |
    And I proof that the following Drupal modules are installed:
      | paragraphs                     |
      | degov_paragraph_node_reference |
      | degov_node_press               |

  Scenario: Node type press can be referenced in node reference paragraph
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/paragraphs_type/node_reference/fields"
    Then I should see text matching "field_node_reference_nodes"
    And I click the "#field-node-reference-nodes > td:nth-child(4) > div > div > ul > li.edit.dropbutton-action > a" element
    And I dump the HTML
    And I should see text matching "Inhaltstyp" after a while
    Then the "settings[handler_settings][target_bundles][press]" checkbox should be checked
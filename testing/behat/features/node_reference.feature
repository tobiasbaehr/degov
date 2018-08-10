@api @drupal
Feature: deGov - Node reference

  Background:
    Given I am installing the following Drupal modules:
      | degov_paragraph_node_reference |
      | degov_node_press               |

  Scenario: Node type press can be referenced in node reference paragraph
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/structure/paragraphs_type/node_reference/fields/paragraph.node_reference.field_node_reference_nodes"
    And I should see text matching "REFERENZTYP"
    And I should see text matching "Inhaltstyp"
    Then the "settings[handler_settings][target_bundles][press]" checkbox should be checked
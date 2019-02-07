<?php

/**
 * Implements hook_views_data_alter().
 */
function degov_file_management_views_data_alter(array &$data) {
  $data['file_managed']['file_delete_link'] = [
    'title' => 'FNA',
    'field' => [
      'title' => 'FNU',
      'help' => 'Fnerb',
      'id' => 'degov_file_management_file_delete_link',
    ],
  ];
}
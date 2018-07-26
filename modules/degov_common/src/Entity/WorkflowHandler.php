<?php

namespace Drupal\degov_common\Entity;


use Drupal\Core\Config\ConfigFactory;

class WorkflowHandler {

  /**
   * @var \Drupal\Core\Config\ConfigFactory
   */
  private $configFactory;

  /** @var */
  private $config;

  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
    $this->config = $this->configFactory
      ->getEditable('workflows.workflow.editorial');
  }

  public function enableWorkflow(string $nodeType): void {
    $nodeTypes = $this->config->get('type_settings.entity_types.node');
    if (!$nodeTypes || !array_key_exists($nodeType, array_flip($nodeTypes))) {
      $nodeTypes[] = $nodeType;
      $this->config
        ->set('type_settings.entity_types.node', $nodeTypes)
        ->save(TRUE);
    }
  }

  public function disableWorkflow(string $nodeType): void {
    $nodeTypesConfig = $this->config->get('type_settings.entity_types.node');
    $nodeTypes = array_keys(array_flip($nodeTypesConfig));
    if (\in_array($nodeType, $nodeTypes, TRUE)) {
      unset($nodeTypes[$nodeType]);
      $filteredTypes = array_values($nodeTypes);
      $this->config
        ->set('type_settings.entity_types.node', $filteredTypes)
        ->save(TRUE);
    }
  }

}

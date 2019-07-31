<?php

namespace degov\Scripts\Robo\Model;


class InstallationProfile {

  private $machineName;

  private $label;

  private $description;

  /**
   * InstallationProfileDefinitionModel constructor.
   *
   * @param string $machineName
   * @param string $label
   * @param string $description
   */
  public function __construct(string $machineName, string $label, string $description) {
    $this->machineName = $machineName;
    $this->label = $label;
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getMachineName() {
    return $this->machineName;
  }

  /**
   * @param string $machineName
   */
  public function setMachineName(string $machineName): void {
    $this->machineName = $machineName;
  }

  /**
   * @return string
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * @param string $label
   */
  public function setLabel(string $label): void {
    $this->label = $label;
  }

  /**
   * @return string
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription(string $description): void {
    $this->description = $description;
  }

}
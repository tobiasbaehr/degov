<?php

namespace Drupal\degov_govbot_faq;

class GovBotFieldsMerger {

  /**
   * @param GovBotFieldsModel[] $govBotFieldModels
   * @return string
   */
  public function computeText(array $govBotFieldModels): string {
    $text = '';

    foreach ($govBotFieldModels as $govBotFieldModel) {
      $text .= $this->computePlain($govBotFieldModel->getFieldGovBotQuestion()) . ' ' . $this->computePlain($govBotFieldModel->getFieldGovBotAnswer());
    }

    return $text;
  }

  private function computePlain(string $text) {
    return trim(strip_tags($text));
  }

}

<?php

namespace Drupal\degov_govbot_faq;

class GovBotFieldsModel {

  /**
   * @var string
   */
  private $field_govbot_answer;

  /**
   * @var string
   */
  private $field_govbot_question;

  /**
   * GovBotFieldsModel constructor.
   * @param string $field_govbot_answer
   * @param string $field_govbot_question
   */
  public function __construct(string $field_govbot_answer, string $field_govbot_question)
  {
    $this->field_govbot_answer = $field_govbot_answer;
    $this->field_govbot_question = $field_govbot_question;
  }


  /**
   * @return string
   */
  public function getFieldGovBotAnswer(): string
  {
    return $this->field_govbot_answer;
  }

  /**
   * @param string $field_govbot_answer
   */
  public function setFieldGovBotAnswer(string $field_govbot_answer): void
  {
    $this->field_govbot_answer = $field_govbot_answer;
  }

  /**
   * @return string
   */
  public function getFieldGovBotQuestion(): string
  {
    return $this->field_govbot_question;
  }

  /**
   * @param string $field_govbot_question
   */
  public function setFieldGovBotQuestion(string $field_govbot_question): void
  {
    $this->field_govbot_question = $field_govbot_question;
  }

}

<?php

namespace Drupal\degov_simplenews\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;

class InsertNameService {

  public function updateForeAndSurname(FormStateInterface $form_state, Connection $database) {
    if (\Drupal::currentUser()->isAnonymous()) {
      $email = $form_state->getValues()['mail'][0]['value'];
    } else {
      $email = \Drupal::currentUser()->getEmail();
    }
    $database
      ->update('simplenews_subscriber')
      ->fields([
        'forename' => $form_state->getValues()['forename'],
        'surname' => $form_state->getValues()['surname'],
      ])
      ->condition('mail', $email, '=')
      ->execute();
  }

}

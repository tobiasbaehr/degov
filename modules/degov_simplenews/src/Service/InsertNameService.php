<?php

namespace Drupal\degov_simplenews\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

class InsertNameService {

  public function updateForeAndSurname(User $user, array $subscriberData, Connection $database): void {
    if ($user->isAnonymous()) {
      $email = $subscriberData['mail'];
    } else {
      $email = $user->getEmail();
    }
    $database
      ->update('simplenews_subscriber')
      ->fields([
        'forename' => $subscriberData['forename'],
        'surname' => $subscriberData['surname'],
      ])
      ->condition('mail', $email, '=')
      ->execute();
  }

}

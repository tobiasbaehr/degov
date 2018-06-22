<?php

namespace Drupal\degov_simplenews\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxy;

class InsertNameService {

	/**
	 * @var Connection
	 */
	private $database;

	public function __construct(Connection $database)
	{
		$this->database = $database;
	}

	public function updateForeAndSurname(AccountProxy $user, array $subscriberData): void {
    if ($user->isAnonymous()) {
      $email = $subscriberData['mail'];
    } else {
      $email = $user->getEmail();
    }
    $this->database
      ->update('simplenews_subscriber')
      ->fields([
        'forename' => $subscriberData['forename'],
        'surname' => $subscriberData['surname'],
      ])
      ->condition('mail', $email, '=')
      ->execute();
  }

}

<?php

namespace Drupal\Tests\degov_simplenews\Kernel;

use Drupal\Core\Database\Connection;
use Drupal\degov_simplenews\Service\InsertNameService;
use Drupal\KernelTests\KernelTestBase;
use Drupal\simplenews\Entity\Subscriber;
use Drupal\user\Entity\User;

/**
 * Unit tests for certain functions.
 *
 * @group degov_simplenews
 */
class DeGovSimplenewsKernelTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['degov_simplenews', 'simplenews', 'user', 'system'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('simplenews_subscriber');
    $this->installSchema('system', ['sequences']);
  }

  public function testTest() {

    /** @var User $user */
    $user = User::create([
      'name' => 'foo',
      'mail' => 'foo@example.com',
    ]);
    $user->save();

    $subscriberData = [
      'mail' => ['foo@example.com'],
      'forename' => 'John',
      'surname' => 'Doe',
    ];

    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::create(['mail' => $subscriberData['mail'],]);
    $subscriber->save();

    /** @var Connection $database */
    $database = Connection::class;

    /** @var InsertNameService $insertName */
    $insertName = InsertNameService::class;
    $insertName->updateForeAndSurname($user, $subscriberData, $database);

    $result = $database
      ->select('simplenews_subscriber', 'ss')
      ->fields('ss', ['forename', 'surname',])
      ->condition('ss.mail', $subscriberData['mail'], '=')
      ->execute()
      ->fetchAll();

    // todo: asserts (Vorname und Nachname wurde richtig in der Datenbank gespeichert ?)

    /** @var Subscriber $subscriber */
    /*
    $subscriber = Subscriber::create(['mail' => 'user@example.com',]);
    $subscriber->save();

    $user = User::create([
      'name' => 'user',
      'mail' => 'user@example.com',
    ]);
    $user->save();

    $subscriber = Subscriber::load($subscriber->id());
    $this->assertEquals($user->id(), $subscriber->getUserId());
    $this->assertFalse($subscriber->getStatus());

    $user->setEmail('user2@example.com');
    $user->activate();
    $user->save();

    $subscriber = Subscriber::load($subscriber->id());
    $this->assertEquals('user2@example.com', $subscriber->getMail());
    $this->assertTrue($subscriber->getStatus());
    */
  }

}

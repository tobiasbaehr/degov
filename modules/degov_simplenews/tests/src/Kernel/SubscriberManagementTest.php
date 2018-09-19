<?php

namespace Drupal\Tests\degov_simplenews\Kernel;


use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\node\Entity\Node;
use Drupal\simplenews\Entity\Newsletter;
use Drupal\simplenews\Entity\Subscriber;
use Drupal\simplenews\SubscriberInterface;
use Drupal\Tests\token\Kernel\KernelTestBase;

class SubscriberManagementTest extends KernelTestBase
{
  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'node',
    'views',
    'simplenews',
    'field',
    'degov_simplenews'
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp()
  {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('simplenews_subscriber');
    $this->installConfig('simplenews');

    $now = time();
    $last_week = strtotime('-1 week');

    $second_newsletter = Newsletter::create([
      'id'           => 'second',
      'name'         => 'My second newsletter',
      'description'  => 'Another one!',
      'format'       => 'plain',
      'priority'     => 0,
      'receipt'      => 0,
      'from_name'    => '',
      'subject'      => '[[simplenews-newsletter:name]] [node:title]',
      'from_address' => 'replace@example.org'
    ]);
    $second_newsletter->save();

    // create subscriber with recent signup date
    $subscriberBeforeExpirationDate = Subscriber::create([
      'mail'     => 'test1@test.com',
      'status'   => SubscriberInterface::ACTIVE,
      'langcode' => 'en',
      'created'  => $now,
      'forename' => 'Testi',
      'surname'  => 'McTesting',
    ]);
    $subscriberBeforeExpirationDate->save();
    $subscriberBeforeExpirationDate->subscribe('default', SIMPLENEWS_SUBSCRIPTION_STATUS_UNCONFIRMED, 'test', $now);
    $subscriberBeforeExpirationDate->subscribe('second', SIMPLENEWS_SUBSCRIPTION_STATUS_UNCONFIRMED, 'test', $now);
    $subscriberBeforeExpirationDate->save();

    // create subscriber older than check-threshold with all confirmed subscriptions
    $subscriberWithAllConfirmedSubs = Subscriber::create([
      'mail'     => 'test2@test.com',
      'status'   => SubscriberInterface::ACTIVE,
      'langcode' => 'en',
      'created'  => $last_week,
      'forename' => 'Testi',
      'surname'  => 'McTesting',
    ]);
    $subscriberWithAllConfirmedSubs->save();
    $subscriberWithAllConfirmedSubs->subscribe('default', SIMPLENEWS_SUBSCRIPTION_STATUS_SUBSCRIBED, 'test', $last_week);
    $subscriberWithAllConfirmedSubs->subscribe('second', SIMPLENEWS_SUBSCRIPTION_STATUS_SUBSCRIBED, 'test', $last_week);
    $subscriberWithAllConfirmedSubs->save();

    // create subscriber older than check-threshold with mixed-confirmation subscriptions
    $subscriberWithMixedSubs = Subscriber::create([
      'mail'     => 'test3@test.com',
      'status'   => SubscriberInterface::ACTIVE,
      'langcode' => 'en',
      'created'  => $last_week,
      'forename' => 'Testi',
      'surname'  => 'McTesting',
    ]);
    $subscriberWithMixedSubs->save();
    $subscriberWithMixedSubs->subscribe('default', SIMPLENEWS_SUBSCRIPTION_STATUS_SUBSCRIBED, 'test', $last_week);
    $subscriberWithMixedSubs->subscribe('second', SIMPLENEWS_SUBSCRIPTION_STATUS_UNCONFIRMED, 'test', $last_week);
    $subscriberWithMixedSubs->save();

    // create subscriber older than check-threshold with all unconfirmed subscriptions
    $subscriberWithAllUnconfirmedSubs = Subscriber::create([
      'mail'     => 'test4@test.com',
      'status'   => SubscriberInterface::ACTIVE,
      'langcode' => 'en',
      'created'  => $last_week,
      'forename' => 'Testi',
      'surname'  => 'McTesting',
    ]);
    $subscriberWithAllUnconfirmedSubs->save();
    $subscriberWithAllUnconfirmedSubs->subscribe('default', SIMPLENEWS_SUBSCRIPTION_STATUS_UNCONFIRMED, 'test', $last_week);
    $subscriberWithAllUnconfirmedSubs->subscribe('second', SIMPLENEWS_SUBSCRIPTION_STATUS_UNCONFIRMED, 'test', $last_week);
    $subscriberWithAllUnconfirmedSubs->save();

    // create unsubscribed subscriber
    $unsubscribedSubscriber = Subscriber::create([
      'mail'     => 'test5@test.com',
      'status'   => SubscriberInterface::ACTIVE,
      'langcode' => 'en',
      'created'  => $last_week,
      'forename' => 'Testi',
      'surname'  => 'McTesting',
    ]);
    $unsubscribedSubscriber->save();
    $unsubscribedSubscriber->subscribe('default', SIMPLENEWS_SUBSCRIPTION_STATUS_UNSUBSCRIBED, 'test', $last_week);
    $unsubscribedSubscriber->subscribe('second', SIMPLENEWS_SUBSCRIPTION_STATUS_UNSUBSCRIBED, 'test', $last_week);
    $unsubscribedSubscriber->save();
  }

  public function testSubscriberAddedBeforeTheExpirationTimeShouldSurvive()
  {
    $subscribers = $this->loadSubscribersByProperties(['mail' => 'test1@test.com']);
    $this->assertEquals(1, count($subscribers));
    \Drupal::service('cron')->run();
    $subscribers = $this->loadSubscribersByProperties(['mail' => 'test1@test.com']);
    $this->assertEquals(1, count($subscribers));
  }

  public function testSubscriberWithAllConfirmedSubscriptionsShouldSurvive()
  {
    $subscribers = $this->loadSubscribersByProperties(['mail' => 'test2@test.com']);
    $this->assertEquals(1, count($subscribers));
    \Drupal::service('cron')->run();
    $subscribers = $this->loadSubscribersByProperties(['mail' => 'test2@test.com']);
    $this->assertEquals(1, count($subscribers));
  }

  public function testSubscriberWithMixedConfirmationStatusSubscriptionsShouldSurvive()
  {
    $subscribers = $this->loadSubscribersByProperties(['mail' => 'test3@test.com']);
    $this->assertEquals(1, count($subscribers));
    \Drupal::service('cron')->run();
    $subscribers = $this->loadSubscribersByProperties(['mail' => 'test3@test.com']);
    $this->assertEquals(1, count($subscribers));
  }

  public function testSubscriberWithAllUnconfirmedSubscriptionsShouldPerish()
  {
    $subscribers = $this->loadSubscribersByProperties(['mail' => 'test4@test.com']);
    $this->assertEquals(1, count($subscribers));
    \Drupal::service('cron')->run();
    $subscribers = $this->loadSubscribersByProperties(['mail' => 'test4@test.com']);
    $this->assertEquals(0, count($subscribers));
  }

  public function testUnsubscribedSubscriberShouldSurvive()
  {
    $subscribers = $this->loadSubscribersByProperties(['mail' => 'test5@test.com']);
    $this->assertEquals(1, count($subscribers));
    \Drupal::service('cron')->run();
    $subscribers = $this->loadSubscribersByProperties(['mail' => 'test5@test.com']);
    $this->assertEquals(1, count($subscribers));
  }

  private function loadSubscribersByProperties(array $properties)
  {
    return \Drupal::entityTypeManager()->getStorage('simplenews_subscriber')->loadByProperties($properties);
  }
}
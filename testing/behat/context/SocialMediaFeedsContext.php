<?php

declare(strict_types=1);

namespace Drupal\degov\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Drupal\degov\Behat\Context\Traits\DebugOutputTrait;
use Drupal\degov\Behat\Context\Traits\DurationTrait;

/**
 * class SocialMediaFeedsContext
 */
class SocialMediaFeedsContext extends RawMinkContext {

  use DebugOutputTrait;

  use DurationTrait;

  /**
   * @var bool
   */
  private $initializedStartTime = TRUE;

  /**
   * @Then I prove that social media feed :feedName is enabled
   */
  public function iProveSocialMediaFeedIsEnabled(string $feedName): void {
    $this->ensureSocialMediaFeedIsEnabled($feedName);
  }

  /**
   * Provide social media feeds data in the following format:
   *
   * | instagram      |
   * | twitter        |
   *
   * @Given I wait until the following social media feeds are enabled as cookie values:
   */
  public function waitUntilMultipleSocialMediaFeedsAreEnabledAsCookieValues(TableNode $socialMediaFeedsTable): void {
    $rowsHash = $socialMediaFeedsTable->getRowsHash();
    $socialMediaFeedNames = array_keys($rowsHash);

    while (self::maxDurationNotElapsed() && !$this->ensureMultipleSocialMediaFeedsAreEnabled($socialMediaFeedNames)) {
      continue;
    }
  }

  private function ensureMultipleSocialMediaFeedsAreEnabled(array $socialMediaFeedNames): bool {
    $allAreEnabled = TRUE;

    foreach ($socialMediaFeedNames as $socialMediaFeedName) {
      if (!$this->ensureSocialMediaFeedIsEnabled($socialMediaFeedName)) {
        $allAreEnabled = FALSE;
      }
    }

    return $allAreEnabled;
  }

  private function ensureSocialMediaFeedIsEnabled(string $feedName): bool {
    $cookieConfig = $this->getSession()->evaluateScript("document.cookie
      .split('; ')
      .find(row => row.startsWith('degov_social_media_settings'))
      .split('=')[1];");

    if (strpos($cookieConfig, $feedName . '%22%3Atrue')) {
      return TRUE;
    }

    return FALSE;
  }

}

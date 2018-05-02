<?php

namespace Drupal\degov_social_media_settings\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class CookieCheck {

  /**
   * @var RequestStack
   */
  private $requestStack;

  /**
   * @var \stdClass
   */
  private $socialMediaSettings;

  public function __construct(RequestStack $request_stack) {
    $this->requestStack = $request_stack;
    $this->setSocialMediaSettings();
  }

  private function setSocialMediaSettings() {
    $this->socialMediaSettings = json_decode($this->requestStack->getCurrentRequest()->cookies->get('degov_social_media_settings'));
  }

  public function isYouTubeEnabled(): bool {
    return (bool) $this->socialMediaSettings->youtube;
  }

  public function isFacebookEnabled(): bool {
    return (bool) $this->socialMediaSettings->facebook;
  }

  public function isInstagramEnabled(): bool {
    return (bool) $this->socialMediaSettings->instagram;
  }

  public function isTwitterEnabled(): bool {
    return (bool) $this->socialMediaSettings->twitter;
  }

}
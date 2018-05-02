<?php

namespace Drupal\Tests\degov_social_media_settings\Kernel;

use Drupal\degov_social_media_settings\Service\CookieCheck;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CookieCheckTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  private function mockRequestStack() {
    /**
     * @var RequestStack $requestStack
     */
    $requestStack = $this->prophesize(RequestStack::class);
    /**
     * @var Request $request
     */
    $request = new \stdClass();

    $parameterBag = $this->prophesize(ParameterBag::class);
    $parameterBag->get(Argument::exact('degov_social_media_settings'))->willReturn('{"facebook":false,"twitter":false,"youtube":true,"googleplus":false,"pinterest":false,"flickr":false,"vimeo":false,"other":false}');

    $request->cookies = $parameterBag->reveal();

    $requestStack->getCurrentRequest()->willReturn($request);

    return $requestStack->reveal();
  }

  public function testIsYouTubeEnabled() {
    $cookieCheck = new CookieCheck($this->mockRequestStack());

    $this->assertTrue($cookieCheck->isYouTubeEnabled());
  }

  public function testIsFacebookEnabled() {
    $cookieCheck = new CookieCheck($this->mockRequestStack());

    $this->assertFalse($cookieCheck->isFacebookEnabled());
  }

}

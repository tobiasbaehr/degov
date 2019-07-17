<?php

namespace Drupal\degov_theming\TwigExtension;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Template\TwigExtension;
use Drupal\Core\Theme\ThemeManagerInterface;
use Twig\TwigFunction;


class IsEmptyValueExtension extends TwigExtension {

  protected $twigExtensionService;

  protected $logger;

  use EmptyValueCheckerTrait;

  public function __construct(RendererInterface $renderer, UrlGeneratorInterface $url_generator, ThemeManagerInterface $theme_manager, DateFormatterInterface $date_formatter, LoggerChannelFactoryInterface $loggerFactory) {
    parent::__construct($renderer,  $url_generator,  $theme_manager,  $date_formatter, $loggerFactory);
    $this->logger = $loggerFactory->get('degov_theme');
  }

  public function getFunctions(): array {
    return [
      new TwigFunction('is_empty', [$this, 'isEmpty']),
    ];
  }

  public function getName(): string {
    return 'is_empty';
  }

}

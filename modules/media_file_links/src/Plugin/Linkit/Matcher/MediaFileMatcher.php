<?php

namespace Drupal\media_file_links\Plugin\Linkit\Matcher;

use Drupal\linkit\MatcherBase;
use Drupal\linkit\Suggestion\EntitySuggestion;
use Drupal\linkit\Suggestion\SuggestionCollection;
use Drupal\media_file_links\Service\MediaFileSuggester;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides specific LinkIt matchers for our custom entity type.
 *
 * @Matcher(
 *   id = "entity:media_file_links",
 *   label = @Translation("Media file links"),
 *   target_entity = "media",
 *   provider = "media_file_links"
 * )
 */
class MediaFileMatcher extends MatcherBase {

  /**
   * @var \Drupal\media_file_links\Service\MediaFileSuggester
   */
  protected $fileSuggester;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The target entity type ID.
   *
   * @var string
   */
  protected $targetType;

  /**
   * @param \Drupal\media_file_links\Service\MediaFileSuggester $file_suggester
   */
  public function setFileSuggester(MediaFileSuggester $file_suggester): void {
    $this->fileSuggester = $file_suggester;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->setFileSuggester($container->get('media_file_links.file_suggester'));
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->targetType = 'media';
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function execute($string): SuggestionCollection {
    $suggestions = new SuggestionCollection();

    $mediaEntities = \json_decode($this->fileSuggester->findBySearchString($string), TRUE);

    if (!empty($mediaEntities)) {
      foreach ($mediaEntities as $mediaEntityResult) {
        $suggestion = $this->createSuggestion($mediaEntityResult);
        $suggestions->addSuggestion($suggestion);
      }
    }

    return $suggestions;
  }


  /**
   * {@inheritdoc}
   */
  protected function createSuggestion(array $mediaEntityResult) {
    $mediaEntity = $this->entityTypeManager->getStorage($this->targetType)->load($mediaEntityResult['id']);

    $suggestion = new EntitySuggestion();
    $suggestion->setEntityTypeId($this->targetType);
    $suggestion->setLabel($mediaEntityResult['title'])
      ->setGroup($this->t('Media file links'))
      ->setDescription(sprintf(
        '<i class="%s" /> %s, %s',
        $mediaEntityResult['iconClass'],
        $mediaEntityResult['bundleLabel'],
        $mediaEntityResult['filename']
      ))
      ->setSubstitutionId('canonical')
      ->setEntityUuid($mediaEntity->uuid())
      ->setPath('[media/file/' . $mediaEntityResult['id'] . ']');
    return $suggestion;
  }



}

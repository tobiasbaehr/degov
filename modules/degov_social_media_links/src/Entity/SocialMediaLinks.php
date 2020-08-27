<?php

namespace Drupal\degov_social_media_links\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\degov_social_media_links\SocialMediaLinksInterface;

/**
 * Defines the SocialMediaLinks entity.
 *
 * @ConfigEntityType(
 *   id = "degov_social_media_links",
 *   label = @Translation("Social media link"),
 *   handlers = {
 *     "list_builder" = "Drupal\degov_social_media_links\Controller\SocialMediaLinksListBuilder",
 *     "form" = {
 *       "add" = "Drupal\degov_social_media_links\Form\SocialMediaLinksForm",
 *       "edit" = "Drupal\degov_social_media_links\Form\SocialMediaLinksForm",
 *       "delete" = "Drupal\degov_social_media_links\Form\SocialMediaLinksDeleteForm",
 *     }
 *   },
 *   config_prefix = "link",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "url" = "url",
 *     "label" = "label",
 *     "icon" = "icon",
 *     "weight" = "weight"
 *   },
 *   config_export = {
 *     "id",
 *     "url",
 *     "label",
 *     "icon",
 *     "weight"
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/degov/social_media_links/edit/{degov_social_media_links}",
 *     "delete-form" = "/admin/config/degov/social_media_links/{degov_social_media_links}/delete",
 *   }
 * )
 */
class SocialMediaLinks extends ConfigEntityBase implements SocialMediaLinksInterface {

  /**
   * Id.
   *
   * @var string
   */
  public $id;

  /**
   * Url.
   *
   * @var string
   */
  public $url;

  /**
   * Label.
   *
   * @var string
   */
  public $label;

  /**
   * Icon class name(s).
   *
   * @var string
   */
  public $icon;

  /**
   * Weight.
   *
   * @var int
   */
  public $weight;

}

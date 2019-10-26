<?php

namespace Drupal\node_action\AccessChecker;


trait MessagesTrait {

  /**
   * @var string
   */
  private static $messageNoRolePermission = '`@actionName` action took no effect on node titled `@nodeTitle`, since your user has no role permission.';

  /**
   * @var string
   */
  private static $messageNoPermissionByTermPermission = '`@actionName` action took no effect on node titled `@nodeTitle`, due to taxonomy term restrictions regarding your user.';

  /**
   * @var string
   */
  private static $messageNoUnpublishPermission = 'Node with title `@nodeTitle` is published. The workflow does not allow editors to modify the moderation state of published content.';

  /**
   * @var string
   */
  private static $messageNoModerationStatesForContentEntityType = 'Content with title `@title` is of content entity type `@contentEntityType`, which does not have any moderation states assigned.';

}

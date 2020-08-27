# Social media links module

This module allows you to add arbitrary external links with a name and icon using font-awesome icon selector (`degov_fa_icon_picker`) to your theme.

Add links at *`/admin/config/degov/degov_social_media_links`*.


You may use something like the following to render this ordered link set in your theme.

```php
use Drupal\degov_social_media_links\Controller\SocialMediaLinksListBuilder;
$variables['#social_media_links'] = SocialMediaLinksListBuilder::getRenderArray();
```

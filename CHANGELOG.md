# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased
- Merged configuration for node normal page into one module
- Merged configuration for node press into one module
- Merged configuration for node blog into one module
- Merged configuration for node faq into one module
- Deleted duplicated rewrites for simplenews module

## [2.0.10] - 24-07-2018
- Fixed deGov removeContent routine
- Removed workbench_moderation \#2984124
- configured content_moderation
- Some module description translations from german into english
- Fixed configuration which has not been appliable via config_replace module

## [2.0.9] 23-07-2018
- Removed lightning workflow module \#2987098
- Fixed media edit page not accessable \#2986289
- Fixed "media_type_label" is an invalid render array key error
- Fixed image styles and cropping dependencies.
- Fixed 'deGov - Node external teaser' installation.

## [2.0.8] - 22-07-2018
- Security Update for EU Cookie Compliance & more

## [2.0.7] - 19-07-2018
- Fixed installation of degov_restrict_ip module.
- Fixed installation - removed unmet module "degov_shariff_social_sharing" from installation step.

## [2.0.6] - 17-07-2018
- Fixed degov_node_blog_rewrite module dependency.

## [2.0.5] - 12-07-2018
- Add Jenkinsfile to allow the CI/CD process in a Jenkins environment
- Renamed a few nrwGov naming left-overs
- Fixed failed tag pushing to drupal.org

## [2.0.4] - 10-07-2018
- Removed entity reference integrity module
- \#2984568: Moved modules from proprietary nrwGov distribution into deGov. E.g. search media manager, paragraph overrides, paragraph header video, external teaser, miscellaneous config replacements. 

## [2.0.3] - 09-07-2018
- Moved optional user roles config to 'install'
- New implementation of degov_media_gallary
- Moved optional user roles config to 'install'
- Removed unnecessary stuff
- Removed nrw dependencies
- Implemented automatic push into the bitbucket pipeline
- Pushing translated tags from bitbucket to drupal.org
- Removed dependency to lightning API
- Added automatic backup and restore function for platform.sh
- \#2984104: Removed leftover nrw_simplenews table

## [2.0.2] - 05-07-2018
- Replaced dependency to config_rewrite with config_replace module (does not allow config replacements for not existing original config)
- Merged config rewrites into original config
- Removal of faulty config
- Moved meta tag fields for content types from rewrites into original config
- Fixed not existing logger channel for config_replace module

## [2.0.1] - 05-07-2018
- Security Udpate 8.5.5
- Use another branch for the deGov Theme

## [2.0.0] - 05-07-2018
- First 2.x release

## [2.0.0-beta21] - 05-07-2018
- new implementation of degov_media_gallery

## [2.0.0-beta20] - 03-07-2018
- Implemented static code analyse with phpstan

## [2.0.0-beta19] - 03-07-2018
- Changed project tag from 'deGov' to 'degov'

## [2.0.0-beta18] - 03-07-2018
- Removed make files
- Updated README file to improve documentation
- Improved pipeline
- Added matomo dependecy
- Removed piwik dependency from deGov installation profile
- Removed broken patches

## [2.0.0-beta17] - 27-06-2018
- Show and hide slick controls by SoMe settings

## [2.0.0-beta16] - 27-06-2018
- Added makefile for automatic generation of deGov

## [2.0.0-beta15] - 26-06-2018
- Fixed translation warnings on localize.drupal.org
- Added Youtube Slider
- Added Instagram Slider
- Fixed social-media-settings (social media sliders can be enables/disabled without reloading the whole page)
- Added drush and drupal console as dependencies
- Using Chromedriver for behat tests in the pipeline

## [2.0.0-beta14] - 19-06-2018
- Fixed twig syntax for text and faq paragraph

## [2.0.0-beta13] - 18-06-2018
- Add ids to the faq, to allow referencing and anchoring

## [2.0.0-beta12] - 18-06-2018
- Videos and FAQ paragraphs are accessible with the keyboard

## [2.0.0-beta11] - 01-06-2018
- Fixed unimported namespace in degov_common_add_translation()

## [2.0.0-beta10] - 25-05-2018
- [CRITICAL] Guest user newsletter registration does not update all other guest user email addresses.

## [2.0.0-beta9] - 15-05-2018
- Show mediatype in slideshow text (MHKBGNRW-69). 
- Solved bug in degov_media_gallery according to the use of PhotoSwipe

## [2.0.0-beta7] - 09-05-2018
- Renamed "webformular" paragraph type id to "webform" via update hook
- Removed broken facets module version dependency in degov_search_base module

## [2.0.0-beta7] - 08-05-2018
- Fixed media gallery indexing via checking field value before retrieving it
- Implemented disabling of Twitter feeds
- Template rendering via Template service
- Implemented degov_common function for adding translations manually. E.g. in update hooks.

## [2.0.0-beta6] - 27-04-2018
- Updated translations in degov_simplenews module

## [2.0.0-beta5] - 25-04-2018
- Updated drupal core to 8.5.3

## [2.0.0-beta4] - 24-04-2018
- Added settings for privacy URL and netiquette URL in common module (please execute drush updb)
- Added fields for forename and surname in the Simplenews newsletter subscription.
- Fixed suggestion of module templates in the layer system. Base theme templates are loaded,
if project theme templates are not existing. Otherwise the module templates are loaded. 
- Moved PHPUnit to the development dependencies
- Pipelines - Undo removing of lightning tests

## [2.0.0-beta3] - 17-04-2018
- Implemented external teaser content type
- Drupal security update 8.5.1
- Override default configuration of the metatag module
- Use stable versions of lightning
- Pipelines - Started php build-in server in proper directory
- Simplenews module requires privacy policy acceptance
- Implemented allow download functionality in gallery media type
- Removed external teaser content type
- Removed german naming in webform paragraph
- Tests for the common module
- Implemented paragraphs removal into common module

## [2.0.0-beta2] - 22-03-2018
- Updated Changelogs
- Removed duplicated files
- Fixed date format in calendar to be in German by default

## [2.0.0-beta1] - 09-03-2018
- Removed lightning profile from deGov
- Outsourced installation profile 
- Moved deGov modules into installation profile
- Updated modules to support drupal 8.5+
- Added installation process
- Added pipelines configuration
- Added Behat configuration
- Added PHPUnit configuration

## [1.14.2] - 05-01-2018
### Fixed
- Removed duplicated colon in libraries.yml

## [1.14.1] - 05-01-2018
### Fixed
- Simplenews newsletter plain text view modes.

## [1.14.0] - 20-12-2017 
### Added
- Added description for field field_slideshow_type in paragraph degov_paragraph_slideshow
- Added support for media bundles tweet and instagram to degov_social_media_settings.

### Fixed
- Fixed configurations of degov_media_video configurations

## Changed
- Chnage cardinility of simplenews_issue field to 10 in degov_simplenews module.
- Views reference when argument field is empty doesn't try to set the argument, 
  so the default argument is calculated by the view itself.

## [1.13.1] - 11-12-2017
### Added
- Updated dependencies from deGov modules to keep lightning up to date.

## [1.13.0] - 05-12-2017
### Added
- Media bundle facts (Module degov_media_facts)

### Removed
- The gallery subtitle field has been deprecated. In case your project
  used this field, than you must override all media gallery templates found
  in the degov_media_gallery module into your own theme and include the
  subtitle back into these templates.
  
## Changed
- Supported version of facets starts from 8.x-1.0-beta1.

### Fixes
- By default render the tags field with surrounding and inner item wrappers.
- Copied configuration from deprecated update_hook to install and rewrite

## [1.12.3] - 24-11-2017
### Changed
- Remove tags display from document display.

## [1.12.0 - 1.12.2] - 24-11-2017
### Added
- New degov_social_media_settings module for social media access control.
- The field to control the display of the time in event node type.
- Validation for event dates.
- Public/internal title for all media bundles.
- Added novalidate attribute to node and media forms.
- Added entity reference fields to node type simplenews_issue.
- Filter of view modes on paragraphs edit form.
- Added Data protection checkbox to newsletter.
- A new media video preview image is added replacing the previous thumbnail.

### Changed
- Changed RSS feeds view from rendered entity to fields for better control.
- Remove untranslated nodes from sitemap.xml
- Use default theme for sending HTML mails.
- By default the media search now shows 12 items and a full pager.
- Removes auto trimming of the teaser title.
- Updated editor role permissions.
- Media video template.
- Removed label and fixed info block for media video upload.

### Fixed
- Reinstalls missing audio embedded view mode.
- Removed linking dependencies through variables from media modules. 
- Fixes broken slideshow of type 2.
- Displays the position field of a media contact.
- Use comma as decimal separator for file sizes.
- Link image media tags to search page.
- Hide blog author if not specified in a blog article.
- Adapted minimum search keyword length to search index settings.

## [1.11.0] - 06-11-2017
### Changed
- Removed deprecated degov_view_helper module from codebase.

### Fixed
- All the events should be displayed by default from today.

## [1.10.0] - 27-10-2017
### Added
- Added composer dependency on views_reference module to degov_paragraph_view_reference.

### Changed
- Move views helper functionality from degov_common to degov_paragraph_view_reference.
- Field header paragraphs is not required in any content type anymore.

### Fixed
- Iframe paragraph title now uses the same layout as other paragraphs.

## [1.9.0] - 23.10.17
### Added
- New field title has been added to media.
- Redirect module as a dependency to degov_pathauto.

### Changed
- Multi valued entity reference fields that have entity browser widget now have ability to sort items
  with media_browser Entity Browser.
- Scheduled updates field widget on nodes is now set to be a complex inline entity form.
- Caption fields on media are migrated to the new title field.

### Fixed
- Default permissions have been added for the degov_sitemap_settings module.
- Right sidebar paragraph block is now shown in the node preview mode.

## [1.8.0]
### Changed
- degov_views_helper functionality was transfered to degov_common module.
- Added control field for optional media download.
- Make field_image_caption for media (image) optional.

### Fixed
- Template suggestions now can be set from different modules for the same bundle of entity type.
- The image preview in Media reference paragraph preview mode is now not overlapping the edit buttons.

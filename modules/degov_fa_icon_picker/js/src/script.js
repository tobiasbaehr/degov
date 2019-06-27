import 'fontawesome';

(($) => {

  'use strict';

  /**
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.iconPicker = {
    attach: () => {
      jQuery('.form-item-link-0-options-attributes-class input').iconpicker().show(() => {  });
    }
  };

})(jQuery);

import sum from '../common-functions/sum';

(($) => {
  Drupal.behaviors.nodeForm = {
      attach: function() {
        let calc = new sum();
      }
  };
})(jQuery);

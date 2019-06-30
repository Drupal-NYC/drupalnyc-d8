(function ($, Drupal, drupalSettings) {
  Drupal.color = {
    logoChanged: false,
    callback: function callback(context, settings, $form) {
      if (!this.logoChanged) {
        $('.color-preview .color-preview-logo img').attr('src', drupalSettings.color.logo);
        this.logoChanged = true;
      }

      if (drupalSettings.color.logo === null) {
        $('div').remove('.color-preview-logo');
      }

      var $colorPreview = $form.find('.color-preview');
      var $colorPalette = $form.find('.js-color-palette');

      // Page background
      $colorPreview.css('backgroundColor', $colorPalette.find('input[name="palette[bg]"]').val());
      // Header background
      $colorPreview.find('.color-preview-header').css('backgroundColor', $colorPalette.find('input[name="palette[header]"]').val());
      // Footer background
      $colorPreview.find('.color-preview-footer').css('backgroundColor', $colorPalette.find('input[name="palette[footer]"]').val());
      // Sidebar background
      $colorPreview.find('.color-preview-sidebar').css('backgroundColor', $colorPalette.find('input[name="palette[sidebar]"]').val());

      // Text color
      $colorPreview.css('color', $colorPalette.find('input[name="palette[text]"]').val());
      // Header colors
      $colorPreview.find('h1, h2').css('color', $colorPalette.find('input[name="palette[headertext]"]').val());
      // Link color
      $colorPreview.find('a').css('color', $colorPalette.find('input[name="palette[link]"]').val());

      // Button background
      $colorPreview.find('.color-preview-button').css('backgroundColor', $colorPalette.find('input[name="palette[buttonbg]"]').val());
      // Button text
      $colorPreview.find('.color-preview-button').css('color', $colorPalette.find('input[name="palette[buttontext]"]').val());



    }
  };
})(jQuery, Drupal, drupalSettings);

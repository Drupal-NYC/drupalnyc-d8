/**
 * Components - Slider
 * Establishing a working library in order to attach the Splide slider
 * JavaScript library wherever needed. Splide controls, structure and other
 * documentation can be found at https://splidejs.com/guides/getting-started/
 *
 * - 01 - Globals
 * - 02 - Splide
 */

/*------------------------------------*
  01 - Globals
  Declared functions here will be used throughout the rest of this JavaScript
  document, throughout each declared behavior.
\*------------------------------------*/
((Drupal, once, Splide) => {
  Splide.defaults = {
    i18n: {
      prev: 'Previous slide',
      next: 'Next slide',
    },
  };

  /*------------------------------------*\
  02 - Splide
  Taking the external Splide NPM package library and using it to instantiate
  instances wherever necessary, with addition logic and functionality also
  attached.
\*------------------------------------*/

  Drupal.behaviors.slideshowMedia = {
    /**
     * Slider
     * Mounting function for Splide sliders. Also contains functionality for
     * pagination numbering counter.
     * @param givenElement: [object] Slider wrapper to act upon.
     */
    slider(givenElement) {
      const slideshow = new Splide(givenElement);
      slideshow.mount();

      let button = givenElement.querySelector('.splide__toggle');

      if (button) {
        let pausedClass = 'is-paused';

        // Remove the paused class and change the label to "Pause".
        slideshow.on('autoplay:play', function () {
          button.classList.remove(pausedClass);
          button.setAttribute('aria-label', Drupal.t('Pause Autoplay'));
        });

        // Add the paused class and change the label to "Play".
        slideshow.on('autoplay:pause', function () {
          button.classList.add(pausedClass);
          button.setAttribute('aria-label', Drupal.t('Start Autoplay'));
        });
      }

      const playPauseButton = givenElement.querySelector('.splide__toggle');
      if (playPauseButton) {
        const track = givenElement.querySelector('.splide__track');
        const trackId = track.getAttribute('id');
        playPauseButton.setAttribute('aria-controls', trackId);
      }
    },

    /*
     * Attach
     * Final build to the site for display within Drupal context.
     * - @param context: Website document context.
     */

    attach(context) {
      once('slideshowMedia', '.c-slideshow', context).forEach((slider) => {
        if (slider.length === 0) {
          return;
        }
        this.slider(slider);
      });
    },
  };
})(Drupal, once, Splide);

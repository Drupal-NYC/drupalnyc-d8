/******/ (() => { // webpackBootstrap
(function (Drupal, once) {
  Drupal.behaviors.accordion = {
    attach: function attach(context, settings) {
      // Accordion Expand/Collapse functionality
      once('accordionButtons', '.c-accordion__button', context).forEach(function (accordionButton) {
        accordionButton.addEventListener('click', function () {
          var accordionContent = document.getElementById(accordionButton.getAttribute('aria-controls'));
          var isExpanded = accordionButton.getAttribute('aria-expanded') === 'true';
          accordionButton.setAttribute('aria-expanded', !isExpanded);
          accordionContent.setAttribute('aria-hidden', isExpanded);
        });
      });
    }
  };
})(Drupal, once);
/******/ })()
;
//# sourceMappingURL=accordion.js.map
((Drupal, once) => {
  Drupal.behaviors.accordion = {
    attach(context, settings) {
      // Accordion Expand/Collapse functionality
      once('accordionButtons', '.c-accordion__button', context).forEach(
        (accordionButton) => {
          accordionButton.addEventListener('click', () => {
            const accordionContent = document.getElementById(
              accordionButton.getAttribute('aria-controls')
            );
            const isExpanded =
              accordionButton.getAttribute('aria-expanded') === 'true';
            accordionButton.setAttribute('aria-expanded', !isExpanded);
            accordionContent.setAttribute('aria-hidden', isExpanded);
          });
        }
      );
    },
  };
})(Drupal, once);

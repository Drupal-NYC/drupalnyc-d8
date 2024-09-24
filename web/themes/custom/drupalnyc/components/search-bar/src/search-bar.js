((Drupal, once) => {
  Drupal.behaviors.barSearch = {
    attach(context, _settings) {
      // Search bar Expand/Collapse functionality
      once('searchbar', '.c-search-bar__button', context).forEach(
        (searchToggle) => {
          // Mobile Media Query
          const isMobile = window.matchMedia('(max-width: 1023px)');
          const searchContent = document.getElementById(
            searchToggle.getAttribute('aria-controls')
          );
          const inputFields = searchContent.querySelectorAll('input');

          // If on mobile always display search content
          if (isMobile.matches) {
            searchContent.setAttribute('aria-hidden', false);
          }
          searchToggle.addEventListener('click', () => {
            const isExpanded =
              searchToggle.getAttribute('aria-expanded') === 'true';
            searchToggle.setAttribute('aria-expanded', !isExpanded);
            if (!isMobile.matches) {
              searchContent.setAttribute('aria-hidden', isExpanded);
            }
            // For each inputField set tabindex to -1
            inputFields.forEach((inputField) => {
              if (isExpanded) {
                inputField.setAttribute('tabindex', -1);
              } else {
                inputField.setAttribute('tabindex', 0);
              }
            });
            searchToggle.querySelector(
              '.c-search-bar__button-text'
            ).textContent = !isExpanded ? 'Close Search' : 'Expand Search';

            if (!isExpanded) {
              context.addEventListener('keydown', closeSearch, true);
            }
          });

          // Listen for escape key to close search
          function closeSearch(e) {
            if (e.key == 'Escape' || e.key == 'Esc') {
              context.removeEventListener('keydown', closeSearch, true);
              searchToggle.setAttribute('aria-expanded', 'false');
              if (!isMobile.matches) {
                searchContent.setAttribute('aria-hidden', 'true');
              }
              // For each inputField set tabindex to -1
              inputFields.forEach((inputField) => {
                inputField.setAttribute('tabindex', -1);
              });
              searchToggle.querySelector(
                '.c-search-bar__button-text'
              ).textContent = 'Expand Search';

              // Set focus for keyboard users
              searchToggle.focus();
            }
          }
        }
      );
    },
  };
})(Drupal, once);

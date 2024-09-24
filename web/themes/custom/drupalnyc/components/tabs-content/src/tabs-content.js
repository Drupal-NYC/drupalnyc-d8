/**
 * Components - Tabs Content
 * - 01 - Responsive Tabs
 * - 02 - Drupal Attach
 */

(function (Drupal, once) {
  /*------------------------------------*\
  01 - Responsive Tabs
  Responsive Tabs is used to set a tabbed panel set of elements on desktop
  devices, but collapse to accordions for mobile devices.
\*------------------------------------*/

  const responsiveTabs = (wrapper, breakpoint) => {
    const expandedClass = 'expanded';
    let mediaQuery;
    let navigation;
    let groups;
    let state;
    let triggers;

    /**
     * Trigger Collapse
     * Any actions needing to be taken when considering a trigger as collapsed.
     * @param element - [HTMLElement] Element to take action upon.
     * @see onTriggerClick
     * @see onTriggerKeydown
     * @see onMediaQueryChange
     */
    const collapseTrigger = (element) => {
      element.classList.remove(expandedClass);
      element.setAttribute('aria-selected', 'false');
      element.setAttribute('aria-expanded', 'false');

      if (state === 'desktop') {
        element.setAttribute('tabindex', '-1');
      }
    };

    /**
     * Trigger Expand
     * Any actions needing to be taken when considering a trigger to be expanded.
     * @param element - [HTMLElement] Element to take action upon.
     * @see onTriggerClick
     * @see onTriggerKeydown
     * @see onMediaQueryChange
     */
    const expandTrigger = (element) => {
      element.classList.add(expandedClass);
      element.setAttribute('aria-selected', 'true');
      element.setAttribute('aria-expanded', 'true');

      if (state === 'desktop') {
        element.removeAttribute('tabindex');
      }
    };

    /**
     * Trigger Click
     * Any events needing to be taken when a trigger has been clicked.
     * @param event - [object] all associated details pertaining to a specific event.
     * @see init
     */
    const onTriggerClick = (event) => {
      const controlID = event.currentTarget.getAttribute('aria-controls');
      const group = wrapper.querySelector(`#${controlID}`);

      if (state === 'desktop') {
        if (
          !event.currentTarget.classList.contains(expandedClass) &&
          !group.classList.contains(expandedClass)
        ) {
          // First set all triggers to be collapsed.
          triggers.forEach((trigger) => {
            collapseTrigger(trigger);
          });

          // First set all panels to be collapsed.
          groups.forEach((group) => {
            group.classList.remove(expandedClass);
          });

          // Finally open current trigger and associated panel.
          expandTrigger(event.currentTarget);
          group.classList.add(expandedClass);
        }
      }

      if (state === 'mobile') {
        if (event.currentTarget.classList.contains(expandedClass)) {
          // Collapse accordion.
          collapseTrigger(event.currentTarget);
          group.classList.remove(expandedClass);
        } else {
          // Expand accordion.
          expandTrigger(event.currentTarget);
          group.classList.add(expandedClass);
        }
      }
    };

    /**
     * Trigger Keydown
     * Any events needing to be taken when a trigger has been interacted with via keyboard.
     * @param event - [object] all associated details pertaining to a specific event.
     * @see init
     */
    const onTriggerKeydown = (event) => {
      const controlID = event.currentTarget.getAttribute('aria-controls');
      const group = wrapper.querySelector(`#${controlID}`);

      if (state === 'desktop') {
        if (event.key === 'ArrowLeft') {
          // Set focus to last trigger if on the first.
          // Otherwise, set focus to the trigger to the left.
          if (event.currentTarget === triggers[0]) {
            setTimeout((setFocus) => {
              triggers[triggers.length - 1].focus();
            }, 1);
          } else {
            setTimeout((setFocus) => {
              event.target.previousElementSibling.focus();
            }, 1);
          }
        }

        if (event.key === 'ArrowRight') {
          // Set focus to first trigger if on the last.
          // Otherwise, set focus to the trigger to the right.
          if (event.currentTarget === triggers[triggers.length - 1]) {
            setTimeout((setFocus) => {
              triggers[0].focus();
            }, 1);
          } else {
            setTimeout((setFocus) => {
              event.target.nextElementSibling.focus();
            }, 1);
          }
        }

        if (event.key === 'Home') {
          setTimeout((setFocus) => {
            triggers[0].focus();
          }, 1);
        }

        if (event.key === 'End') {
          setTimeout((setFocus) => {
            triggers[triggers.length - 1].focus();
          }, 1);
        }
      }

      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();

        if (state === 'desktop') {
          if (
            !event.currentTarget.classList.contains(expandedClass) &&
            !group.classList.contains(expandedClass)
          ) {
            // First set all triggers to be collapsed.
            triggers.forEach((trigger) => {
              collapseTrigger(trigger);
            });

            // First set all panels to be collapsed.
            groups.forEach((group) => {
              group.classList.remove(expandedClass);
            });

            // Finally open current trigger and associated panel.
            expandTrigger(event.currentTarget);
            group.classList.add(expandedClass);
          }
        }

        if (state === 'mobile') {
          if (event.currentTarget.classList.contains(expandedClass)) {
            // Collapse accordion.
            collapseTrigger(event.currentTarget);
            group.classList.remove(expandedClass);
          } else {
            // Expand accordion.
            expandTrigger(event.currentTarget);
            group.classList.add(expandedClass);
          }
        }
      }
    };

    /**
     * Media Query Change
     * Any events needing to be taken when a specific breakpoint has been activated.
     * @see init
     */
    const onMediaQueryChange = () => {
      if (mediaQuery.matches) {
        state = 'desktop';

        // Grab all triggers from within panels and move them to the navigation.
        // Also reset the first panel to be active.
        triggers.forEach((trigger, index) => {
          navigation.append(trigger);

          if (index !== 0) {
            collapseTrigger(trigger);
            groups[index].classList.remove(expandedClass);
          } else {
            expandTrigger(trigger);
            groups[0].classList.add(expandedClass);
          }
        });
      } else {
        state = 'mobile';

        // Grab all triggers from within the navigation and move them to respective panels.
        triggers.forEach((trigger, index) => {
          groups[index].prepend(trigger);
          trigger.removeAttribute('tabindex');
        });
      }
    };

    /**
     * Initialization
     * Add any and all functionality as a singular program, dynamically setting
     * element variables, states and event listeners.
     * @param wrapper - [HTMLObject] Element that contains all elements necessary
     * @param breakpoint - [Integer] Numeric value representing the pixel width at which to switch tabs and accordions.
     * for responsive tabs functionality.
     */
    const init = (wrapper, breakpoint) => {
      navigation = wrapper.querySelector('.c-tabs-content__navigation');
      groups = wrapper.querySelectorAll('.c-tabs-content__group');
      triggers = wrapper.querySelectorAll('.c-tabs-content__trigger');

      triggers.forEach((trigger) => {
        trigger.addEventListener('click', onTriggerClick);
        trigger.addEventListener('keydown', onTriggerKeydown);
      });

      // Device or viewport change listener and on load.
      mediaQuery = window.matchMedia(`(min-width: ${breakpoint}px)`);
      mediaQuery.addEventListener('change', onMediaQueryChange);
      onMediaQueryChange();
    };

    init(wrapper, breakpoint);
  };

  /*------------------------------------*\
  02 - Drupal Attach
\*------------------------------------*/

  Drupal.behaviors.drupalnycTabsContent = {
    attach(context) {
      const tabs = once(
        'drupalnyc-tabs-content',
        context.querySelectorAll('.c-tabs-content')
      );

      if (tabs.length !== 0) {
        tabs.forEach((tab) => {
          const breakpoint = tab.getAttribute('data-tabs-content-breakpoint');
          responsiveTabs(tab, breakpoint);
        });
      }
    },
  };
})(Drupal, once);

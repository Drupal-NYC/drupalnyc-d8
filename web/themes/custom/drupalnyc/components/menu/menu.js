/******/ (() => { // webpackBootstrap
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
(function (Drupal) {
  Drupal.behaviors.menuControl = {
    attach: function attach(context) {
      once('menuControl', '.c-menu', context).forEach(function (menuContainer) {
        /**
         * Accessible Menu
         * This file is based on content that is licensed according to the W3C Software License at
         * https://www.w3.org/copyright/software-license/
         *
         * - 01 - Utility Functions
         * - 02 - MenuLink Class (Base)
         * - 03 - MenuButton Class (Extends MenuLink)
         * - 04 - Constants
         * - 05 - Initialize Menus
         * - 06 - Drupal Aria Controls
         */

        /*-------------------------------------------------------------------------------*\
            - 01 - Utility Functions
            closeMobile separate function to close mobile menu, can be called in Classes
            mobileMenuNavigationControl controls mobile menu when button is clicked
            Custom listener for if user has clicked away from menu
        \*-------------------------------------------------------------------------------*/

        /**
         * Closes the mobile navigation menu and manages focus based on the key pressed.
         *
         * @param {string} key - The key that was pressed to trigger the function.
         *
         * This function removes the "js-prevent-scroll" class from the body and the "checked" class from the mobile navigation button,
         * and sets the "aria-expanded" attribute of the mobile navigation button to "false".
         * If the "Esc" or "Escape" key was pressed, the function sets focus to the mobile navigation button.
         */
        function closeMobile(key) {
          document.body.classList.remove('js-prevent-scroll');
          mobileNavButton.classList.remove('checked');
          mobileNavButton.setAttribute('aria-expanded', 'false');

          // It was escape key, set focus
          if (key === 'Esc' || key === 'Escape') {
            mobileNavButton.focus();
          }
        }

        /**
         * Controls the behavior of the mobile navigation menu based on user interaction.
         *
         * @param {Event} event - The event that triggered the function.
         *
         * This function checks the "aria-expanded" attribute of the mobile navigation button.
         * If the attribute is "false", the function adds the "js-prevent-scroll" class to the body,
         * the "checked" class to the mobile navigation button, and sets the "aria-expanded" attribute of the button to "true".
         * If the attribute is not "false", the function calls the `closeMobile` function to close the menu.
         * The function then stops the propagation of the event and prevents the default action of the event.
         */
        function mobileMenuNavigationControl(event) {
          var isMenuClosed = mobileNavButton.getAttribute('aria-expanded') === 'false';

          // Toggle overlay & checked classes
          if (isMenuClosed) {
            document.body.classList.add('js-prevent-scroll');
            mobileNavButton.classList.add('checked');
            mobileNavButton.setAttribute('aria-expanded', 'true');
          } else {
            closeMobile();
          }
          event.stopPropagation();
          event.preventDefault();
        }

        /**
         * Event listener for click events on the window.
         *
         * This function checks if the mobile navigation menu is open. If it is, it checks if the click event occurred outside of the navigation menu.
         * If the click event occurred outside the navigation menu, the function closes the menu and prevents the click event from propagating to other elements.
         *
         * @param {Event} event - The click event.
         */
        window.addEventListener('click', function (event) {
          var isMenuOpen = document.body.classList.contains('js-prevent-scroll');
          if (isMenuOpen) {
            var navMenu = menuContainer.getAttribute('id');

            // Check if the click event is outside the navigation menu
            var isClickOutsideNav = navMenu && !navMenu.contains(event.target) && navMenu.parentElement !== event.target;
            if (isClickOutsideNav) {
              // Close the menu
              document.body.classList.remove('js-prevent-scroll');
              mobileNavButton.classList.remove('checked');
              mobileNavButton.setAttribute('aria-expanded', 'false');

              // Prevent the click event from propagating to other elements
              event.stopPropagation();
              event.preventDefault();
            }
          }
        });

        /**
         * Event listener for keydown events on the window.
         *
         * This function checks if the mobile navigation menu is open and if the Escape key was pressed.
         * If both conditions are true, it calls the `closeMobile` function to close the menu.
         *
         * @param {KeyboardEvent} e - The keydown event.
         */
        window.addEventListener('keydown', function (e) {
          // Is the menu actually open?
          if (mobileNavButton && mobileNavButton.getAttribute('aria-expanded') === 'true' && (e.key === 'Esc' || e.key === 'Escape')) {
            // Close mobile menu
            closeMobile('Esc');
          }
        });

        /*------------------------------------------------------------------------*\
            - 02 - MenuLink Class (Base)
            The base class for attaching functions to menu links.
        \*------------------------------------------------------------------------*/

        /**
         * `MenuLinks` is a class that manages the behavior of menu links in a navigation menu.
         * It provides methods to handle keyboard navigation within the menu, including arrow keys, tab, and escape.
         *
         * @class
         * @param {HTMLElement} domNode - The root node of the menu.
         *
         * @property {HTMLElement} domNode - The root node of the menu.
         * @property {Array} menuitemNodes - An array of all menu item nodes in the menu.
         * @property {HTMLElement} firstMenuitem - The first menu item node in the menu.
         * @property {HTMLElement} lastMenuitem - The last menu item node in the menu.
         *
         * @method setFocusToMenuitem - Sets focus to a specific menu item.
         * @method setFocusToFirstMenuitem - Sets focus to the first menu item.
         * @method setFocusToLastMenuitem - Sets focus to the last menu item.
         * @method setFocusToPreviousMenuitem - Sets focus to the menu item before the current one.
         * @method setFocusToNextMenuitem - Sets focus to the menu item after the current one.
         * @method onMenuitemKeydown - Handles keydown events on menu items.
         * @method closeRelatedButton - Closes the related button of a menu item.
         * @method handleNestedMenu - Handles navigation in nested menus.
         * @method handleParentMenuItem - Handles navigation to parent menu items.
         * @method focusOnNextParentMenuLink - Sets focus to the next parent menu link.
         * @method focusOnPrevParentMenuLink - Sets focus to the previous parent menu link.
         * @method handleNonNestedMenu - Handles navigation in non-nested menus.
         * @method handleUpArrow - Handles the up arrow key.
         * @method handleDownArrow - Handles the down arrow key.
         * @method handleLeftArrow - Handles the left arrow key.
         * @method handleRightArrow - Handles the right arrow key.
         * @method handleTab - Handles the tab key.
         * @method handleEscape - Handles the escape key.
         */
        var MenuLinks = /*#__PURE__*/function () {
          function MenuLinks(domNode) {
            var _this = this;
            _classCallCheck(this, MenuLinks);
            this.domNode = domNode;
            this.menuitemNodes = Array.from(domNode.querySelectorAll('.menu__link'));
            if (domNode && domNode.nodeName === 'BUTTON') {
              // If it is a button, do nothing
            } else if (this.menuitemNodes.length > 0) {
              this.firstMenuitem = this.menuitemNodes[0];
              this.lastMenuitem = this.menuitemNodes[this.menuitemNodes.length - 1];
              this.menuitemNodes.forEach(function (item) {
                item.addEventListener('keydown', _this.onMenuitemKeydown.bind(_this));
              });
            } else {
              console.warn('No menu items found');
            }
          }

          /**
           * Sets focus to the specified menu item.
           *
           * @param {HTMLElement} newMenuitem - The menu item to focus on.
           */
          return _createClass(MenuLinks, [{
            key: "setFocusToMenuitem",
            value: function setFocusToMenuitem(newMenuitem) {
              newMenuitem.focus();
            }

            /**
             * Sets focus to the first menu item in the menu.
             */
          }, {
            key: "setFocusToFirstMenuitem",
            value: function setFocusToFirstMenuitem() {
              this.setFocusToMenuitem(this.firstMenuitem);
            }

            /**
             * Sets focus to the last menu item in the menu.
             */
          }, {
            key: "setFocusToLastMenuitem",
            value: function setFocusToLastMenuitem() {
              this.setFocusToMenuitem(this.lastMenuitem);
            }

            /**
             * Sets focus to the menu item before the current one. If the current menu item is the first one,
             * it sets focus to the last menu item.
             *
             * @param {HTMLElement} currentMenuitem - The current menu item.
             * @returns {HTMLElement} The new menu item that received focus.
             */
          }, {
            key: "setFocusToPreviousMenuitem",
            value: function setFocusToPreviousMenuitem(currentMenuitem) {
              var index = this.menuitemNodes.indexOf(currentMenuitem);
              var newMenuitem = index === 0 ? this.lastMenuitem : this.menuitemNodes[index - 1];
              this.setFocusToMenuitem(newMenuitem);
              return newMenuitem;
            }

            /**
             * Sets focus to the menu item after the current one. If the current menu item is the last one,
             * it sets focus to the first menu item.
             *
             * @param {HTMLElement} currentMenuitem - The current menu item.
             * @returns {HTMLElement} The new menu item that received focus.
             */
          }, {
            key: "setFocusToNextMenuitem",
            value: function setFocusToNextMenuitem(currentMenuitem) {
              var index = this.menuitemNodes.indexOf(currentMenuitem);
              var newMenuitem = index === this.menuitemNodes.length - 1 ? this.firstMenuitem : this.menuitemNodes[index + 1];
              this.setFocusToMenuitem(newMenuitem);
              return newMenuitem;
            }

            /**
             * Handles keydown events on menu link items.
             *
             * @param {KeyboardEvent} event - The keydown event.
             * This method checks the key pressed and performs the corresponding action:
             * - Up or ArrowUp: Calls the handleUpArrow method and prevents the default action.
             * - Down or ArrowDown: Calls the handleDownArrow method and prevents the default action.
             * - Left or ArrowLeft: Calls the handleLeftArrow method.
             * - Right or ArrowRight: Calls the handleRightArrow method.
             * - Tab: Calls the handleTab method.
             * - Escape or Esc: Calls the handleEscape method and, if not on mobile, prevents the default action and stops propagation.
             */
          }, {
            key: "onMenuitemKeydown",
            value: function onMenuitemKeydown(event) {
              var target = event.currentTarget;
              var key = event.key;
              if (event.ctrlKey || event.altKey || event.metaKey) {
                return;
              }
              switch (key) {
                case 'Up':
                case 'ArrowUp':
                  this.handleUpArrow(target);
                  event.preventDefault();
                  break;
                case 'Down':
                case 'ArrowDown':
                  this.handleDownArrow(target);
                  event.preventDefault();
                  break;
                case 'Left':
                case 'ArrowLeft':
                  this.handleLeftArrow(target);
                  break;
                case 'Right':
                case 'ArrowRight':
                  this.handleRightArrow(target);
                  break;
                case 'Tab':
                  this.handleTab(event, target);
                  break;
                case 'Escape':
                case 'Esc':
                  this.handleEscape(target);

                  // Only prevent default if on mobile
                  if (!isMobile.matches) {
                    event.stopPropagation();
                    event.preventDefault();
                  }
                  break;
                default:
                  break;
              }
            }

            /*----------------------------------------------*\
              Supporting functions Keydown for MenuLinks
            \*----------------------------------------------*/

            /**
             * Closes the related button of the target element.
             *
             * @param {HTMLElement} target - The target element.
             * This method finds the related button of the target element (which is assumed to be two levels up and one level back in the DOM tree),
             * and sets its "aria-expanded" attribute to "false".
             */
          }, {
            key: "closeRelatedButton",
            value: function closeRelatedButton(target) {
              var relatedButton = target.parentNode.parentNode.previousElementSibling;
              if (relatedButton) {
                relatedButton.setAttribute('aria-expanded', 'false');
              }
            }

            /**
             * Handles the nested menu based on the direction of navigation.
             *
             * @param {HTMLElement} target - The current active menu item.
             * @param {HTMLElement} parentMenu - The parent menu of the current active menu item.
             * @param {string} direction - The direction of navigation ("right" or "left").
             *
             * This method finds the parent menu item of the current active menu item and,
             * if it exists, calls the `handleParentMenuItem` method with the target, parent menu item, and direction as arguments.
             */
          }, {
            key: "handleNestedMenu",
            value: function handleNestedMenu(target, parentMenu, direction) {
              // Find the parent level item
              var parentMenuItem = parentMenu.parentElement.querySelector('.menu__link');
              if (parentMenuItem) {
                if (direction === 'right') {
                  this.handleParentMenuItem(target, parentMenuItem, direction);
                } else if (direction === 'left') {
                  this.handleParentMenuItem(target, parentMenuItem, direction);
                }
              }
            }

            /**
             * Handles the parent menu item based on the direction of navigation.
             *
             * @param {HTMLElement} target - The current active menu item.
             * @param {HTMLElement} parentMenuItem - The parent menu item of the current active menu item.
             * @param {string} direction - The direction of navigation ("right" or "left").
             *
             * This method navigates between sibling menu items at the parent level based on the direction.
             * It finds the next (for "right") or previous (for "left") sibling menu item of the parent menu item.
             * If such a sibling menu item exists, it finds its associated link.
             * If the link exists, it calls the appropriate method to focus on it, based on the direction.
             */
          }, {
            key: "handleParentMenuItem",
            value: function handleParentMenuItem(target, parentMenuItem, direction) {
              // Find the next or previous sibling of the parent level item based on direction
              var siblingMenuItem = direction === 'right' ? parentMenuItem.parentElement.nextElementSibling : parentMenuItem.parentElement.previousElementSibling;
              if (siblingMenuItem) {
                var siblingMenuLink = siblingMenuItem.querySelector('.menu__link');
                if (siblingMenuLink) {
                  direction === 'right' ? this.focusOnNextParentMenuLink(target, siblingMenuLink) : this.focusOnPrevParentMenuLink(target, siblingMenuLink);
                }
              }
            }

            /**
             * Focuses on the next parent menu link and closes the related button of the target.
             *
             * @param {HTMLElement} target - The current active menu item.
             * @param {HTMLElement} nextParentMenuLink - The next parent menu link to focus on.
             *
             * This method focuses on the next parent menu link and calls the `closeRelatedButton` method with the target as an argument.
             */
          }, {
            key: "focusOnNextParentMenuLink",
            value: function focusOnNextParentMenuLink(target, nextParentMenuLink) {
              // Focus on the next sibling of the parent level item
              nextParentMenuLink.focus();
              this.closeRelatedButton(target);
            }

            /**
             * Focuses on the previous parent menu link and closes the related button of the target.
             *
             * @param {HTMLElement} target - The current active menu item.
             * @param {HTMLElement} prevParentMenuLink - The previous parent menu link to focus on.
             *
             * This method focuses on the previous parent menu link and calls the `closeRelatedButton` method with the target as an argument.
             */
          }, {
            key: "focusOnPrevParentMenuLink",
            value: function focusOnPrevParentMenuLink(target, prevParentMenuLink) {
              // Focus on the previous sibling of the parent level item
              prevParentMenuLink.focus();
              this.closeRelatedButton(target);
            }

            /**
             * Handles the navigation for non-nested menus based on the direction of navigation.
             *
             * @param {HTMLElement} target - The current active menu item.
             * @param {HTMLElement} parentMenu - The parent menu of the current active menu item.
             * @param {string} direction - The direction of navigation ("right" or "left").
             *
             * This method finds the next or previous sibling menu item of the current active menu item based on the direction.
             * If such a sibling menu item exists and it contains a link, it focuses on that link.
             * Otherwise, it focuses on the first menu item link in the parent menu (or the last one if the direction is "left").
             */
          }, {
            key: "handleNonNestedMenu",
            value: function handleNonNestedMenu(target, parentMenu, direction) {
              var siblingElement = direction === 'right' ? target.parentElement.nextElementSibling : target.parentElement.previousElementSibling;
              var menuItems = Array.from(parentMenu.querySelectorAll('.menu__item > .menu__link'));
              if (direction === 'left') {
                menuItems = menuItems.reverse();
              }
              var siblingLink = siblingElement && siblingElement.querySelector('.menu__link');
              if (siblingLink) {
                siblingLink.focus();
              } else {
                menuItems[0].focus(); // Wrap to the front or end depending on direction
              }
            }

            /*------------------------------------*\
                Keydown functions for MenuLinks
            \*------------------------------------*/

            /**
             * Handles the 'Up Arrow' key event for a menu item.
             *
             * @param {HTMLElement} target - The current active menu item.
             *
             * This method finds the previous sibling menu item of the current active menu item.
             * If such a sibling menu item exists and it contains a link, it focuses on that link.
             * Otherwise, it finds the closest parent 'ul' element, gets all its direct child menu item links,
             * and focuses on the last one.
             */
          }, {
            key: "handleUpArrow",
            value: function handleUpArrow(target) {
              var prev = target.parentElement.previousElementSibling;
              var prevLink = prev && prev.querySelector('.menu__link');
              if (prevLink) {
                prevLink.focus();
              } else {
                var closestUl = target.parentElement.closest('ul');
                if (closestUl) {
                  var prevMenuItems = closestUl.querySelectorAll(':scope > .menu__item > .menu__link');
                  if (prevMenuItems.length > 0) {
                    prevMenuItems[prevMenuItems.length - 1].focus();
                  }
                }
              }
            }

            /**
             * Handles the 'Up Arrow' key event for a menu item.
             *
             * @param {HTMLElement} target - The current active menu item.
             *
             * This method finds the previous sibling menu item of the current active menu item.
             * If such a sibling menu item exists and it contains a link, it focuses on that link.
             * Otherwise, it finds the closest parent 'ul' element, gets all its direct child menu item links,
             * and focuses on the last one.
             */
          }, {
            key: "handleDownArrow",
            value: function handleDownArrow(target) {
              var next = target.parentElement.nextElementSibling;
              var nextLink = next && next.querySelector('.menu__link');
              if (nextLink) {
                nextLink.focus();
              } else {
                var parent = target.parentElement.parentElement;
                var links = parent.querySelectorAll('.menu__item > .menu__link');
                if (links.length > 0) {
                  links[0].focus();
                }
              }
            }

            /**
             * Handles the 'Left Arrow' key event for a menu item.
             *
             * @param {HTMLElement} target - The current active menu item.
             *
             * This method checks if the parent menu of the current active menu item is a nested menu with data-depth > 0.
             * If it is, it calls the `handleNestedMenu` method with the target, the parent menu, and "left" as arguments.
             * Otherwise, it calls the `handleNonNestedMenu` method with the target, the parent menu, and "left" as arguments.
             */
          }, {
            key: "handleLeftArrow",
            value: function handleLeftArrow(target) {
              var parentMenu = target.parentElement.parentElement;
              // Check if the parent menu is a nested menu with data-depth > 0
              if (parentMenu.dataset.depth && parentMenu.dataset.depth > 0) {
                this.handleNestedMenu(target, parentMenu, 'left');
              } else {
                this.handleNonNestedMenu(target, parentMenu, 'left');
              }
            }

            /**
             * Handles the 'Right Arrow' key event for a menu item.
             *
             * @param {HTMLElement} target - The current active menu item.
             *
             * This method determines the type of the parent menu of the current active menu item based on its data-depth attribute.
             * If the parent menu is a nested menu (data-depth > 0), it delegates the handling to the `handleNestedMenu` method.
             * If the parent menu is not a nested menu (data-depth <= 0), it delegates the handling to the `handleNonNestedMenu` method.
             * Both `handleNestedMenu` and `handleNonNestedMenu` methods are called with the target, the parent menu, and "right" as arguments.
             */
          }, {
            key: "handleRightArrow",
            value: function handleRightArrow(target) {
              var parentMenu = target.parentElement.parentElement;
              // Check if the parent menu is a nested menu with data-depth > 0
              if (parentMenu.dataset.depth > 0) {
                this.handleNestedMenu(target, parentMenu, 'right');
              } else {
                this.handleNonNestedMenu(target, parentMenu, 'right');
              }
            }

            /**
             * Handles the 'Tab' key event for a menu item.
             *
             * @param {Event} event - The key event.
             * @param {HTMLElement} target - The current active menu item.
             *
             * This method checks if the 'Shift' key is also pressed during the 'Tab' key event.
             * If it is, and the current active menu item is the first child of a nested menu (depth > 0),
             * the method simulates a click on the sibling button of the parent menu (if it exists and is a button),
             * which should close the menu if the button is set up to toggle the menu's visibility.
             */
          }, {
            key: "handleTab",
            value: function handleTab(event, target) {
              if (event.shiftKey && target.parentElement.parentElement.dataset.depth > 0) {
                var parentMenu = target.parentElement.parentElement;
                var siblingButton = parentMenu.previousElementSibling;
                if (parentMenu.children[0] === target.parentElement && (siblingButton === null || siblingButton === void 0 ? void 0 : siblingButton.tagName) === 'BUTTON') {
                  siblingButton.click();
                }
              }
            }

            /**
             * Handles the 'Escape' key event for a menu item.
             *
             * @param {HTMLElement} target - The current active menu item.
             *
             * This method assumes the related button is a parent of the target.
             * It finds the related button and if it exists, sets its 'aria-expanded' attribute to 'false' and focuses on it.
             * This effectively closes the menu and returns focus to the related button.
             */
          }, {
            key: "handleEscape",
            value: function handleEscape(target) {
              // Assuming the related button is a parent of the target
              var relatedButton = target.parentNode.parentNode.previousElementSibling;
              if (relatedButton) {
                relatedButton.setAttribute('aria-expanded', 'false');
                relatedButton.focus();
              }
            }
          }]);
        }();
        /*---------------------------------------------------------------*\
            - 03 - MenuButton Class (Extends MenuLink)
            The base class for attaching functions to menu buttons
        \*----------------------------------------------------------------*/
        /**
         * `MenuButtons` is a class that extends `MenuLinks` to handle the functionality of menu buttons.
         *
         * @extends MenuLinks
         *
         * @property {HTMLElement} buttonNode - The button element in the menu.
         * @property {HTMLElement} menuNode - The related ul menu for the button.
         * @property {Array} menuitemNodes - The menu items in the menu.
         * @property {HTMLElement} firstMenuitem - The first menu item in the menu.
         * @property {HTMLElement} lastMenuitem - The last menu item in the menu.
         *
         * The constructor initializes the base `MenuLinks` class with the main menu, sets the `aria-expanded` attribute of the button to 'false',
         * and attaches event listeners to the main menu button, each menu item, and the window.
         *
         * The class includes methods to handle button and menu item keydown events, button click events, and background mousedown events,
         * as well as methods to open and close the menu, check if the menu is open, and focus on the first menu item.
         */
        var MenuButtons = /*#__PURE__*/function (_MenuLinks) {
          function MenuButtons(menuContainer) {
            var _this2;
            _classCallCheck(this, MenuButtons);
            // Find the related ul menu for this button
            var relativeMenu = menuContainer.parentNode.querySelector('button');
            // Initialize the base MenuLinks class with the main menu
            _this2 = _callSuper(this, MenuButtons, [relativeMenu]);
            _this2.buttonNode = menuContainer.parentNode.querySelector('button');
            _this2.buttonNode.setAttribute('aria-expanded', 'false');
            if (relativeMenu) {
              _this2.menuNode = relativeMenu;
            } else {
              console.warn('Relative menu not found for button');
            }

            // Attach event listeners to the main menu button
            _this2.buttonNode.addEventListener('keydown', _this2.onButtonKeydown.bind(_this2));
            _this2.buttonNode.addEventListener('click', _this2.onButtonClick.bind(_this2));

            // Attach event listeners to each menu item
            _this2.menuitemNodes.forEach(function (menuitem, index) {
              menuitem.addEventListener('keydown', _this2.onMenuitemKeydown.bind(_this2));
              if (index === 0) {
                _this2.firstMenuitem = menuitem;
              }
              _this2.lastMenuitem = menuitem;
            });
            window.addEventListener('mousedown', _this2.onBackgroundMousedown.bind(_this2), true);
            return _this2;
          }

          /**
           * Focuses on the first menu item of the nested list related to the given button.
           *
           * @param {HTMLElement} button - The button related to the nested list.
           *
           * This method finds the nested list related to the given button.
           * If the nested list exists, it finds the first menu item in the list and focuses on it.
           */
          _inherits(MenuButtons, _MenuLinks);
          return _createClass(MenuButtons, [{
            key: "focusFirstMenuItem",
            value: function focusFirstMenuItem(button) {
              var nestedList = button.parentNode.querySelector('ul.menu');
              if (nestedList) {
                var firstMenuItem = nestedList.querySelector('.menu__link');
                if (firstMenuItem) {
                  firstMenuItem.focus();
                }
              }
            }

            /**
             * Handles keydown events on the menu button.
             *
             * @param {Event} event - The keydown event.
             *
             * This method checks the key pressed during the event and performs actions based on the key:
             * - 'Up' or 'ArrowUp': Calls the `handleUpArrow` method with the button node.
             * - 'Down' or 'ArrowDown': If the next sibling of the button node has a 'data-depth' attribute of '1', opens the popup, focuses on the first menu item, and prevents the default action. Otherwise, calls the `handleDownArrow` method with the button node and prevents the default action.
             * - 'Left' or 'ArrowLeft': If the next sibling of the button node does not have a 'data-depth' attribute of '1', closes the popup and focuses on the button node. Otherwise, calls the `handleLeftArrow` method with the button node.
             * - 'Right' or 'ArrowRight': If the next sibling of the button node does not have a 'data-depth' attribute of '1', opens the popup and focuses on the first menu item. Otherwise, calls the `handleRightArrow` method with the button node.
             * - 'Esc' or 'Escape': Closes the popup and prevents the default action.
             *
             * If the 'ctrl', 'alt', or 'meta' key is pressed during the event, the method returns without doing anything.
             */
          }, {
            key: "onButtonKeydown",
            value: function onButtonKeydown(event) {
              var key = event.key;
              if (event.ctrlKey || event.altKey || event.metaKey) {
                return;
              }
              switch (key) {
                case 'Up':
                case 'ArrowUp':
                  this.handleUpArrow(this.buttonNode);
                  break;
                case 'Down':
                case 'ArrowDown':
                  if (this.buttonNode.nextElementSibling.getAttribute('data-depth') == '1') {
                    this.openPopup();
                    this.focusFirstMenuItem(event.target);
                    event.preventDefault();
                  } else {
                    this.handleDownArrow(this.buttonNode);
                    event.preventDefault();
                  }
                  break;
                case 'Left':
                case 'ArrowLeft':
                  if (this.buttonNode.nextElementSibling.getAttribute('data-depth') !== '1') {
                    this.closePopup();
                    this.buttonNode.focus();
                  } else {
                    this.handleLeftArrow(this.buttonNode);
                  }
                  break;
                case 'Right':
                case 'ArrowRight':
                  if (this.buttonNode.nextElementSibling.getAttribute('data-depth') !== '1') {
                    this.openPopup();
                    this.focusFirstMenuItem(event.target);
                  } else {
                    this.handleRightArrow(this.buttonNode);
                  }
                  break;
                case 'Esc':
                case 'Escape':
                  this.closePopup();
                  event.preventDefault();
                  break;
              }
            }

            /**
             * Handles click events on the menu button.
             *
             * @param {Event} event - The click event.
             *
             * This method checks if the menu is open:
             * - If the menu is open, it calls the `closePopup` method to close the menu.
             * - If the menu is not open, it calls the `openPopup` method to open the menu.
             *
             * After handling the menu, it stops the propagation of the event and prevents the default action.
             */
          }, {
            key: "onButtonClick",
            value: function onButtonClick(event) {
              if (this.isOpen()) {
                this.closePopup();
              } else {
                this.openPopup();
              }
              event.stopPropagation();
              event.preventDefault();
            }

            /**
             * Checks if the menu is open.
             *
             * @returns {boolean} - Returns true if the menu is open, false otherwise.
             *
             * This method checks the 'aria-expanded' attribute of the button node.
             * If the attribute is 'true', the method returns true, indicating that the menu is open.
             * If the attribute is not 'true', the method returns false, indicating that the menu is not open.
             */
          }, {
            key: "isOpen",
            value: function isOpen() {
              return this.buttonNode.getAttribute('aria-expanded') === 'true';
            }

            /**
             * Opens the popup menu.
             *
             * This method sets the 'aria-expanded' attribute of the button node to 'true',
             * indicating that the associated popup menu is open.
             */
          }, {
            key: "openPopup",
            value: function openPopup() {
              this.buttonNode.setAttribute('aria-expanded', 'true');
            }

            /**
             * Closes the popup menu.
             *
             * This method sets the 'aria-expanded' attribute of the button node to 'false',
             * indicating that the associated popup menu is closed.
             */
          }, {
            key: "closePopup",
            value: function closePopup() {
              this.buttonNode.setAttribute('aria-expanded', 'false');
            }

            /**
             * Handles the mousedown event on the background.
             *
             * @param {Event} event - The mousedown event.
             *
             * This method checks if the mousedown event occurred outside the menu container.
             * If it did and the menu is open and the device is not mobile, it sets focus to the button node and closes the popup menu.
             */
          }, {
            key: "onBackgroundMousedown",
            value: function onBackgroundMousedown(event) {
              if (!menuContainer.contains(event.target)) {
                if (this.isOpen() && !isMobile.matches) {
                  this.buttonNode.focus();
                  this.closePopup();
                }
              }
            }
          }]);
        }(MenuLinks);
        /*----------------------------------------------*\
            - 04 - Constants
            Select DOM constants for mobile menu
        \*----------------------------------------------*/
        var mobileNavButtonId, mobileBreakpoint;
        if (menuContainer.hasAttribute('data-mobile')) {
          mobileNavButtonId = menuContainer.getAttribute('data-mobile').replace('#', '');
        }

        // Get the mobileNavButton element using the ID
        var mobileNavButton = document.getElementById(mobileNavButtonId);
        if (menuContainer.hasAttribute('data-breakpoint')) {
          mobileBreakpoint = menuContainer.getAttribute('data-breakpoint').replace('#', '');
        }

        // Mobile Media Query
        var isMobile = window.matchMedia('(max-width: ' + mobileBreakpoint + 'px)');

        /*--------------------------------------------*\
            - 05 - Initialize Menus
            Initialize Menus for keyboard navigation
        \*--------------------------------------------*/

        /**
         * Initializes the menus within a given container.
         *
         * @param {HTMLElement} menuContainer - The container within which to initialize menus.
         *
         * This function selects all button elements within the menuContainer and initializes a new MenuButtons instance for each.
         * It then selects all elements with the class 'menu__item' within the menuContainer.
         * For each 'menu__item' that does not contain a button, it initializes a new MenuLinks instance.
         */
        function initializeMenus(menuContainer) {
          // Select all buttons in the menuContainer
          var buttons = menuContainer.querySelectorAll('button');

          // Initialize MenuButtons for each button
          for (var i = 0; i < buttons.length; i++) {
            new MenuButtons(buttons[i]);
          }

          // Initialize main menu list
          var menuItems = menuContainer.querySelectorAll('.menu__item');
          for (var _i = 0; _i < menuItems.length; _i++) {
            // Attach different class based on button or link
            if (menuItems[_i].querySelector('button') === null) {
              new MenuLinks(menuItems[_i]);
            }
          }
        }

        // Call the function with the menuContainer as argument
        initializeMenus(menuContainer);

        // Attach mobile menu listeners
        if (mobileNavButton) {
          mobileNavButton.addEventListener('click', mobileMenuNavigationControl);
        }
      });
    }
  };
  /*------------------------------------*\
    - 06 - Drupal Aira Controls
    Attach aria controls to menu items
  \*------------------------------------*/
  Drupal.behaviors.ariaControls = {
    attach: function attach(context) {
      once('ariaControls', '.c-menu', context).forEach(function (menu) {
        // Find top level menu buttons
        var buttons = menu.querySelectorAll('ul[data-depth].menu > li button.menu__link');

        // Add aria controls for buttons & related submenus
        buttons.forEach(function (button) {
          var id = button.getAttribute('data-plugin-id');
          button.setAttribute('aria-haspopup', 'true');
          var submenu = button.nextElementSibling;
          if (submenu) {
            var submenuId = "panel-".concat(id);
            submenu.setAttribute('id', submenuId);
            button.setAttribute('aria-controls', submenuId);
            button.setAttribute('aria-label', button.textContent.trim());
          }
        });
      });
    }
  };
})(Drupal);
/******/ })()
;
//# sourceMappingURL=menu.js.map
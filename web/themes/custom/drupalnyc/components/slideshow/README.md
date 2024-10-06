# Slideshow Media Component

The `slideshow.js` file is a JavaScript module that handles the functionality of a slider in the page title media section of a webpage. It uses the Splide.js library to create and control the slider.

## Functionality

This component provides the following functionality:

- **Slider Initialization**: Initializes a new Splide slider for each given element.

- **Autoplay Toggle Button**: Adds functionality to a button (with class `.splide__toggle`) to pause and resume autoplay. The button's class and `aria-label` attribute are updated to reflect the current state of autoplay. If autoplay is not enabled this is hidden.

- **Accessibility Improvements**: Sets the `aria-controls` attribute of the autoplay toggle button to the `id` of the slider's track, improving accessibility.

- **Drupal Integration**: Integrates the slider functionality with Drupal by adding it as a Drupal behavior. The `attach` method is called by Drupal to apply the behavior to elements on the page.

## Splide Data Object

The `data_splide` object is a configuration object for the SplideJS slider. It is set with the following properties:

- `arrows`: Controls the visibility of arrows.
- `autoHeight`: Adjusts the height automatically.
- `autoplay`: Enables autoplay.
- `drag`: Enables drag.
- `interval`: Sets the interval for autoplay.
- `pagination`: Controls the visibility of pagination.
- `perPage`: Sets the number of slides displayed per page.
- `perMove`: Sets the number of slides moved when an arrow is clicked.
- `rewind`: Enables rewind, this does not work in loop mode.
- `speed`: Sets the transition speed in milliseconds.
- `type`: Sets the type of the slider, these options include `slide`, `loop`, and `fade`.
- `mediaQuery`: Sets the order in which breakpoint specific options are applied. Defaults to 'max'. Set to 'min' for mobile first. 
- `breakpoints`: Sets the breakpoints for responsive design.

The `data_splide` object is then converted to a JSON string using the `json_encode` filter and passed to the `data-splide` attribute of the `section` element. This is how SplideJS receives its configuration.

Additional options can be enabled. Visit [SplideJS Options Guide](https://splidejs.com/guides/options) to see all possible options.

## Dependencies

This module depends on the following libraries:

- **Splide.js**: Used for creating and controlling the slider.
- **Drupal**: Used for integrating the slider functionality with a Drupal website.

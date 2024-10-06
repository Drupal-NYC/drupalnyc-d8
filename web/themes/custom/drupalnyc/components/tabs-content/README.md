# Component - Tabs Content

This is the primary element for a collection of tabs.

## Usage

To utilize this component it should be embedded within a twig template
and then within the embed assign the data to the tabs_items array.

The tabs_items_override expects the same data as if using the tabs_items
prop, but with the ability to override certain data.

Each tab item should have an ID, title and content -- all strings.

The `data-tabs-breakpoint` is required to determine when to switch the
tabs and accordion functionality. In order to always display tabs,
simply set this value to `0`;

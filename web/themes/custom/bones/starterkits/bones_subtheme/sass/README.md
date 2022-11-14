# Introduction to [Sass](http://sass-lang.com/)
Sass makes CSS fun again. Sass is an extension of CSS3, adding nested rules,
variables, mixins, selector inheritance, and more. It’s translated to well-
formatted, standard CSS using the command line tool or a web-framework plugin.

Please refer to the [Sass documentation](http://sass-lang.com/docs.html) for
further information about the syntax.

# Bones subtheme sass
The sass files from the main theme are copied here, but only the styles that
will be need to recompiled once variables are changed. Major changes can be
made to the subtheme simply by changing the variable values and recompiling the
sass. Commonly used mixins have also been brought over for bonus points.

# Color module integration
Some of these values will be overridden by color.css. To further customise
the colors, simply adjust the css rule to be more specific OR add !important to
the end of the rule.

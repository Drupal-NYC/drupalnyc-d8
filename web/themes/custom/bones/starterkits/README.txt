Bones Starterkit Instructions
-----------------------------

1. Install Bones from Drupal.org with composer -
$ composer require drupal/bones_theme

2. Create a new folder called “custom” in the themes folder

3. Move (cut rather than copy) the bones_subtheme folder from the bones_theme startkit folder to a new custom themes folder:

⸺ Web root
    ⌎⸺ themes
          ⌎⸺ contrib
                 ⌎⸺ bones_theme
				   ⌎⸺ starterkits
					    ⌎⸺ bones_subtheme (old location)
          ⌎⸺ custom
                 ⌎⸺ bones_subtheme (new location)

Congratulations, you’ve just made a subtheme!

4. Go to the website Appearance page and install the main Bones theme: /admin/appearance

5. Set the Bones Subtheme to 'install and set as default'

6. Flush the cache and visit the frontpage. You will know the subtheme is working properly because the logo will turn red.

7. You can now change the settings to enable extra features and recolor the theme quickly under the theme settings: /admin/appearance/settings/bones_subtheme


Extra: to rename the subtheme to something else, find & replace all instances of 'bones_subtheme' with your new name, as well as edit the info.yml details.


More information:
https://www.drupal.org/docs/8/theming-drupal-8/creating-a-drupal-8-sub-theme-or-sub-theme-of-sub-theme

Node packages are installed locally, so to run gulp, there is a script added to the package.json.
Run "yarn dev" (or "npm run dev") to run gulp.


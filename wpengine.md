# WPEngine's approach to wp-config.php
While [Pantheon](pantheon.md)'s approach to `wp-config.php` is less than ideal, WPEngine's approach is worse. 

WPEngine _"manages"_ your `wp-config.php` and tells you not to modify `wp-config` because their automated systems will periodically update it. When pushed they do acknowledge you can  make additions the top of the file &mdash; such as adding `define`()d constants required by plugins you may be using &mdash; but you cannot modify the remainder of the `wp-config.php` file because their automated update process may change it at some point in the future and thus break your site.

They also `define()` many of the constants you as a WordPress developer might want to have control of, and since PHP does not allow re-defining constants, you are pretty-much screwed if you want/need to change them _(well, typically you can ask support to change for you, but in some cases they won't change for you.)_

All-in-all, WPEngine's approach to `wp-config.php` is a real PITA. I am really hoping they will improve this in the _(near?)_ future.

But if you are on WPEngine, don't despair; there is a better way! 

Use [**Better WP-Config**](https://github.com/wplib/better-wp-config) and some WPEngine-specific workaround instead!

## Do you work for WPEngine?
If you are from WPEngine, please [reach out to discuss](mailto:team@wplib.org) how we can collaborate to make WPEngine much better for your customers by incorporating and Better WP-Config into your WordPress hosting service and provide us feedback so we can make Better WP-Config support your service even better that we already do.

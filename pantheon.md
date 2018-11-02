# [Pantheon](https://pantheon.io/)'s approach to wp-config.php

[Pantheon's default `wp-config.php`](https://github.com/pantheon-systems/WordPress/blob/default/wp-config.php) first looks for `wp-config-local.php`, confirms it is not running on Panthen and then loads `wp-config-local.php` but bypasses everything else, which means that for local development you'll need to duplicate and maintain all the default settings into your local config. 

Further, Pantheon does not provide a clean method to differentiate between their various environments your code will be running on, which can become especially challenging if you use branches. 

Yes, Pantheon provides configuration in `$_ENV` variables _(I wish all WordPress webhosts did this)_ but you still have to roll-your-own alternative to `wp-config.php`, which from experience we can definitively say is a rabbit-hole you probably don't want to waste your time on. 

And even if you do roll your own special `wp-config.php` it will almost certainly be incompatible with other web hosts that your future clients will almost certainly demand you use instead of Pantheon because, for example, they once read that [Gartner](https://www.gartner.com/) said a different managed WordPress host was the most secure _(3 years ago.)_ At least that has been our experience.

But if you are running your WordPress websites on Pantheon don't despair; there is a better way! Use [**Better WP-Config**](/wplib/better-wp-config) instead!

# WPEngine's approach to wp-config.php
While [Pantheon](pantheon.md)'s approach to `wp-config.php` is less than ideal, WPEngine's approach is worse. 

WPEngine _"manages"_ your `wp-config.php` and tells you not to modify `wp-config` because their automated systems will periodically update it. When pushed they do acknowledge you can  make additions the top of the file &mdash; such as adding `define`()d constants required by plugins you may be using &mdash; but you cannot modify the rest of the `wp-config.php` file because their automated update process may change it at some point in the future and thus break your site.

They also `define()` many of the constants you as a WordPress developer might want to have control of, and since PHP does not allow re-defining constants, you are pretty-much screwed if you want/need to change them _(well, typically you can ask support to change for you, but in some cases they won't change for you.)_  

To see what I mean, take a look at the following. This was taken from a live WPEngine site with the identifying information changed as well as the whitespace and few comments removed:

```
<?php
/*
 * Example of define()s from a WPEngine wp-config.php that as a customer you cannot modify.
 */

define( 'DB_NAME', 'wp_example' );
define( 'DB_USER', 'example' );
define( 'DB_PASSWORD', 'abcdefghijklmnopqrstuvwxyz' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_HOST_SLAVE', '127.0.0.1' );
define( 'DB_CHARSET', 'utf8');
define( 'DB_COLLATE', 'utf8_unicode_ci');

$table_prefix = 'wp_';

define( 'AUTH_KEY',         '|.0cJ+:^e|9XMzh}l13~ A]2|-z[V$E|dNM{pPxGOLeqL0Q~R?+I9CU)gVwxkl[7' );
define( 'SECURE_AUTH_KEY',  'jWpw|+;Y&YQ0tcw+GgT6-||ub-&vY+A!jR]4|*/C5 E*Fvi|e0so.ic;gLx3N+UD' );
define( 'LOGGED_IN_KEY',    'kV{3>}FPvk#)_EoK%|h//FB4OK/ui%7Extzl0/[adHB,ye))D<}uu<uj[r3H-!w}' );
define( 'NONCE_KEY',        'V.7R%V/xiIh<m>=~taZ%Q0*-jAIF?q,60GdtnR F4HcM<vb$r!a{ j1]:u-]R#-T' );
define( 'AUTH_SALT',        'g^E+;s&DbKAP5fjHfq>b{Se>RO*gGQhl`#%d%|g2zr:q-HTRT[$lq-JQLo&8%tqE' );
define( 'SECURE_AUTH_SALT', 'rx+&teS(eE_nc)U.)Q~k`I9C|V>g+UXl*UdpqIyFTrn*FVlmQ@cjKn=xt[EhJ$~$' );
define( 'LOGGED_IN_SALT',   'fu]M*;x,KKJ+%.0;lOsE:q94UEz8FO,ci7(XE]Y=BIG|!; v+Gn|W)WW7FKq^wrb' );
define( 'NONCE_SALT',       '*w2T|;@&k54,WI$P,bz&!1/9]+*?2;B@PAdv_Vx9+8T{WR!}M_+fa$=u+869R:UH' );
define( 'WP_CACHE', TRUE );
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'PWP_NAME', 'example' );
define( 'FS_METHOD', 'direct' );
define( 'FS_CHMOD_DIR', 0775 );
define( 'FS_CHMOD_FILE', 0664 );
define( 'PWP_ROOT_DIR', '/nas/wp' );
define( 'WPE_APIKEY', 'abcdefghijklmnopqrstuvwxyz1234567890' );
define( 'WPE_FOOTER_HTML', "" );
define( 'WPE_CLUSTER_ID', '123456' );
define( 'WPE_CLUSTER_TYPE', 'pod' );
define( 'WPE_ISP', true );
define( 'WPE_BPOD', false );
define( 'WPE_RO_FILESYSTEM', false );
define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );
define( 'WPE_SFTP_PORT', 2222 );
define( 'WPE_LBMASTER_IP', '' );
define( 'WPE_CDN_DISABLE_ALLOWED', false );
define( 'DISALLOW_FILE_MODS', FALSE );
define( 'DISALLOW_FILE_EDIT', FALSE );
define( 'DISABLE_WP_CRON', true );
define( 'WPE_FORCE_SSL_LOGIN', false );
define( 'FORCE_SSL_LOGIN', false );
define( 'WPE_EXTERNAL_URL', false );
define( 'WP_POST_REVISIONS', FALSE );
define( 'WPE_WHITELABEL', 'wpengine' );
define( 'WP_TURN_OFF_ADMIN_BAR', false );
define( 'WPE_BETA_TESTER', false );
define( 'WPLANG','');
define( 'WP_MEMORY_LIMIT', '3000M' );
```

All-in-all, WPEngine's approach to `wp-config.php` is a real PITA. I am really hoping they will improve this in the _(near?)_ future.

But if you are on WPEngine, don't despair; there is a better way! 

Use [**Better WP-Config**](https://github.com/wplib/better-wp-config) and some WPEngine-specific workaround instead!

## Do you work for WPEngine?
If you are from WPEngine, please [reach out to discuss](mailto:team@wplib.org) how we can collaborate to make WPEngine much better for your customers by incorporating and Better WP-Config into your WordPress hosting service and provide us feedback so we can make Better WP-Config support your service even better that we already do.

# Better WP-Config: Quick Start 

If you want to learn more about the _"**why**"_ of each of these steps, checkout our our [**7 Step Tutorial**](https://github.com/wplib/better-wp-config/blob/master/tutorial.md) instead.


## Step #1: Copy better-wp-config.php to your web root
Copy [`better-wp-config.php`](https://github.com/wplib/better-wp-config/blob/master/better-wp-config.php) to your web site's root directory, the same directory where your root `/index.php` is located.

## Step #2: Update your root /index.php file
Change these two (2) lines in WordPress' root `/index.php`:

```
/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
```

To look like this four (4) lines instead:

```
/* Loads the Better WP-Config bootstrapper */
require( __DIR__ . '/better-wp-config.php' );

/** Loads the WordPress Environment and Template */
require( wp_config()->dirs->core . '/wp-blog-header.php' );
```

## Step #3: Replace your wp-config.php file
Copy your `wp-config.php` to `wp-config.save.php`; you will need it in a minute.

Now replace your `/wp-config.php` with the following code, making sure that you locate this `wp-config.php` file is in your web root and *not* in the web root's parent directory _(you can [_**copy it directly**_](https://raw.githubusercontent.com/wplib/better-wp-config/master/wp-config.php) from the GitHub raw page if you like):_

```
<?php
/** 
 * Config by Better WP-Config
 * @see: https://github.com/wplib/better-wp-config 
 */
require_once( __DIR__ . '/better-wp-config.php' );
require_once( wp_config()->dirs->core . '/wp-settings.php' );
```

## Step #4: Create your wp-bootstrap.php file
Create a `/wp-bootstrap.php` to map your environment's domains to a name for each environment, and then map your environments to directory where you want your configuration files:

```
<?php
return array(
	'environments' => array(
		'(www\.)?example.com'   => 'live',
		'test.example.com'      => 'test',
		'stage.example.com'     => 'stage',
		'(www\.)?example.local' => 'local',
	),
	'configs' => array(
		'(live|test|dev)' => '/wp-content/config',
		'local'           => '/wp-content/config',
	),
);
```
 
## Step #5: Create your project's config.php file
Next, create a `/wp-content/config/config.php` file and include any configuration options that are project wide but that differ from Better WP-Config's defaults _(you can see the defaults in the next section after this example.)_ You may need to refer to your saved `wp-config.save.php` here:


```
<?php
return array(
	'disallow[file_edit]'         => true,
	'disallow[file_mods]'         => true,
	'debug[php]'                  => true,
	'debug[script]'               => true,
	'error[display]'              => '1',
	'error[display_startup]'      => '1',
	'db[name]'                    => 'example_db',
	'db[user]'                    => 'example_user',
	'db[pass]'                    => '1234567890abcdef',
	'db[table_prefix]'            => 'ex_',
	'salts[auth_key]'             => '&-BI@:CFy~}WNeWHvM#J{T%Gf#t$]#iLL,,ERkNoE!Kc]ieXD[{-qYMy2>mJlieD',
	'salts[secure_auth_key]'      => '@zO^-`:Yc{mVBpH1gt:%mMAM&*u-[.j?(jL<8r ];h~BWZnf||):Y?gyRQ]R+gI(',
	'salts[logged_in_key]'        => '}Rb,~^#Bn#EW,jkdIG[*+vMSdte&+#ewIPC;^eM{fakrqXafX|ewHn+q/8Bh8P-R',
	'salts[nonce_key]'            => 'T-FNz9xr8[41;n@KD $M*aIUBSN 8r4-[0>Ws2t`og!0YntKCyS!JwEfC?|ELb>_',
	'salts[auth_salt]'            => 'qJDhrvzmP;qa4O&<P{*ct-,F$|V!-]SHO1$V72W<_o$[cV/sR,5+-Q3<-PhX2r|2',
	'salts[secure_auth_salt]'     => 'J|H`ZOmiW3]vK-8?/NqDNsaVCV5K6QL|&4z10N=^<OlQzv,vg!|6gf)d}$HhB5{m',
	'limit[memory]'               => '256M',
	'limit[max_memory]'           => '256M',
);
```
### Better WP-Config's default options
Here are the default options for Better WP-Config:
```
public function defaults() {
	return array(
		'defines'                    => array(),
		'environment[scheme]'         => 'https',
		'environment[domain]'         => 'www.example.com',
		'environment[platform]'       => 'wordpress',
		'disallow[unfiltered_html]'   => false,
		'disallow[file_edit]'         => false,
		'disallow[file_mods]'         => false,
		'allow[subdirectory_install]' => false,
		'allow[unfiltered_uploads]'   => false,
		'allow[auto_update_core]'     => false,
		'allow[auto_update_plugin]'   => false,
		'allow[auto_update_theme]'    => false,
		'debug[php]'                  => false,
		'debug[script]'               => false,
		'error[reporting]'            => E_ALL,
		'error[display]'              => '0',
		'error[display_startup]'      => '0',
		'db[name]'                    => 'wordpress',
		'db[user]'                    => 'wordpress',
		'db[pass]'                    => 'wordpress',
		'db[host]'                    => 'localhost',
		'db[charset]'                 => 'utf8',
		'db[collate]'                 => '',
		'db[table_prefix]'            => 'wp_',
		'salts[auth_key]'             => 'insecure',
		'salts[secure_auth_key]'      => 'insecure',
		'salts[logged_in_key]'        => 'insecure',
		'salts[nonce_key]'            => 'insecure',
		'salts[auth_salt]'            => 'insecure',
		'salts[secure_auth_salt]'     => 'insecure',
		'limit[memory]'               => '64M',
		'limit[max_memory]'           => '64M',
		'dir[root]'                   => __DIR__,
	);
}
````

### Step #6: Create your environment's configuration files
For each environment you specified in your `wp-bootstrap.php` file you can a configuration file to override both  Better WP-Config's default as well those options set in your project's `/wp-content/config/config.php` file. 

Assuming you have four (4) environments &mdash; `local`, `test`, `stage` and `live` &mdash; here are examples of setting of each of your environments:

#### /wp-content/config/environments/local.php
```
<?php
return array(
	'environment[domain]'  => 'example.local',
	'environment[scheme]'  => 'http',
);
```

#### /wp-content/config/environments/test.php
```
<?php
return array(
	'environment[domain]'  => 'test.example.local',
	'environment[scheme]'  => 'http',
	'db[pass]'             => '0987654321fedcba',
);
```

#### /wp-content/config/environments/stage.php
```
<?php
return array(
	'environment[domain]'    => 'stage.example.local',
	'db[pass]'               => '0987654321fedcba',
	'debug[php]'             => false,
	'debug[script]'          => false,
	'error[display]'         => '0',
	'error[display_startup]' => '0',
);
```

#### /wp-content/config/environments/live.php
```
<?php
return array(
	'environment[domain]'    => 'www.example.com ',
	'db[pass]'               => '0987654321fedcba',
	'debug[php]'             => false,
	'debug[script]'          => false,
	'error[display]'         => '0',
	'error[display_startup]' => '0',
);
```

### Step #7: Use/create your web host provider's configuration files
You may want/need to create a configuration file for your web host and/or local development solution. 

The following is our provider configuration file for [**Pantheon**](https://pantheon.io) as an example.  To use it at Pantheon simply replace their `wp-config.php` file as explained in this tutorial and it should _"**just work**" (you can [_**copy it directly**_](https://raw.githubusercontent.com/wplib/better-wp-config/master/wp-content/config/providers/pantheon.php) from the GitHub raw page if you like):_

```
<?php
/**
 * This is configured for use with Pantheon.
 *
 * These settings were derived from Pantheon's WordPress upstream on GitHub:
 *
 *      https://github.com/pantheon-systems/WordPress/blob/default/wp-config.php
 *
 */
$is_https = isset( $_SERVER['HTTP_USER_AGENT_HTTPS'] )
       && 'ON' === $_SERVER['HTTP_USER_AGENT_HTTPS'];
if ( $is_https ) {
	$_SERVER['HTTPS'] = 'on';
}
return array(
	'db[host]'               => "{$_ENV[ 'DB_HOST' ]}:{$_ENV['DB_PORT']}",
	'db[name]'               => $_ENV[ 'DB_NAME' ],
	'db[user]'               => $_ENV[ 'DB_USER' ],
	'db[pass]'               => $_ENV[ 'DB_PASSWORD' ],
	'salt[auth_key]'         => $_ENV[ 'AUTH_KEY' ],
	'salt[secure_auth_key]'  => $_ENV[ 'SECURE_AUTH_KEY' ],
	'salt[logged_in_key]'    => $_ENV[ 'LOGGED_IN_KEY' ],
	'salt[nonce_key]'        => $_ENV[ 'NONCE_KEY' ],
	'salt[auth_salt]'        => $_ENV[ 'AUTH_SALT' ],
	'salt[secure_auth_salt]' => $_ENV[ 'SECURE_AUTH_SALT' ],
	'salt[logged_in_salt]'   => $_ENV[ 'LOGGED_IN_SALT' ],
	'salt[nonce_salt]'       => $_ENV[ 'NONCE_SALT' ],
	'error[reporting]'       => E_ALL ^ E_DEPRECATED,
	'environment[scheme]'    => $is_https ? 'https' : 'https',
	'disallow[file_mods]'    => in_array( $_ENV[ 'PANTHEON_ENVIRONMENT' ], array( 'test', 'live' ) ),
	'defines'                => array(
		'WP_TEMP_DIR' => "{$_SERVER[ 'HOME' ]}/tmp",
	)
);
```


As implied, provider configuration files are also useful for local development environments. Here is the one we ship for [**WPLib Box**](https://wplib.org/box/) _(you can [_**copy it directly**_](https://raw.githubusercontent.com/wplib/better-wp-config/master/wp-content/config/providers/wplib-box.php) from the GitHub raw page if you like):_

```
<?php
/**
 * This is configured for use with the WPLib Box local development solution for WordPress.
 *
 * @see https://github.com/wplib/wplib-box
 *
 */
return array(
	'db[name]'    => $_ENV[ 'DB_NAME' ],
	'db[user]'    => $_ENV[ 'DB_USER' ],
	'db[pass]'    => $_ENV[ 'DB_PASSWORD' ],
	'db[host]'    => $_ENV[ 'DB_HOST' ],
	'db[charset]' => $_ENV[ 'DB_CHARSET' ],
	'db[collate]' => $_ENV[ 'DB_COLLATE' ],
);
```




## That's it!  You are done!
But if you have any **need for support**, feel free to ask questions on our Slack _([_**join here**_](https://launchpass.com/wplib))_ and/or on Github by submitting [**an issue**](https://github.com/wplib/better-wp-config/issues/new), or even better, a [**pull request**](https://github.com/wplib/better-wp-config/compare).


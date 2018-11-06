# Better WP-Config: 7 Step Tutorial  

If you are a WordPress professional you should be able to use **Better WP-Config** for almost any WordPress website where you want more control and better workflow.

## Better WP-Config is a single file
Better WP-Config's source code comes in a single file: [`better-wp-config.php`](https://github.com/wplib/better-wp-config/blob/master/better-wp-config.php). That is the only source code file you will need to add, but you will need to modify the existing `/wp-config.php` and the root `/index.php` files.

### Step #1: Copy better-wp-config.php to your web root
Copy [`better-wp-config.php`](https://github.com/wplib/better-wp-config/blob/master/better-wp-config.php) to your web site's root directory, the same directory where your root `/index.php` is located.


## WordPress' root index.php file

One of the benefits of using Better WP-Config is you no longer need to modify your root `/index.php` if your local development configuration for WordPress core is different from your hosted configuration.  


### Step #2: Update your root /index.php file
Update your root `/index.php` to look exactly like the following _(you can [_**copy it directly**_](https://raw.githubusercontent.com/wplib/better-wp-config/master/index.php) from the GitHub raw page if you like):_

```
<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/**
 * Load Better WP Config configuration
 */
require( __DIR__ . '/better-wp-config.php' );

/** Loads the WordPress Environment and Template */
require( wp_config()->dirs->core . '/wp-blog-header.php' );
```

The above has only two (2) small changes from the `/index.php` that ships with WordPress core. Those changes are:

1. The `require()` statement to load for `better-wp-config.php`, and 
2. The use of `wp_config()->dirs->core` to locate `/wp-blog-header.php`.

By using `wp_config()->dirs->core` instead of `dirname( __FILE__ )` then you won't need to change `/index.php` in your deploy if your local development environment has WordPress core in a  directory _(for example: `/wp`)_ that differs from where your web host has WordPress core _(typically: `/`)_. 


## WordPress' wp-config.php file

When using Better WP-Config you can use one standard `wp-config.php`, and you won't ever have to change it.


### Step #3: Replace your /wp-config.php file
Copy your `wp-config.php` to `wp-config.save.php`; you will need it in a minute.

Now replace your `/wp-config.php` with the following code, making sure that you locate this `wp-config.php` file is in your web root and *not* in the web root's parent directory _(you can [_**copy it directly**_](https://raw.githubusercontent.com/wplib/better-wp-config/master/wp-config.php) from the GitHub raw page if you like):_

```
<?php
/* Config by Better WP-Config: https://github.com/wplib/better-wp-config */
require_once( __DIR__ . '/better-wp-config.php' );
require_once( wp_config()->dirs->core . '/wp-settings.php' );
```

Note that all configuration is now performed when `better-wp-config.php` is required, thus making the only other action needed in `wp-config.php` is to require `wp-settings.php`.

## Better WP-Config's wp-bootstrap.php file

Better WP-Config expects a `/wp-bootstrap.php` file **in the root of your website** &mdash; the same directory where your root `/index.php` is found &mdash; and it should contain at least the first two (2) of the following four (4) items:

1. **Environments**: An array of regular expressions to match and associate the domains you are using with a _"name"_ for each of your various web hosts, e.g. `local`/`test`/`stage`/`live`.

1. **Configs**: An array of regular expressions to match your environment _"names"_ from the environments and a relative path to the _directory_ that will contain your configuration file(s).

1. **File Format**: The file formats for your configuration files, one of: `php`, `env` or `json`. **Defaults to `php`**.

1. **Provider**: A name for your webhost or local development provider.  **_Normally_** you won't need to set this because Better WP-Config sets this for you based on inspecting the current environment although it is possible you may need to set it in rare cases. 

	Currently the supported webhosts are `pantheon` and `wpengine`, and `wplib-box` for local development, however we will be happy to accept pull requests that add support for others. 

### Step #4: Create your wp-bootstrap.php file
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
 
## Better WP-Config's project config.php file
Better WP-Config stores allow its configuration in a one-dimension associative array  _(except for the `defines` element which can contain an array of defines to set.)_ 

The following are the **default options** set in Better WP-Config _(expect we will add more as needed; pull requests appreciated!):_

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
If any of the above options are sufficient for your project, you don't need to set them. You only need to set the ones that differ.

### Step #5: Create your project's config.php file
Next, create a `/wp-content/config/config.php` file and include any configuration options that are project wide but differ from Better WP-Config's defaults. You may need to refer to your saved `wp-config.save.php` here: 

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

## Better WP-Config's environment-specific configuration files
For each of your environments you can create a environment-specific configuration filename will be the same as your environment and found in the `/wp-content/config/environments` directory, e.g. your `live` environment configuration will be found in  `/wp-content/config/environments/live.php`


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

### Web host provider configuration files
Lastly, you might want/need to use or create a configuration file for your web host, especially if they are a managed host and are heavy handled and controlling with their service's `wp-config.php` files _(\*cough\* [WPEngine](https://www.wpengine.com) \*cough\*)_.  

At the moment Better WP-Config includes a pre-written provider for web host [**Pantheon**](https://pantheon.io) and local development solution [**WPLib Box**](https://github.com/wplib/wplib-box), with immediate plans to add support for **WPEngine** _(if we can)_ and any other web hosts and local development solutions that want to work with us to add support.

### Step #7: Use/create your web host provider's configuration files
The following is our provider configuration file for Pantheon; simply replace their `wp-config.php` file as explained in this tutorial and it should _"**just work**" _(you can [_**copy it directly**_](https://raw.githubusercontent.com/wplib/better-wp-config/master/wp-content/config/providers/pantheon.php) from the GitHub raw page if you like):_

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


As implied, provider configuration files are also useful for local development environments. Here is the one we ship for WPLib Box _(you can [_**copy it directly**_](https://raw.githubusercontent.com/wplib/better-wp-config/master/wp-content/config/providers/wplib-box.php) from the GitHub raw page if you like):_

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


<?php

/**
 * @return WP_Bootstrap|WP_Config
 */
function wp_config() {
	static $config, $bootstrapped = false;
	if ( ! $bootstrapped ) {
		if ( ! isset( $config ) ) {
			$config = new WP_Bootstrap();
		}
		if ( function_exists( 'add_filter' ) ) {
			$config = new WP_Config( $config );
			WP_Config::release_memory();
			$bootstrapped = true;
		}
	}
	return $config;
}

/**
 * Interface WP_Configurable
 */
interface WP_Configurable {
	public function configure( $wp_config );
}

/**
 * Class WP_Config
 * @mixin WP_Bootstrap
 */
class WP_Config {

	/**
	 * @var array[]
	 */
	private static $_hooks = array();

	/**
	 * @var WP_Bootstrap|mixed
	 */
	private $_contained;

	/**
	 * @var WP_Config
	 */
	protected $_parent = null;

	/**
	 * @var string
	 */
	protected $_current_name;

	/**
	 * @param WP_Config|mixed $parent
	 */
	public function set_parent( $parent ) {
		$this->_parent = $parent;
	}

	/**
	 * WP_Config constructor.
	 *
	 * @param WP_Bootstrap|mixed $value
	 */
	public function __construct( $value ) {
		$this->_contained = $this->contain( $value );
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	function __get( $name ) {
		$this->_current_name = $name;
		$value = property_exists( $this->_contained, $name )
			? $this->_contained->$name
			: $this->_contained;
		$value = apply_filters( 'wp_config_option', $value, $this->_fullname(), $this );
		return $value;
	}

	/**
	 * Fullname includes parent names separated by colons
	 * @return string
	 */
	protected function _fullname() {
		do {
			$fullname = $this->_current_name;
			if ( is_null( $this->_parent ) ) {
				break;
			}
			$fullname = "{$this->_parent->_current_name}:{$fullname}";
		} while ( false );
		return $fullname;
	}

	/**
	 * @return mixed
	 */
	function __toString() {
		return is_scalar( $this->_contained )
			? $this->_contained
			: null;
	}

	/**
	 * Wraps any bootstrapping objects and their properties with config objects, but passes thru scalars.
	 *
	 * @param WP_Bootstrap|mixed $contained;
	 * @return WP_Config
	 */
	public function contain( $contained ) {
		if ( ! is_scalar( $contained ) ) {
			$properties = is_object( $contained )
				? get_object_vars( $contained )
				: $contained;
			foreach( $properties as $name => $value ) {
				is_object( $contained )
					? $contained->$name  = $this->maybe_make_contained( $value )
					: $contained[ $name ] = $this->maybe_make_contained( $value );
			}
		}
		return $contained;
	}

	/**
	 * Wraps any bootstrapping objects with config objects but passthru scalar data.
	 *
	 * @param mixed $value
	 *
	 * @return object
	 */
	public function maybe_make_contained( $value ) {
		if ( ! is_scalar( $value ) ) {
			/**
			 * @var WP_Config $value
			 */
			$value = new WP_Config_Wrapper( $value );
			$value->set_parent( $this );
		}
		return $value;
	}

	/**
	 * @param object $object
	 * @param mixed[] $properties
	 */
	static function set_properties( $object, $properties ) {

		do {

			$property_types = method_exists( $object, 'property_types' )
				? $object->property_types()
				: array();

			$parsed_properties = array();
			foreach( $properties as $property_name => $value ) {
				$property_name = rtrim( $property_name, ']' );
				if ( false !== strpos( $property_name, '[' ) ) {
					list( $parent_name, $sub_name ) = explode( '[', $property_name, 2 );
					$parsed_properties[ $parent_name ][ $sub_name ] = $value;
					unset( $properties[ $property_name ] );
					continue;
				}
				if ( ! property_exists( $object, $property_name ) ) {
					continue;
				}
				$object->$property_name = $value;
			}

			foreach ( $parsed_properties as $property_name => $values ) {
				if ( ! isset( $property_types[ $property_name ] ) ) {
					if ( ! isset( $property_types[ $plural_name = "{$property_name}s" ] ) ) {
						$object->$property_name = $values;
						continue;
					}
					$property_name = $plural_name;
				}
				$class_name = $property_types[ $property_name ];
				if ( ! isset( $object->$property_name ) ) {
					$child_object = new $class_name;
					if ( method_exists( $child_object, 'set_parent' ) ) {
						$child_object->set_parent( $object );
					}
					$object->$property_name = $child_object;
				}
				self::set_properties( $object->$property_name, $values );
			}

		} while ( false );

	}

	/**
	 * @param string $hook_type
	 * @param callable $callable
	 * @param array $args
	 * @param int $priority
	 */
	static function register_hook( $hook_type, $callable, $args = array(), $priority = 10 ) {
		if ( ! is_array( $args ) ) {
			$args = array( $args );
		}
		array_unshift( $args, $callable );
		$args[] = $priority;
		self::$_hooks[ $hook_type ][] = $args;
	}

	/**
	 */
	static function add_hooks() {
		foreach( self::$_hooks as $hook_type => $hooks ) {
			foreach ( $hooks as $args ) {
				call_user_func_array( "add_{$hook_type}", $args );
			}
		}
	}

	/**
	 */
	public static function release_memory() {
		self::$_hooks = null;
	}

	/**
	 * Prints the configuration in .env format
	 */
	public function print_env() {
		$this->_print( 'env' );
	}

	/**
	 * Prints the configuration in .php format
	 */
	public function print_php() {
		$this->_print( 'php' );
	}

	/**
	 * Prints the configuration in .json format
	 */
	public function print_json() {
		$this->_print( 'json' );
	}

	/**
	 * Prints the configuration based on the
	 * file_format found in wp-bootstrap.php.
	 */
	public function print_config() {
		$this->_print( $this->file_format );
	}

	/**
	 * @param string $file_format
	 * @param null|object $object
	 * @param null|string $parent_name
	 * @return array
	 */
	private function _print( $file_format, $object = null, $parent_name = null ) {

		if ( is_null( $object ) ) {
			$object = $this;
		}

		$contained = is_object( $object->_contained )
			? get_object_vars( $object->_contained )
			: $object->_contained;
		$options = array();
		foreach( $contained as $name => $value ) {
			$fullname = ! is_null( $parent_name )
				? "{$parent_name}[{$name}]"
				: $name;
			if ( is_scalar( $value ) ) {
				if ( is_bool( $value ) ) {
					$value = $value ? 'true' : 'false';
				} else if ( is_string( $value ) ) {
					$value = "'{$value}'";
				}
				$options[ $fullname ] = $value;
			} else {
				$options = array_merge( $options, $this->_print( $file_format, (object) $value, $fullname ) );
			}

		}
		if ( $this === $object ) {
			foreach( $options as $name => $option ) {
				switch( $file_format ) {
					case 'env':
						$options[ $name ] = "{$name}=" . str_replace( "'", '"', $option );
						break;
					case 'php':
						$options[ $name ] = "\t'{$name}' => {$option},";
						break;
					case 'json':
						$options[ $name ] = "\t'{$name}': {$option},";
						break;
				}
			}
			switch( $file_format ) {
				case 'env':
					echo implode( "\n", $options );
					break;
				case 'php':
					echo "return array(\n";
					echo implode( "\n", $options );
					echo "\n);";
					break;
				case 'json':
					echo "[\n";
					echo rtrim( implode( "\n", $options ), ',' );
					echo "\n]";
					break;
			}
		}
		return $options;
	}
}

class WP_Config_Wrapper extends WP_Config {}

/**
 * Class WP_Bootstrap
 */
class WP_Bootstrap {

	const PROJECT_CONFIG_PATH = '/wp-content/config/config.php';

	/**
	 * @var string
	 */
	public $provider = null;

	/**
	 * @var string
	 */
	public $file_format = 'php';

	/**
	 * @var WP_Environment_Bootstrap
	 */
	public $environment;

	/**
	 * @var WP_Paths_Bootstrap
	 */
	public $paths;

	/**
	 * @var WP_Dirs_Bootstrap
	 */
	public $dirs;

	/**
	 * @var WP_Allows_Bootstrap
	 */
	public $allows;

	/**
	 * @var WP_Disallows_Bootstrap
	 */
	public $disallows;

	/**
	 * @var WP_Debug_Bootstrap
	 */
	public $debug;

	/**
	 * @var WP_Error_Bootstrap
	 */
	public $error;

	/**
	 * @var WP_DB_Bootstrap
	 */
	public $db;

	/**
	 * @var WP_Salts_Bootstrap
	 */
	public $salts;

	/**
	 * @var WP_Limits_Bootstrap
	 */
	public $limits;

	/**
	 * @var array
	 */
	public $defines;

	/**
	 * @return array
	 */
	public function defaults() {
		return array(
			'defines'                    => array(),
			'environment[scheme]'         => 'https',
			'environment[domain]'         => 'www.example.com',
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

	/**
	 * @return string[]
	 */
	public function property_types() {
		return array(
			'environment' => 'WP_Environment_Bootstrap',
			'paths'       => 'WP_Paths_Bootstrap',
			'dirs'        => 'WP_Dirs_Bootstrap',
			'allows'      => 'WP_Allows_Bootstrap',
			'disallows'   => 'WP_Disallows_Bootstrap',
			'debug'       => 'WP_Debug_Bootstrap',
			'error'       => 'WP_Error_Bootstrap',
			'db'          => 'WP_DB_Bootstrap',
			'salts'       => 'WP_Salts_Bootstrap',
			'limits'      => 'WP_Limits_Bootstrap',
		);
	}

	/**
	 *
	 */
	public function load_config() {

		$this->paths         = new WP_Paths_Bootstrap();
		$this->paths->config = $this->_load_bootstrap();

		$this->dirs         = new WP_Dirs_Bootstrap();
		$this->dirs->root   = __DIR__;
		$this->dirs->config = __DIR__ . $this->paths->config;

		/**
		 * Load the default configuration for all projects using better-wp-config.php
		 */
		$config = $this->defaults();

		/**
		 * Load and merge configuration for this project/site
		 */
		$config = $this->_load_merge_config( 'project', $config );

		/**
		 * Load and merge configuration for the current environment
		 */
		$config = $this->_load_merge_config( 'environment', $config );

		/**
		 * Load and merge configuration for the local dev/web host provider
		 */
		$config = $this->_load_merge_config( 'provider', $config );

		/**
		 * Assign final merged configuration to this properties array
		 */
		WP_Config::set_properties( $this, $config );

		/**
		 * Compose the paths whose defaults can be derived from other paths
		 */
		$this->paths->admin    = "{$this->paths->core}/wp-admin";
		$this->paths->includes = "{$this->paths->core}/wp-includes";

		if ( is_null( $this->paths->private ) ) {
			/**
			 * To actually be private, this requires webhost support
			 * or you will need to configure yourself. But we hope to
			 * encourage a defacto-standard here. Help us by asking
			 * your host to standardize on this.
			 */
			$this->paths->private = "{$this->paths->content}/private";
		}

		/**
		 * Compose these dirs based on these paths
		 */
		$this->dirs->core     = "{$this->dirs->root}{$this->paths->core}";
		$this->dirs->content  = "{$this->dirs->root}{$this->paths->content}";
		$this->dirs->vendor   = "{$this->dirs->root}{$this->paths->vendor}";
		$this->dirs->admin    = "{$this->dirs->root}{$this->paths->admin}";
		$this->dirs->includes = "{$this->dirs->root}{$this->paths->includes}";
		$this->dirs->private  = "{$this->dirs->root}{$this->paths->private}";

		/**
		 * Run configure() for each contained object that implements WP_Configurable
		 */
		foreach ( get_object_vars( $this ) as $property => $object ) {
			if ( ! is_object( $object ) ) {
				continue;
			}
			if ( ! in_array( WP_Configurable::class, class_implements( $object ) ) ) {
				continue;
			}
			/**
			 * @var $object WP_Configurable
			 */
			$object->configure( $this );
		}


		/**
		 * Define any constants that were hardcoded in the config files
		 */
		foreach ( $this->defines as $constant => $value ) {
			if ( ! defined( $constant ) ) {
				continue;
			}
			define( $constant, $value );
		}

		/**
		 * Make sure 'dir[core]' was set correctly.
		 */
		if ( ! is_file( "{$this->dirs->core}/wp-blog-header.php" ) ) {
			trigger_error( sprintf( 'The core directory [%s] not correctly set.', $this->dirs->core ) );
		}

	}

	/**
	 * Load wp-bootstrap file
	 *
	 * @example Format of wp-bootstrap.php
	 *
	 *	<?php
	 *  return array(
	 *		'environments' => array(
	 *			'(www\.)?classicpress.net'          => 'live',
	 *			'live-classicpress.pantheonsite.io' => 'live',
	 *			'test-classicpress.pantheonsite.io' => 'test',
	 *			'dev-classicpress.pantheonsite.io'  => 'dev',
	 *			'(www\.)?classicpress.local'        => 'local',
	 *		),
	 *		'configs' => array(
	 *			'(live|test|dev)' => '/wp-content/uploads/private/config/config.php',
	 *			'local'           => '/content/config/config.php',
	 *		),
	 *	);
	 *
	 *
	 * @return string relative configuration file path
	 */
	private function _load_bootstrap() {

		$bootstrap = is_file( $bootstrap_file = __DIR__ . '/wp-bootstrap.php' )
			? (array) require( $bootstrap_file )
			: array();

		$bootstrap = (object) array_merge(
			array(
				'provider'     => null,
				'file_format'  => 'php',
				'environments' => array(),
				'configs'      => array()
			),
			$bootstrap
		);

		$this->provider = $this->_determine_provider( $bootstrap->provider );

		if ( preg_match( '#^(php|env|json)$#', $bootstrap->file_format, $match ) ) {
			$this->file_format = $bootstrap->file_format;
			do {
				if ( $autoloader_filepath = __DIR__ . '/vendor/autoload.php' ) {
					require_once( $autoloader_filepath );
				}

				if ( class_exists( '\\Dotenv\\Dotenv' ) ) {
					break;
				}

				trigger_error( '\\Dotenv\\Dotenv is required by wp-bootstrap.com but is not loaded.' );
				die(1);

			} while ( false );
		}

		if ( empty( $bootstrap->environments[ '.+' ] ) ) {
			$bootstrap->environments[ '.+' ] = 'default';
		}
		if ( empty( $bootstrap->configs[ 'default' ] ) ) {
			$bootstrap->configs[ 'default' ] = self::PROJECT_CONFIG_PATH;
		}

		$this->environment = new WP_Environment_Bootstrap();
		$this->environment->identify_environment( $bootstrap->environments );

		$config_path = self::PROJECT_CONFIG_PATH;
		foreach( $bootstrap->configs as $environment_regex => $config_path ) {
			if ( preg_match( "#^{$environment_regex}$#", $this->environment->name ) ) {
				break;
			}
		}

		return $config_path;
	}

	/**
	 * "Sniff out" what provider's platform is in use.
	 *
	 * Currently supports wplib.org/box, pantheon.io and wpengine.com.
	 *
	 * @param string $provider
	 *
	 * @return string
	 */
	private function _determine_provider( $provider ) {
		do {
			if ( isset( $_SERVER[ 'WPLIB_BOX' ] ) ) {
				/**
				 * Local development for WordPress
				 */
				$provider = 'wplib-box';
				break;
			}
			if ( isset( $_ENV[ 'PANTHEON_ENVIRONMENT' ] ) ) {
				/**
				 * Managed hosting for WordPress
				 */
				$provider = 'pantheon';
				break;
			}
			if ( isset( $_SERVER[ 'WPENGINE_ACCOUNT' ] ) ) {
				/**
				 * Managed hosting for WordPress
				 */
				$provider = 'wpengine';
				break;
			}
			/**
			 * More "expensive" tests below
			 */
			if ( is_dir( __DIR__ . '/_wpeprivate' ) ) {
				/**
				 * Managed hosting for WordPress
				 */
				$provider = 'wpengine';
				break;
			}
		} while ( false );
		return $provider;
	}

	/**
	 * Loads a configuration file and merged in passed in config for default values.
	 *
	 * @param string $config_type
	 * @param array $config
	 *
	 * @return mixed
	 */
	private function _load_merge_config( $config_type, $config ) {

		switch ( $config_type ) {
			case 'project':
				$config_filepath = "{$this->dirs->config}/config";
				break;

			case 'environment':
				$config_filepath = $this->environment->name
					? "{$this->dirs->config}/environments/{$this->environment->name}"
					: null;
				break;

			case 'provider':
				$config_filepath = $this->provider
					? "{$this->dirs->config}/providers/{$this->provider}"
					: null;
				break;

			default:
				$config_filepath = null;
		}
		do {

			$merged = $config;

			if ( is_null( $config_filepath ) ) {
				break;
			}
			$config_filepath = 'env' === $this->file_format
				? preg_replace( '#^(.+)/config$#', '$1', $config_filepath ) . '/.env'
				: $config_filepath . ".{$this->file_format}";
			if ( ! is_file( $config_filepath ) ) {
				break;
			}

			switch ( $this->file_format ) {
				case 'php':
					$loaded = (array) require( $config_filepath ) ;
					break;

				case 'env':
					$dotenv = new Dotenv\Dotenv(
						dirname( $config_filepath ),
						basename( $config_filepath )
					);
					$dotenv->safeLoad();
					$loaded = array();
					foreach( $dotenv->getEnvironmentVariableNames() as $name ) {
						$loaded[ $name ] = getenv( $name );
					}
					break;

				case 'json':
					if ( ! $json = file_get_contents( $config_filepath ) ) {
						break 2;
					}
					if ( ! $loaded = json_decode( $json ) ) {
						break 2;
					}
					break;
			}
			$merged              = array_merge( $config, (array) $loaded );
			$merged[ 'defines' ] = array_merge( $config[ 'defines' ], $merged[ 'defines' ] );

		} while ( false );

		return $merged;

	}

}


/**
 * Class WP_Environment_Bootstrap
 */
class WP_Environment_Bootstrap implements WP_Configurable {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $scheme;

	/**
	 * @var string
	 */
	public $header;

	/**
	 * @var string
	 */
	public $domain;

	/**
	 * @var string
	 */
	public $home;

	/**
	 * @var string
	 */
	public $site_url;

	/**
	 * Select this environment given array of hostname-matching regexes and associate names.
	 *
	 * @example /wp-bootstrap.php file contents:
	 *
	 * @param string[] $environments @example array(
	 *          '(www\.)?example.local' => 'local',
	 *          'test.example.com'      => 'test',
	 *          'stage.example.com'     => 'stage',
	 *          '(www\.)?example.com'   => 'live',
	 *      );
	 */
	public function identify_environment( $environments ) {
		$this->header = preg_replace( '#^(www\.)?(.+)$#', '$2', $_SERVER[ 'HTTP_HOST' ] );
		$this->name   = null;
		foreach ( $environments as $regex => $environment ) {
			if ( preg_match( "#^{$regex}$#", $this->header ) ) {
				$this->name = $environment;
				break;
			}
		}
	}

	/**
	 * @param WP_Bootstrap $wp_config
	 */
	public function configure( $wp_config ) {

		if ( ! isset( $this->domain ) ) {
			$this->domain = $this->header;
		}

		$this->home = "{$this->scheme}://{$this->domain}";
		$this->site_url = '/' !== $wp_config->paths->core
			? "{$this->home}{$wp_config->paths->core}"
			: $this->home;

		define( 'WP_HOME',    $this->home );
		define( 'WP_SITEURL', $this->site_url );

	}

}

/**
 * Class WP_Config_Paths
 */
class WP_Paths_Bootstrap {

	/**
	 * @var string
	 */
	public $core = '/';

	/**
	 * @var string
	 */
	public $content = '/wp-content';

	/**
	 * @var string
	 */
	public $config = '/wp-config.php';

	/**
	 * @var string
	 */
	public $vendor = '/vendor';

	/**
	 * @var string
	 */
	public $admin = '/wp-admin';

	/**
	 * @var string
	 */
	public $includes = '/wp-includes';

	/**
	 * @var string
	 */
	public $private;

}

/**
 * Class WP_Config_Dirs
 */
class WP_Dirs_Bootstrap extends WP_Paths_Bootstrap implements WP_Configurable {
	/**
	 * @var string
	 */
	public $root;

	/**
	 * @param WP_Bootstrap $wp_config
	 */
	public function configure( $wp_config ) {
		define( 'WP_CONTENT_DIR', $this->content );
		define( 'WP_CONTENT_URL', "{$wp_config->environment->home}{$wp_config->paths->content}" );
	}

}

/**
 * Class WP_Limits_Bootstrap  */
class WP_Limits_Bootstrap implements WP_Configurable {

	/**
	 * @var string
	 */
	public $memory = '64M';

	/**
	 * @var string
	 */
	public $max_memory = '64M';

	/**
	 * @param WP_Bootstrap $wp_config
	 */
	public function configure( $wp_config ) {
		define( 'WP_MEMORY_LIMIT',     $this->memory );
		define( 'WP_MAX_MEMORY_LIMIT', $this->max_memory );
	}

}

/**
 * Class WP_DB_Bootstrap  */
class WP_DB_Bootstrap implements WP_Configurable {
	/**
	 * @var string
	 */
	public $name = 'wordpress';
	/**
	 * @var string
	 */
	public $user = 'wordpress';
	/**
	 * @var string
	 */
	public $pass = 'wordpress';
	/**
	 * @var string
	 */
	public $host = 'localhost';
	/**
	 * @var string
	 */
	public $charset = 'utf8';
	/**
	 * @var string
	 */
	public $collate = '';
	/**
	 * @var string
	 */
	public $table_prefix = 'wp_';

	/**
	 * @param WP_Bootstrap $wp_config
	 */
	public function configure( $wp_config ) {
		define( 'DB_NAME',      $this->name );
		define( 'DB_USER',      $this->user );
		define( 'DB_PASSWORD',  $this->pass );
		define( 'DB_HOST',      $this->host );
		define( 'DB_CHARSET',   $this->charset );
		define( 'DB_COLLATE',   $this->collate );
		$GLOBALS[ 'table_prefix' ] = $this->table_prefix;
	}

}

/**
 * Class WP_Allows_Bootstrap  */
class WP_Allows_Bootstrap implements WP_Configurable {

	/**
	 * @var bool ALLOW_SUBDIRECTORY_INSTALL setting
	 */
	public $subdirectory_install =  false;

	/**
	 * @var bool ALLOW_UNFILTERED_UPLOADS setting
	 */
	public $unfiltered_uploads = false;

	/**
	 * @var bool WP_AUTO_UPDATE_CORE setting
	 */
	public $auto_update_core = false;

	/**
	 * @var bool add_filter( 'auto_update_plugin', '__return_true' );
	 */
	public $auto_update_plugin = false;

	/**
	 * @var bool add_filter( 'auto_update_theme', '__return_true' );
	 */
	public $auto_update_theme = false;

	/**
	 * @param WP_Bootstrap $wp_config
	 */
	public function configure( $wp_config ) {

		define( 'ALLOW_SUBDIRECTORY_INSTALL', $this->subdirectory_install );
		define( 'ALLOW_UNFILTERED_UPLOADS',   $this->unfiltered_uploads );
		define( 'WP_AUTO_UPDATE_CORE',        $this->auto_update_core );

		WP_Config::register_hook( 'filter', 'auto_update_plugin',
			$this->auto_update_plugin ? '__return_true' : '__return_false'
		);

		WP_Config::register_hook( 'filter', 'auto_update_theme',
			$this->auto_update_theme ? '__return_true' : '__return_false'
		);
	}

}

/**
 * Class WP_Disallows_Bootstrap  */
class WP_Disallows_Bootstrap implements WP_Configurable {

	/**
	 * @var bool
	 */
	public $unfiltered_html;

	/**
	 * @var bool
	 */
	public $file_mods;

	/**
	 * @var bool
	 */
	public $file_edit;

	/**
	 * @param WP_Bootstrap $wp_config
	 */
	public function configure( $wp_config ) {
		define( 'DISALLOW_UNFILTERED_HTML', $this->unfiltered_html );
		define( 'DISALLOW_FILE_MODS',       $this->file_mods );
		define( 'DISALLOW_FILE_EDIT',       $this->file_edit );
	}
}

/**
 * Class WP_Debug_Bootstrap  */
class WP_Debug_Bootstrap implements WP_Configurable {

	/**
	 * @var string WP_DEBUG setting
	 */
	public $php = false;

	/**
	 * @var string SCRIPT_DEBUG setting
	 */
	public $script = false;

	/**
	 * @param WP_Bootstrap $wp_config
	 */
	public function configure( $wp_config ) {
		define( 'WP_DEBUG',     $this->php );
		define( 'SCRIPT_DEBUG', $this->script );
	}
}

/**
 * Class WP_Error_Bootstrap  */
class WP_Error_Bootstrap implements WP_Configurable {

	/**
	 * @var string Setting error_reporting( $this->reporting );
	 */
	public $reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT;

	/**
	 * @var string
	 */
	public $display = '0';

	/**
	 * @var string
	 */
	public $display_startup = '0';

	/**
	 * @param WP_Bootstrap $wp_config
	 */
	public function configure( $wp_config ) {
		error_reporting( $this->reporting );
		ini_set('display_errors', $this->display );
		ini_set('display_startup_errors', $this->display_startup );
	}
}

/**
 * Class WP_Salts_Bootstrap  */
class WP_Salts_Bootstrap implements WP_Configurable {

	/**
	 * @var string
	 */
	public $auth_key;

	/**
	 * @var string
	 */
	public $secure_auth_key;

	/**
	 * @var string
	 */
	public $logged_in_key;

	/**
	 * @var string
	 */
	public $nonce_key;

	/**
	 * @var string
	 */
	public $auth_salt;

	/**
	 * @var string
	 */
	public $secure_auth_salt;

	/**
	 * @param WP_Bootstrap $wp_config
	 */
	public function configure( $wp_config ) {
		define( 'AUTH_KEY',         $this->auth_key );
		define( 'SECURE_AUTH_KEY',  $this->secure_auth_key );
		define( 'LOGGED_IN_KEY',    $this->logged_in_key );
		define( 'NONCE_KEY',        $this->nonce_key );
		define( 'AUTH_SALT',        $this->auth_salt );
		define( 'SECURE_AUTH_SALT', $this->secure_auth_salt );
	}

}

wp_config()->load_config();

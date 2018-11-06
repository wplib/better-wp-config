# Better WP-Config

Want to get started using Better WP-Config _**ASAP**_?
- Check out our [**Quick Start**](https://github.com/wplib/better-wp-config/blob/master/quick-start.md), or 
- Check our our more in-depth [**7 Step Tutorial**](https://github.com/wplib/better-wp-config/blob/master/tutorial.md).

## Features

- **No brittle PHP constants required**
  - Better WP-Config `define()`s all constants required by WordPress core for you
  - You configure with `.php` associate arrays, an `.env` file or a `JSON` file.
  - Config options are organized by area, e.g. `db`, `debug`, `error`, `salts`, etc.
  - Specifying options is consistent; no needed to know `define()` vs. `ini_set()`, etc.  
  - But you can specify `define()` constants needed by any plugin or theme
- **Directory-layout agnostic**
  - Store config files in **ANY** directory, parent or subdir
  - Use the `private` directories of managed web hosts to store config 
  - Set `ABSPATH` _(e.g. WP core)_ to web root, `/wp` or any other subdir
  - Set `WP_CONTENT_DIR` to `/wp-content`, `/app` or any other subdir
- **Configurations cascade (like CSS) for easy maintenance**
  - Better WP-Config has defaults for common WP and PHP config options
  - Use platform defaults to override Better WP-Config defaults 
  - Use project defaults to overide platform defaults, etc.
  - Use environment defaults that override platform defaults, etc.
  - Use secrets to override environment defaults, etc.
  - Finally web host configuration can override secrets, etc.
- **Provides first-class multi-environment support**
  - Define unlimited environments: `local`, `test`, `stage`, `prod`, etc.
  - Map named environments to `HTTP_HOST` domains by regular expression
  - Version-control config for all environments _(except `secrets`)_
  - Deploy with no build changes needed to `index.php` or `wp-config.php`
- **Optional handling of secrets**
  - Exclude from version-controlled config
  - Load from `$_ENV`, `$_SERVER` or `getenv()`
- **Optional support for:**
  - Custom configurations for managed web hosts _(If you are a web host, talk to us!)_ 
  - [phpdotenv](https://github.com/vlucas/phpdotenv) 
  - Custom config file location; outside web root, or in `private` directories
  - Trivial to implement multi-tenancy
- **Discoverability**
  - See all options and their values w/`wp_config()->print_config();`
  
## Show me the code!

_(Note: The following will not be true until we complete [this issue](https://github.com/wplib/better-wp-config/issues/7). Until then, see our [quick start](https://github.com/wplib/better-wp-config/blob/master/quick-start.md))_

Just create a file named `/wp-content/config/config.php` and add your site's configuration using the format in this example:

```
<?php
return array(
    'db[name]'         => 'example_db',
    'db[user]'         => 'example_user',
    'db[pass]'         => '1234567890abcdef',
    'db[charset]'      => 'utf8mb4_unicode_ci',
    'db[collate]'      => 'utf8mb4',
    'db[host]'         => 'localhost',
    'db[table_prefix]' => 'wp_',
);
```
You will also need to replace your site's  `/index.php` and `/wp-config.php` with *very* simple alternatives that you can find [here](https://github.com/wplib/better-wp-config/blob/master/index.php) and [here](https://github.com/wplib/better-wp-config/blob/master/wp-config.php), respectively.

### And that is all it takes! 

However, before you make **wrong assumptions:**

#### You can choose your own directory!

Better WP-Config can load config files from `../config`, `/private/config`, or wherever you like. 

#### You can use [phpdotenv](https://github.com/vlucas/phpdotenv) and .env files!

If you are a fan of separating configuration and code, you can set your `file_format` to `'env'`.

#### And it can handle (almost?) any level of complexity
It may look simple &mdash; which was intentional to make getting started easy &mdash; but Better WP-Config was architected to handle highly complex configuration requirements. Once you get started and you encounter more complex use-cases such as multiple environments and automated deployment you will see how flexible and powerful Better WP-Config truly is.
  

## But why?!?
You may ask what problem Better WP-Config is trying to solve. You may ask: _"Why do we need a better WordPress configuration solution?"_  Well, we need one because we have found that far too many use-cases beyond the trivial demand a better configuration solution. Read on:

### 1. No standarized support for professional workflow 
WordPress is a great CMS, but it ignores the needs of WordPress developers who would like to use a more professional workflow such as with `test`/`stage`/`live` environments. The default WordPress configuration was designed to manage configuration for one environment; if you want to manage more you have to roll-your-own multi-environment configuration solution.

### 2. Limited locations where you can store configuration
WordPress allows you to store configuration in `wp-config.php` in the web root, or one directory level above. Although you can store your configuration in another file and then `require()` it in `wp-config.php` _(which is what [**Better WP-Config**](https://github.com/wplib/better-wp-config) does)_ hacking together a custom solution means you'll also need to document and maintain it, assuming you want to depend on it in the future. 

And if you do go to the trouble to develop and document a solution such as Better WP-Config you'll basically have invested time _(and money?)_ into duplicating that which you cpould have just used without any development and documentation effort.

### 3. Managed host's configuration solutions are incompatible

Adding insult to injury each WordPress managed host &mdash; such as [Pantheon](https://pantheon.io/) and [WPEngine](https://wpengine.com/) &mdash; each roll their own arbitrarily-incompatible configuration solutions to support their own managed WordPress offering.

Here is how various WordPress managed webhosts handle and/or limit you with `wp-config.php`. 

- [Pantheon](pantheon.md)
- [WPEngine](wpengine.md)

If you are familiar with how other managed WordPress websites handle `wp-config.php` please consider submitting a pull request with documentation on how they handle their `wp-config.php` to help us and others.

### 4. PHP's inflexible immutable define() constants 
Anyone who has worked with WordPress knows about configuring WordPress' database credentials via the PHP constants `DB_HOST`, `DB_NAME`, `DB_USER` and `DB_PASSWORD`. This seems simple and easy when you firstt start working with WordPress, but over time you realize that it makes configuration _very inflexible_ because you **cannot cascade configurations** from WordPress' defaults, your project's defaults, to your environment's specifics and finally to your web host's configuration.

Better WPConfig does not eliminate the use of immutable constants but instead waits until all cascading configuration is merged before `define()`ing these constants.  _(But this could be a first step to eradicate the use of PHP's `define()`d constants from WordPress. It's a thought, because PHP's immutable constants makes automated testing of WordPress functionality much harder than it needs to be.)_

### 5. Hard to discover configuration options
Unfortunately there is no simple way to find all the options available to WordPress core since they are implied throughout WordPress' codebase and various documented locations onlines.  _Better WP-Config_ _(mostly)_ solves this problem with `"wp_config()->print_config()`, analogous to `phpinfo()`

### 6. Many configuration options have no default
A lot of configuration options have no defaults, such as a the database configuration which adds to learning required for local development.  _Better WP-Config_ provides default options for all _"known"_ options. 

### 7. Multiple environments config is difficult to version control 
By itself you could come up with a set of workable conventions for being able to version control your configuration for all the different environments your project needs &mdash; we did &mdash; but then you realize that each webhost handles it differently and try-as-you-might, you feel like it is impossible to find one consistent solution your team can use that will work with the different web hosts you and your clients have chosen.  

To better understand why this is difficult be sure to read about how [Pantheon](pantheon.md) and [WPEngine](wpengine.md)  handle their `wp-config.php` files, respectively.

### 8. Deployment is harder than it needs to be
Lastly, the problems related to professional workflow and version control uses-cases excerbated by the incompatible choices made by managed WordPress web hosts simply because of the lack of standarization of `wp-config.php` 
just makes deployment harder than it needs to be. This applies whether you are using SFTP upload, Git deployment such as Pantheon has, or deployment via a continuous integration provider like CircleCI.

### But there is a better way
The good news is Better WP-Config solves _(almost)_ all these problems, today!  [**Get started**](https://github.com/wplib/better-wp-config/blob/master/quick-start.md).

## Better WP-Config's Long Term Goals
We are really happy with how well **Better WP-Config**. However, things could be:

1. **Great** if the [ClassicPress](https://www.classicpress.net/) and [CalmPress](https://calmpress.org/) forks were to adopt Better WP-Config for configuration, 

1. **Even better** if WordPress **managed webhosts would standardize** on Better WP-Config for their services, or 

2. **Best** of all if **WordPress itself were to use Better WP-Config** for new installs, while still maintaining ongoing support for existing sites that are already using `wp-config.php` as it has been for years.

## License
GPLv2

# Better WP-Config

## Why do we need a better WordPress configuration solution?

### 1. No standarized support for professional workflow 
WordPress is a great CMS, but it ignores the needs of WordPress developers who would like to use a more professional workflow such as with `test`/`stage`/`live` environments. The default WordPress configuration was designed to manage configuration for one environment; if you want to manage more you have to roll-your-own multi-environment configuration solution.

### 2. Limited locations where you can store configuration
WordPress allows you to store configuration in `wp-config.php` in the web root, or one directory level above. Although you can store your configuration in another file and then `require()` it in `wp-config.php` _(which is what [**Better WP-Config**](#) does)_ hacking together a custom solution means you'll also need to document and maintain it, assuming you want to depend on it in the future. 

And if you do go to the trouble to develop and document a solution such as Better WP-Config you'll basically have invested time _(and money?)_ into duplicating that which you cpould have just used without any development and documentation effort.

### 3. Managed host's configuration solutions are poorly designed and incompatible

Adding insult to injury each WordPress managed host &mdash; such as [Pantheon](https://pantheon.io/) and [WPEngine](https://wpengine.com/) &mdash; each roll their own arbitrarily-incompatible configuration solutions to support their own managed WordPress offering.

Here is how various WordPress managed webhosts handle and/or limit you with `wp-config.php`. 

- [Pantheon](pantheon.md)
- [WPEngine](wpengine.md)

If you are familiar with how other managed WordPress websites handle `wp-config.php` please consider submitting a pull request with documentation on how they handle their `wp-config.php` to help us and others.

### 4. PHP's Inflexible Globally-scoped and Immutable Constants 
Anyone who has worked with WordPress knows about configuring WordPress' database credentials via the PHP constants `DB_HOST`, `DB_NAME`, `DB_USER` and `DB_PASSWORD`. This seems simple and easy when you firstt start working with WordPress, but over time you realize that it makes configuration _very inflexible_ because you **cannot cascade configurations** from WordPress' defaults, your project's defaults, to your environment's specifics and finally to your web host's configuration.

Better WPConfig does not eliminate the use of immutable constants but instead waits until all cascading configuration is merged before `define()`ing these constants.  _(But this could be a first step to eradicate the use of PHP's `define()`d constants from WordPress. It's a thought, because PHP's immutable constants makes automated testing of WordPress functionality much harder than it needs to be.)_

### 5. Discovery of configuration options
Unfortunately there is no simple way to find all the options available to WordPress core since they are implied throughout WordPress' codebase and various documented locations onlines.  _Better WP-Config_ _(mostly)_ solves this problem with `wp_config()->print()`, analogous to `phpinfo()`

### 6. Configuration options have no defaults
A lot of configuration options have no defaults, such as a the database configuration which adds to learning required for local development.  _Better WP-Config_ provides default options for all _"known"_ options. 

### 7. Difficult to version control configuration for multiple environments
By itself you could come up with a set of workable conventions for being able to version control your configuration for all the different environments your project needs &mdash; we did &mdash; but then you realize that each webhost handles it differently and try-as-you-might, you feel like it is impossible to find one consistent solution your team can use that will work with the different web hosts you and your clients have chosen.  

To better understand why this is difficult be sure to read about how [Pantheon](pantheon.md) and [WPEngine](wpengine.md)  handle their `wp-config.php` files, respectively.

### 8. Deployment is harder than it needs to be
Lastly, the problems related to professional workflow and version control uses-cases excerbated by the incompatible choices made by managed WordPress web hosts simply because of the lack of standarization of `wp-config.php` 
just makes deployment harder than it needs to be. This applies whether you are using SFTP upload, Git deployment such as Pantheon has, or deployment via a continuous integration provider like CircleCI.

### But there is a better way
But the good news is, **Better WP-Config solves _(almost)_ all these problems!**  Read the next section to learn more.

## Better WP-Config's Features

- Multi-environment config support
  - Map named environments to HTTP_HOST domains
  - Define unlimited environments _(Local/Test/Stage/Production/etc)_
  - Version-control config for all environments
- Cascading configurations
  - Better wp-config's defaults
  - Project defaults
  - Environment configuration
  - Webhost configuration
- Optional Secrets Handling
  - Exclude from version-controlled config
  - Load from `$_ENV`, `$_SERVER` or `getenv()`
- Optional support for:
  - Private/atlternate config location
  - Multi-tenancy
  - [phpdotenv](/vlucas/phpdotenv) 
  - Webhost-specfic configuration 
- Usability
  - See all options and their values w/`wp_config()-print();`
  
  
## Better WP-Config's Long Term Goals
We are really happy with how well **Better WP-Config**. However, things could be:

1. **Great** if the [ClassicPress](https://www.classicpress.net/) and [CalmPress](https://calmpress.org/) forks were to adopt Better WP-Config for configuration, 

1. **Even better** if WordPress **managed webhosts would standardize** on Better WP-Config for their services, or 

2. **Best** of all if **WordPress itself were to use Better WP-Config** for new installs, while still maintaining ongoing support for existing sites that are already using `wp-config.php` as it has been for years.

## License
GPLv2

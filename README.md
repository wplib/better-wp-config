# Better wp-config

## Why does the world need better wp-config?

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
  

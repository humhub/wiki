# HumHub - Module Coding Standards

Central repository for **code quality**, **Rector rules**, and **developer tooling** used across all **HumHub modules**.

## Installation

### Composer 

To create a initial Composer config:

```bash
composer init \
  --name="humhub/example-basic" \
  --type="humhub-module" \
  --no-interaction

composer config platform.php 8.2
```

After the `composer.json` file has been created:

```bash
composer config repositories.humhub-module-coding-standards vcs https://github.com/humhub/module-coding-standards.git
composer require --dev humhub/module-coding-standards:dev-main
composer config scripts.rector "vendor/bin/rector process --config=vendor/humhub/module-coding-standards/rector.php"
composer config scripts.fixer "vendor/bin/php-cs-fixer fix --config=vendor/humhub/module-coding-standards/php-cs-fixer.php"
```

Example of a minimal `composer.json`:

```yaml
{
  "name": "humhub/example-basic",
  "type": "humhub-module",
  "config": {
    "platform": {
      "php": "8.2"
    }
  },
  "repositories": {
    "humhub-module-coding-standards": {
      "type": "vcs",
      "url": "https://github.com/humhub/module-coding-standards.git"
    }
  },
  "require-dev": {
    "humhub/module-coding-standards": "dev-main"
  },
  "scripts": {
    "rector": "vendor/bin/rector process --config=vendor/humhub/module-coding-standards/rector.php",
    "fixer": "vendor/bin/php-cs-fixer fix --config=vendor/humhub/module-coding-standards/php-cs-fixer.php
  }
}
```

> Hint: Make sure to always commit the `composer.lock` as well.

### Install Workflows

The predefined workflows from the `workflows` folder should be copied to the `.github/workflows` folder in the module. 

| Workflow                | Description                                                                            |
|-------------------------|----------------------------------------------------------------------------------------|
| php-cs-fixer            | PHP CS Fixer runs automatically on commits to the `master` or `develop` branch.        |
| rector-auto-pr          | Automatic weekly Rector run with additional HumHub-specific adjustments. Creates a PR. |
| codeception-master      | Codeception tests against the HumHub core `master` branch                              |  
| codeception-develop     | Codeception tests against the HumHub core `develop` branch                             |  
| codeception-next        | Codeception tests against the HumHub core `next` branch                                |  
| codeception-min-version | Codeception tests against the HumHub minimum version defined in `module.json`          |  
| marketplace-upload      | On GitHub Module Releases, a module version is uploaded to the Marketplace.            |  

> Note: Some workflows require additional configuration. e.g. Codeception enable REST API
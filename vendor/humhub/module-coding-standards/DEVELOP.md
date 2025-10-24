# Working on this Repository

If you want to **modify or test new Rector rules, PHPStan configs, or CS-Fixer presets**,  
you can link this repository locally instead of installing it via GitHub.

In your module’s `composer.json`, add a **Composer path repository** entry:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../../module-coding-standards",
      "options": {
        "symlink": true
      }
    }
  ],
  "require-dev": {
    "humhub/module-coding-standards": "dev-main"
  }
}
```
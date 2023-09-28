Hosting Bundle
==============

A small bundle that provides basic tools for hosting a Symfony-based application.

> 📚 [**Read the docs**](https://21torr-docs.fly.dev/docs/php/symfony/hosting/)


Installation
------------

Create the env var in your `.env`:

```dotenv
HOSTING_TIER=development
```

Then create the config in `config/packages/hosting.yaml`:

```yaml
hosting:
    # add a unique identifier of your project installation here
    installation: 'ohai'
    tier: '%env(HOSTING_TIER)%'
```

Then install the package:

```bash
composer require 21torr/hosting
```

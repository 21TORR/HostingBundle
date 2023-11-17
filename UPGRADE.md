2.x to 3.0
==========

* The `installation_key` config was removed. There is no replacement, you need to implement something similar in your project directly.
* The `live` hosting tier was removed and replaced with `production`.
* The command `hosting:post-build` was renamed to `hosting:run-tasks:post-build`.
* The command `hosting:post-deploy` was renamed to `hosting:run-tasks:post-deploy`

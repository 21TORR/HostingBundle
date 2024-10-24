3.x to 4.0
==========

* Command `hosting:run-tasks:post-build` was removed, use `hosting:hook:build` instead.
* Command `hosting:run-tasks:post-deploy` was removed, use `hosting:hook:deploy` instead.
* Interface `PostBuildTaskInterface` was removed, use `BuildHookInterface` instead.
* Interface `PostDeploymentTaskInterface` was removed, use `DeployHookInterface` instead.


2.x to 3.0
==========

* The `installation_key` config was removed. There is no replacement, you need to implement something similar in your project directly.
* The `live` hosting tier was removed and replaced with `production`.
* The command `hosting:post-build` was renamed to `hosting:run-tasks:post-build`.
* The command `hosting:post-deploy` was renamed to `hosting:run-tasks:post-deploy`

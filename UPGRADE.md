4.x to 5.0
==========

* Interface `DeployHookInterface` was removed, use `DeployContainerHookInterface` instead.


4.2 to 4.3
==========

* All commands are invokable classes now and no longer extend `Command`. Command names and aliases are unchanged, but `instanceof` checks against the command classes and calls to the inherited `Command` API no longer work.
* `ValidateAppCommand` is `final` now.


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

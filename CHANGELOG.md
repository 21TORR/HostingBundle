4.0.3
=====

* (bug) Fix wrong log context.


4.0.2
=====

* (improvement) Log doctrine health check issues.


4.0.1
=====

* (bug) Keep using the `hosting` config key.


4.0.0
=====

* (bc) The config key was changed to `21torr_hosting`.
* (bc) Remove command `hosting:run-tasks:post-build`, use `hosting:hook:build` instead.
* (bc) Remove command `hosting:run-tasks:post-deploy`, use `hosting:hook:deploy` instead.
* (bc) Remove `PostBuildTaskInterface`, use `BuildHookInterface` instead.
* (bc) Remove `PostDeploymentTaskInterface`, use `DeployHookInterface` instead.


3.2.2
=====

* (bug) Fix typo in BC layer of `hosting:hook:deploy` command.


3.2.1
=====

* (improvement) Change configuration of hosting tier to allow better using it.
* (improvement) Automatically catch all exceptions in the validate app command.


3.2.0
=====

* (feature) Add health checks (live + ready).
* (improvement) Bump Janus and update CI.
* (feature) Add default doctrine check integration.
* (feature) Add `hosting:validate-app` command for usage in the CI.


3.1.0
=====

* (feature) Rename `Post*` tasks concept name to "hooks".
* (deprecation) Deprecate command `hosting:run-tasks:post-build`, use `hosting:hook:build` instead.
* (deprecation) Deprecate command `hosting:run-tasks:post-deploy`, use `hosting:hook:deploy` instead.
* (deprecation) Deprecate `PostBuildTaskInterface`, use `BuildHookInterface` instead.
* (deprecation) Deprecate `PostDeploymentTaskInterface`, use `DeployHookInterface` instead.


3.0.1
=====

* (internal) Remove missed deprecated code.
* (bug) Fix break when not passing an installation key.
* (internal) Add branch alias.
* (internal) Fixate PHPUnit version.


3.0.0
=====

* (bc) Remove deprecated config and code.
* (improvement) Require Symfony 7.


2.1.3
=====

* (improvement) Re-add required installation key.
* (improvement) Also replace PHP 8.2 and PHP 8.3 polyfills.
* (internal) Update CI.


2.1.2
=====

* (improvement) Add build date as `built` in build info by default.
* (improvement) Allow Symfony v7.
* (improvement) Require PHP 8.3+.


2.1.1
=====

* (improvement) Sort build info entries by key when writing and reading the JSON file.


2.1.0
=====

* (deprecation) The `installation_key` config is deprecated.
* (improvement) Bump required versions to PHP 8.2+ and Symfony 6.3+ 
* (improvement) Rename `live` hosting tier to `production`. 
* (deprecation) Deprecated `live` hosting tier. 
* (improvement) The command `hosting:post-build` was renamed to `hosting:run-tasks:post-build`.
* (deprecation) The command `hosting:post-build` was deprecated.
* (improvement) The command `hosting:post-deploy` was renamed to `hosting:run-tasks:post-deploy`.
* (deprecation) The command `hosting:post-deploy` was  deprecated.
* (feature) Rebuild build info system to be event-based.


2.0.6
=====

* (improvement) Also replace `symfony/polyfill-mbstring`.


2.0.5
=====

* (improvement) Fix deprecation in console commands.


2.0.4
=====

* (improvement) Add way to retrieve the installation key.


2.0.3
=====

* (improvement) Add missing getter.


2.0.2
=====

* (bug) Remove unused binding.


2.0.1
=====

* (bug) Update service definition.


2.0.0
=====

* (bc) Git data was removed.
* (feature) Add `BuildInfo`.


1.1.4
=====

* (improvement) Replace all symfony polyfills and require the native packages in Composer instead.
* (improvement) Require PHP 8.1+ and Symfony 6.1+


1.1.3
=====

*   (bug) Fix refreshing the Git data.


1.1.2
=====

*   (bug) Fixed invalid check, that prevented loading stored git version info if the tag was `null`.


1.1.1
=====

*   (bug) Fix copy & paste error in CLI output.
*   (improvement) Improve output.


1.1.0
=====

*   (feature) Add task runners for post deploy + post build.
*   (feature) Add `GitVersion` integration.


1.0.0
=====

*   (feature) Add `hosting_tier` config and `HostingTier` class.
*   (feature) Add `installation` config key.

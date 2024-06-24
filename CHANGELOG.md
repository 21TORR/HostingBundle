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

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

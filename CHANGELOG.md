# Library changelog

Only major versions or things to communicate are written here. Minor changes or bug fixes might not appear in the changelog.

## 3.1.0

Introduces `cb_status` as the preferred Crawlbase status field on response headers (and Storage bulk items).  
`pc_status` is deprecated but still supported as a temporary alias of the resolved status value. When both `cb_status` and `pc_status` are present, `cb_status` takes priority. See the README migration note for upgrading from `pc_status` to `cb_status`.

## 3.0.0

Adds Screenshots API and Storage API.  
We have refactored the base class to allow for future development, there shouldn't be breaking changes but we have decided to release a new major version so you are aware and report if something breaks for your case.

## 2.0.0

Version 2 deprecates the usage of CrawlbaseAPI (although is still usable but will be removed in future versions) in favour of Crawlbase\CrawlingAPI. Please test the upgrade before deploying to production.

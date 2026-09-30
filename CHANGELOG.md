# Changelog

All notable changes to this project will be documented in this file.

## Unreleased

- restore instance permalinks from the 0.27 series, including support for existing links
- restore shared-file link export with reliable selection and copyable output

## 0.31.0

### Downloads
- sortable columns, checkbox-only selection, select-all checkbox, removed redundant links below the table
- filtering for the downloads table
- trash icon replaced with a clearer cleanup icon
- direct speed settings (KB/s ↔ MB/s toggle)

### Uploads
- aligned with the improved Downloads list for a consistent UX
- direct speed settings

### Dashboard
- card order adjusted so credits stay visible at the top on mobile
- server time formatted as DD.MM.YYYY
- download/upload speeds shown with `/s` suffix
- new Public IP row, sourced from the core's `NETWORKINFO/IP` (matching the Java GUI)

### Search
- sortable columns (name, size, format, sources)
- multi-select with multi-download
- filename filter and file-format filter

### Layout & navigation
- wider GUI container on widescreen, footer pinned to the bottom
- current credits shown in the top menu bar
- top header "Add links" action: magnifier replaced with "+" icon, modal renamed to "Links hinzufügen"

### Settings
- KB/s and MB/s toggles for speed values

### Core / infrastructure
- AJAX-based live page updates (no full reload)
- fixed part-mapping regex and added support for 7z parts
- build/setup fixes for local builds, typo cleanup, version bumped to 0.31.0


## 0.30.0

- register `web+ajfsp` protocol handler if gui is running in https
- ajl import fix
- upload page fix if upload slots zero
- fix server error 500 on exit core command

## 0.29.8

- backward compatibility fixes for browser extension

## 0.29.7

- fix Bugs in Dashboard
- delet at first progressbar for downloads

## 0.29.6

- Style fixed

## 0.29.5

- add languagepack english
- fix bugs on all pages
- add serverconnection page
- add mobile Navbar on bottom of screen

## 0.29.4

- fix dashboard
- fix search
- fix download ->buttons now with function
- features remove:
    - style switcher
- fix share

## 0.29.3

- Bug fix
  -simple Theme

## 0.29.2

- code structure improvements (eg. PSR4)

## 0.29.1

- fix login
- fix style switcher

## 0.29.0

- new modern Style
- Community Version
- features add:
    - style switcher
    - version checker
    - fix Search
    - fix Uploads
    - fix Downloads
    - fix Dashboard
    - fix Sharefiles

## 0.28.1

- fix style switcher

## 0.28.0

- php 8 adaptations
- fix memory overflow on large shares

## 0.27.10

- revert back to PHP 7 in docker image

## 0.27.9

- set php `memory_limit` to `-1` in docker container
- `phpinfo` plugin
- use PHP 8 as base docker image
- install php `opcache` extension for better performance

## 0.27.8

- alphabetical ordering in files view

## 0.27.7

- restore gd stuff for partlist

## 0.27.6

- correct rel info url

## 0.27.5

- make `NEWS_URL` configurable
- make `SERVERLIST_URL` configurable
- get GUI NEWS from Github

## 0.27.4

- set default `error_reporting` to `0` (can be changed with `PHP_INI_ERROR_REPORTING`)
- set default `display_errors` to `Off` (can be changed with `PHP_INI_DISPLAY_ERRORS`)

## 0.27.3

- allow tab selection in perma link

## 0.27.2

- fix permalink usage

## 0.27.1

- fix relinfo link builder

## 0.27.0

- add back a simplified RelInfo Icon in `downloads`, `uploads`, `share` and `search` view
- add `permalink` on top Bar
- load http files with `file_get_contents` instead of `fsockopen`
- refactor code and style of `/index.php` for better readability
- removed `minigui`

## 0.26.0

- document `ENV` variables for docker
- remove phpaj `savebw` option
- remove phpaj downloads `autoclean` option
- remove now useless phpaj options from settings view
- allow progressbar configuration from ENV
- allow auto login in `top` frame

## 0.25.5

- add ability to handle multiple links

## 0.25.4

- fix link exporter (AJL and BB-Code)

## 0.25.3

- switch from `fsockopen` to `curl` for all core requests (faster)

## 0.25.2

- remove PopUps on link export

## 0.25.1

- fix minigui for PHP 7.X

## 0.25.0

- import project into VCS
- make Code PHP 7.X compatible
- add Docker support
- remove outdated `appledocs` implementation

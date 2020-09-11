# Changelog
All notable changes to this project will be documented in this file.

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

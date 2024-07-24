# Directory structure

To understand the framework, here is what you need to know about its folders



## Root directory structure

```
/
├── bin
├── lib
├── packages
├── public
│   ├── .htaccess
│   └── index.php
├── spool
├── eq.lib.php
└── run.php
```



| **FILE** | **DESCRIPTION** |
|-|-|
| `eq.lib.php`	| Boostrap library. Defines constants and global utilities |
| `run.php`	| Server script for client-server mode|
| `lib`	      | Folder containing eQual library  (mostly classes and services definitions) |
| `packages` | Folder containing installed packages (classes definition, translations, views, actions, …)|
| `public` | Root public folder for the web server |
| `bin` | Folder containing values of binary fields see BINARY_STORAGE_DIR |
| `spool` | Dedicated to email sending |



The root folder is also the place where to place a `composer.json` file and the subsequent `/vendor` directory.

## public

| **FILE**      | **DESCRIPTION**                                              |
| ------------- | ------------------------------------------------------------ |
| `.htaccess`   | Apache configuration file  used to prevent directory listing and handling url rewriting |
| `index.php`   | This script is also referred to as the dispatcher : its task is to include required libraries and to set the context. This is the main entry point. |
| `console.php` | This is the only alternate entry point.                      |
| `assets`      | static content, javascripts, stylesheets, images, config, translation |
| `assets/env`  | eQual Configuration file.                                    |
| `assets/i18n` | Translation files.                                           |



In addition to those files and folders, other folders might be present, according to the initialized Apps.



## config

* **`config.json`**, to configure your database and other parameters.

* **`config-example.json`**, rename it as `config.inc.php` to use custom configuration.

## lib

Libraries and services used as external resources.

## run.php

The `run.php` scripts acts as a router to handle custom routes and native DO, GET and SHOW queries, either by CLI, HTTP or PHP.




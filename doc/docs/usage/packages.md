# Packages

In order to ease development and collaboration, eQual organizes application logic into themed packages represented by
folders, located under the `/packages` directory.

These packages contain several components : models, views, controllers, and translation files. Models define data structure,
views describe user interface, controllers manage data flow, and translations support i18n features.

## Package structure

Each **package** is defined as follows :

```
package_name
├── classes
│   └── */*.class.php
├── actions
│   └── */*.php
├── apps
│   └── */*.php
├── data
│   └── */*.php
├── tests
│   └── */*.php
├── init
│   └── *.json
├── views
│   ├── 
│   └── *.json
├── i18n
│   └── *
│       └── *.json
├── manifest.json         
├── config.json
└── readme.md
```

| **FOLDER** | **ROLE**                                        | **EXAMPLES**                                                 |
| :--------: | ----------------------------------------------- | ------------------------------------------------------------ |
| `classes`  | model                                           | `core\User.class.php`, `core\Group.class.php`, `core\Permission.class.php` |
| `actions`  | action handler (controller)                     | `core_manage`, `core_utils`                                  |
|   `apps`   | applications related to the package             | `auth`, `apps`                                               |
|   `data`   | data provider                                   | `core_model_read`, `core_config_packages`                    |
|  `tests`   | test units                                      | `default.php`                                                |
|   `init`   | initialize the package (entities, data, routes) | `core_Group.json`                                            |
|  `views`   | templates                                       | `User.form.default.json`, `User.list.default.json`           |
|   `i18n`   | translations                                    | `User.json`                                                  |
|  `assets`  | static content (resources)                      | js scripts, stylesheets, fonts, images                       |

## Self description

### manifest.json

Packages manifests are an essential tool for managing the packages ecosystem and ensure compatibility between different components and versions of software libraries, facilitating seamless integration and streamlined development processes.

Each package has a manifest file (`manifest.json`) containing information about the package along with the apps it provides and the dependencies it requires. 

| **PROPERTY**  | **ROLE**                                                     | **EXAMPLES**                                                 |
| :-----------: | ------------------------------------------------------------ | ------------------------------------------------------------ |
|    `name`     | `(string)` Name of the package, also used as an identifier.<br />It should match the name of the folder of the package. | `"core"`                                                     |
|   `version`   | `(string)` The version of the package, ensuring accurate versionning. | `"2.0"`                                                      |
| `description` | `(string)` Short description of the package.                 | `"Foundations package holding the application logic of the elementary entities."` |
|   `license`   | `(string)` The license (and version) of the package.         | `"LGPL-3"`                                                   |
|   `authors`   | `(array <string>)` List of the name(s) of the author(s).     | `["Cedric Francoys"]`                                        |
|    `tags`     | `(array <string>)` List of descriptive tags or keywords associated with the package for easier categorization and searchability. | `["Cedric Francoys"]`                                        |
| `depends_on`  | `(array <string>)` List of packages that need to be present and initialized in order for the package to work. | `[]`                                                         |
|  `requires`   | `(object)` Map specifying external libraries or packages required by the package, along with version constraints using semantic versionning. | `"requires": {"swiftmailer/swiftmailer": "^6.2"}`            |
|    `apps`     | `(array <string | object>)` Applications embedded in the package. <br />The list provides either Apps names (identifier) or descriptors for virtual apps to be used with the STD App (see below). | `["apps", "auth", "app", "settings"]`                        |

**Example:**

```json
{
    "name": "core",
    "description": "Foundation package holding common application logic and elementary entities.",
    "version": "2.0",
    "authors": ["Author Names"],
    "license": "License Type",
    "tags": [ "equal", "core" ],
    "depends_on": [],
    "requires": {
        "swiftmailer/swiftmailer": "^6.2",
        "phpoffice/phpspreadsheet": "^1.4",
        "dompdf/dompdf": "^0.8.3"
    },
    "apps": [ "apps", "auth", "app", "settings", "workbench", "welcome" ]
}
```

#### `apps` property
The `apps`  property might either contain strings or descriptor objects.  The `app` application, defined in the core package,  can indeed act as surrogate for creating custom Apps using all standard features without having to write additional Angular components. In such case, the descriptor is expected to follow the structure defined below.

**Example:**

```json
{
            "id": "inventory",
            "name": "Inventory",
            "extends": "app",
            "description": "Application inventory",
            "icon": "analytics",
            "color": "#6C3483",
            "access": {
                "groups": [
                    "users"
                ]
            },
            "params": {
                "menus": {
                    "left": "inventory.left",
                    "top": "inventory.top"
                }
            }
        }
```



### README.md

A markdown file containing various relevant information about the package, its installation, configuration, and the features it offers.

## Apps

### manifest.json

| **PROPERTY**       | **ROLE**                                                     | **EXAMPLES**                                        |
| ------------------ | ------------------------------------------------------------ | --------------------------------------------------- |
| name               | The name of the application.                                 | `Settings`                                          |
| description        | A short description of the role and usage of the application. |                                                     |
| url                | Relative URL of the application.                             | `/settings`                                         |
| icon (optional)    | Identifier of the (material) icon for representing the application. | `settings`                                          |
| color (optional)   | Color assigned to the application (used for the button).     | `#FF9741`                                           |
| extends (optional) | String telling which application it extends from, if any.    | `app`                                               |
| access/groups      | List of groups the access to the application is restricted to. | `['users']`                                         |
| params             | Object to be relayed to the targeted App targeted with `extends`(if supported). | `"params": { "menus" : { "left": "myApp.left" }  }` |
| show_in_apps       | Flag telling if the application must be listed in the dashboard Apps list. | default: ``false``                                  |
| tags (optional)    | List of labels for tagging of the application.               | `["equal", "core"]`                                 |


#### params
The `params` property allows to provide the surrogate application (`app`)  with parameters specific to the application being described. It can be used to define which menus must be loaded and which context must be displayed by default.

Example:
````
"params": {
    "menus": {
        "left": "settings.left",
        "top": "settings.top"
    },
    "context": {
        "entity": "core\\User"
        "view": "list.default",
        "order": "id",
        "sort": "asc"
    }
}
````

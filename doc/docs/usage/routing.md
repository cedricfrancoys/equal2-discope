# Router 

The Router `equal/route/Router.class.php` class handles routes located inside the `config/routing` folder.

The routing mechanism is meant to route a request to the targeted controller.

Structure of a route  :	`URI -> operation`

Reserved parameters :

- get

- do

- show

- route

- announce

  

## Creating Routes

Inside the `config/routing` directory, you have the possibility to build your own routes. 

The expected route structure :

| **STRUCTURE** | **TYPE**                                                     | **DESCRIPTION** |
| --------------- | -----------------------------------------|------------------ |
| method | string         | HTTP method, example: "GET", "POST", "PUT", "DELETE", "PATCH". |
| path | string | Full path of the requested URI. |
| parts | array |Array of parts composing the path.|
| operation | string |The operation the URI resolves to.|
| params | array |Map of parameters names as keys, related to their values.|



There are a few default routes built in eQual, to get the users and groups.

**Example**  : 

```JSON
"/user/:ids": {
        "GET": {
            "description": "Retrieve fields values related to a given user",
            "operation": "?get=core_model_read&entity=core\\User"
        },
        "PUT": {
            "description": "Update a user",
            "operation": "?do=core_user_update"
        },
        "DELETE": {
            "description": "Delete a user",
            "operation": "?do=core_model_delete&entity=core\\User"
        }
    },
```

You need the operation and description properties to build your routes. 

The **operation** will be about the "Query" part of the URI/URL. 

And the description is simply a field to indicate what the operation does.



##### routes hierarchy

Routes are stored in files located in th the `config/routing` folder. 

The files holding the routes configuration are read in alphabetical order. Which means, that if two distinct files contain a same route (targeting different controllers), it is the route that is contained in the first file, in alphabetical order, that will be applied.

Typically, when a package needs to override an existing route, it uses files with a prefix having a higher priority.

Example: 

```
/routing
  10-lodging.json
  80-documents.json
  80-identity.json
  99-default.json
```



##### i18n

Inside the `config/routing` folder, you can have sub-folders for different languages, with the possibility to translate the routes in the chosen language.

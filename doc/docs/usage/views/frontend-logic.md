# Front-end logic

Views are intended to describe how to present the objects to end-users under a given context. They are used as templates for the front-end, and are stored as JSON files within the `views` folder of their respective package.

When an object or a collection of objects must be displayed in the UI, along with the data, the UI renderer also requests a view and its translation.

Each view represents a mode of visualization: form, list, chart, dashboard, etc; and can be edited independently from the models they relate to.

It is possible to define as many views (of different types, or variations of same type) as necessary.
Each view is referenced by an ID, which is composed of its type and its name.

As a convention, a default view for `list` and `form` types should be defined for each entity.

**The generic filename format** is: `{class_name}.{view_type}.{view_name}.json`

* `class_name`: the class name of the entity the view relates to (e.g. default form view for  `core\User` is stored as `packages/core/views/User.form.default.json`)
* `view_type`: Possible values are :'*list*', '*form*','*chart*','*dashboard*'
* `view_name`: As a convention, classes should always have a 'default' view for types 'list' and 'view'.



Within a view file, a layout defines the way in which the Items are linked to the model. The view is synchronized with the model during modifications.

Keep in mind that if the view's class extends another class, which will be called the parent, then it should also contain all the fields from this parent class except the computed ones and the ones that are already present in this child class.

A **Model** is a collection of objects of a given entity. This class keeps the full schema of the entity with the default values that are then updated by this model after it requests the corresponding data from the server.

A **Layout** is the layout associated with a given view. It is always linked to a Model.

A **Item** is responsible for displaying the value of an object's field (in 'view' or 'edit' mode). It synchronizes its value with the Model to which it is associated via the Layout and the View that is using it.



Here is an example of the views defined for the `core\User` entity :

| **FILENAME**       | **ENTITY** | **VIEW TYPE** | **VIEW NAME** | **VIEW ID** | **Workbench Name**|
| --------------------------------- | --------- | --------- | --------- | --------- | --- |
| `core\views\User.list.default.json` | core\User | list | default | `list.default` | core\User:list.default|
| `core\views\User.form.default.json` | core\User | form | default | `form.default` | core\User:form.default|



## Types of views

### Form

**Forms** allow to view and edit individual objects. It is possible to define as many views as desired, and a given entity should always have default form view (`{entity}.form.default.json`).

Forms views are JSON objects that describe how to render a specific view related to a given entity.

**Example:** 

```json
{
    "name": "User",
    "description": "Basic form for displaying User.",
    "layout": {
        "groups": [
            {
                "id": "group.user",
                "sections": [
                    {
                        "label": "User details",
                        "id": "section.details",
                        "rows": [
                            {
                                "columns": [
                                    {
                                        "width": "33%",
                                        "align": "left",
                                        "items": [
                                            {
                                                "id": "item.user_id",
                                                "type": "field",
                                                "value": "id",
                                                "width": "50%",
                                                "label": "User ID",
                                                "readonly": true
                                            },
                                            {
                                                "type": "field",
                                                "value": "login",
                                                "width": "50%",
                                                "label": "Login",
                                                "help": "Email address of the user."
                                            },
                                            {
                                                "type": "field",
                                                "value": "validated",
                                                "width": "50%",
                                                "label": "Validated",
                                                "help": "Status of validation of this account."
                                            },
                                            {
                                                "type": "field",
                                                "value": "password",
                                                "width": "50%",
                                                "help": "Enter a new value to update."
                                            }
                                        ]
                                    },
                                    {
                                        "width": "33%",
                                        "align": "left",
                                        "items": [
                                            {
                                                "type": "field",
                                                "value": "firstname",
                                                "width": "50%",
                                                "label": "User given name",
                                                "help": "Forename of the user."
                                            },
                                            {
                                                "type": "field",
                                                "value": "lastname",
                                                "width": "50%",
                                                "label": "User family name"
                                            },
                                            {
                                                "type": "field",
                                                "value": "fullname",
                                                "width": "50%"
                                            },
                                            {
                                                "type": "field",
                                                "value": "language",
                                                "width": "50%",
                                                "widget": {
                                                    "type": "select",
                                                    "values": ["fr", "en", "nl"]
                                                }
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        "label": "Preferences",
                        "id": "section.preferences",
                        "rows": [
                            {
                                "columns": [
                                    {
                                        "width": "100%",
                                        "items": [
                                            {
                                                "type": "field",
                                                "value": "setting_values_ids",
                                                "width": "100%"
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        "label": "Groups",
                        "id": "section.groups",
                        "rows": [
                            {
                                "columns": [
                                    {
                                        "width": "100%",
                                        "align": "left",
                                        "items": [
                                            {
                                                "type": "field",
                                                "value": "groups_ids",
                                                "label": "Groups",
                                                "width": "100%",
                                                "widget": {
                                                    "view": "list.selection"
                                                }
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        ]
    }
}
```





### List

List views are used to display collections of items. It contains the same properties mentioned in the ```Form View``` section, such as `name`, `description`, `layout` and additional ones, specific to Lists.
Clicking on a row in the list redirects you to the form view related to the targeted entity.
Ticking one or more checkboxes triggers the display of a list of available actions that can be applied on the selection.

**Example:**

```
{
  "name": "",
  "description": "",
  "domain": [],
  "filters": [
    {
        "id": "lang.french",
        "label": "français",
        "description": "Users with locale set to french",
        "clause": ["language", "=", "fr"]
    }
  ],
  "layout": {
    "items": [
        {
            "type": "field",
            "value": "id",
            "width": "10%",
            "sortable": true,
            "readonly": true
        },
        {
            "type": "field",
            "value": "created",
            "width": "25%",
            "sortable": true
        },
        {
            "type": "field",
            "value": "validated",
            "width": "10%"
        },
        {
            "type": "field",
            "value": "login",
            "widget": {
                "link": true
            },
            "width": "30%",
            "sortable": true
        },
        {
            "type": "field",
            "value": "language",
            "width": "10%",
            "widget": {
                "type": "select",
                "values": ["fr", "en", "nl"]
            }
        },
        {
            "type": "field",
            "value": "groups_ids",
            "label": "Groups",
            "width": "0%",
            "visible": false,
            "widget": {
                "type": "one2many"
            }
        }
    ]
  }
}
```




### Menu

Menus allow to define custom tree structures of action-buttons for accessing specific routes or contexts.

Menu items have the following structure :

| **PROPERTY** | **DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ |
| id           | Identifier of the item (used for translations).              |
| label        | Title of the item to display within the menu.                |
| description  | (optional) Short string explaining the purpose of the item (the view it leads to). |
| icon         | (optional) icon to show aside the item.                      |
| type         | (mandatory) either 'entry' or 'parent'. In case an item is a 'parent', it also have a 'children' property. |



Parent items have a **children** property, which is an array holding a list of items (which, in turn, can be either parents or entries).

Entries items have a **context** property, which has the following structure :




| **PROPERTY** | **DESCRIPTION**                                          |
| ------------ | -------------------------------------------------------- |
| entity       | Entity to which relates the view to show.                |
| view         | ID of the view to use for showing the targeted entities. |
| order        | (optional)                                               |
| sort         | (optional)                                               |
| domain       | (optional) Domain to apply to specified view.            |

**Example:**

```json
"id": "item.pos_sessions",
"label": "Sessions",
"description": "",
"icon": "menu_book",
"type": "parent",
"children": [
    {
        "id": "item.pos_sessions.pending",
        "type": "entry",
        "label": "Pending sessions",
        "description": "",
        "context": {
            "entity": "lodging\\sale\\pos\\CashdeskSession",
            "view": "list.default",
            "order": "created",
            "sort": "desc",
            "domain": [ ["status", "=", "pending"], ["center_id", "in", "user.centers_ids"] ]
        }
    }
]
```



As other views, a menu has a `name` property and a `layout` property, that describes how the items are going to be displayed.

In the example shown below, one parent menu item is present named "New Booking" and it contains two children, "New Booking" to create a new booking and "All Bookings" that displays the list of all the bookings ordered by id and sorted in descending order.

```json
{
    "name": "Booking menu",
    "layout": {
        "items": [
            {
                "id": "item.bookings",
                "label": "Bookings",
                "description": "",
                "icon": "menu_book",
                "type": "parent",
                "children": [
                    {
                        "id": "item.new_booking",
                        "type": "entry",
                        "label": "New booking",
                        "description": "",
                        "icon": "add",
                        "context": {
                            "entity": "lodging\\sale\\booking\\Booking",
                            "view": "form.default",
                            "purpose": "create"
                        }
                    },
                    {
                        "id": "item.all_booking",
                        "type": "entry",
                        "label": "All bookings",
                        "description": "",
                        "context": {
                            "entity": "lodging\\sale\\booking\\Booking",
                            "view": "list.default",
                            "order": "id",
                            "sort": "desc"
                        }
                    }
                ]
            }
        ]
    }
}
```



### Dashboard

Dashboard views are control panels, opening the possibility to show multiple views on the same page.

The following example displays 4 different views to simplify the management of informations.

| **PROPERTY** | **DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ |
| id           | Identifier of the item (used for translations).              |
| label        | Title of the item to display within the menu.                |
| description  | (optional) Short string explaining the purpose of the item (the view it leads to). |
| width        | Width of the item (as percent value).                        |
| entity       | Entity to which relates the view to show.                    |
| view         | ID of the view to use for showing the targeted entities.     |
| domain       | (optional) Domain to apply to specified view.                |

**Example:**

```json
{
    "name": "Main dashboard",
    "description": "",
    "layout": {
        "groups": [
			{
                "label": "test",
                "height": "100%",
                "sections": [
                    {
                        "rows": [
                            {
                                "height": "50%",
                                "columns": [
                                    {
                                        "width": "100%",
                                        "items": [
                                            {
                                                "id": "item.bookings",
                                                "label": "Alertes",
                                                "description": "",
                                                "width": "50%",
                                                "entity": "core\\alert\\Message",
                                                "view": "list.dashboard",
                                                "domain":  ["object_class", "=", "lodging\\sale\\booking\\Booking"]
                                            },
                                            {
                                                "id": "item.bookings2",
                                                "label": "Mes Réservations",
                                                "description": "",
                                                "width": "50%",
                                                "entity": "lodging\\sale\\booking\\Booking",
                                                "view": "list.dashboard",
                                                "domain": ["creator", "=", "user.id"]
                                            }

                                        ]
                                    }
                                ]
                            },
                            {
                                "height": "50%",
                                "columns": [
                                    {
                                        "width": "100%",
                                        "items": [
                                            {
                                                "id": "item.bookings3",
                                                "label": "CA Prévisionnel des réservations",
                                                "description": "",
                                                "width": "50%",
                                                "entity": "lodging\\sale\\booking\\Booking",
                                                "view": "chart.default"
                                            },
                                            {
                                                "id": "item.bookings4",
                                                "label": "Nombre de checkin",
                                                "description": "",
                                                "width": "50%",
                                                "entity": "lodging\\sale\\booking\\Booking",
                                                "view": "chart.checkin"
                                            }

                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        ]
    }
}
```




### Chart

Charts enable us to visually compare multiple sets of data. It can be very helpful to display statistics.

Below is an example of a chart view, the proprerties are very similar to the ones we can find in menus (check the section above this one).

**Example:**


```json
{
    "name": "Booking total",
    "description": "This view displays the amount of bookings in the DB",
    "access": {
        "groups": ["booking.default.user"]
    },
    "layout": {
        "entity": "lodging\\sale\\booking\\Booking",
        "group_by": "range",
        "range_interval": "year",
        "range_from": "date.this.year.first",
        "range_to": "date.this.year.last",
        "datasets": [
            {
                "label": "Nombre de réservations",
                "operation": ["COUNT", "object.id"],
                "domain": ["id", ">", 5]
            }
{
    "name": "Main dashboard",
    "layout": {
        "groups": [
            {
                "label": "",
                "height": "100%",
                "sections": [
                    {
                        "rows": [
                            {
                                "height": "50%",
                                "columns": [
                                    {
                                        "width": "50%",
                                        "items": [
                                            {
                                                "id": "item.bookings",
                                                "label": "Bookings",
                                                "description": "",
                                                "width": "50%",
                                                "entity": "",
                                                "view": ""
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        ]
    }
}
```



The **access** property is allowing the people that belong to the group to see this chart. The layout describes how the items are going to be displayed.

The **range_interval** property allows us to choose which period of time will delimit the data that we want to see, the possibilities are : "day, week, semester, year".

The **range_to & range_from** properties allow us to choose, when the range starts and when it stops (linked with the range_interval property).
Possibilities : date.[this|prev|next].[day|week|month|quarter|semester|year].[first|last].

The **dataset** property is about the data that will be shown in the graph, we have the label property that will allow us to the name the element displayed.

The **operation** property that will use the operations talked about in the above sections, will allow us to display a certain type of data.

At last, the **domain** property allows us to filter the data even more.

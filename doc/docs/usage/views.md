# Views

Views are intended to describe how to present the objects to end-users under a given context.
They are used as templates for the front-end, and are stored as JSON files within the `views` folder of their respective package.

Each of them represents a mode of visualization: form, list, chart, dashboard, etc; and can be edited independently from the models they relate to.

It is possible to define as many views (of different types, or variations of same type) as necessary.
Each view is referenced by an ID, which is composed of its type and its name.

As a convention, a default view for `list` and `form` types should be defined for each entity.

**The generic filename format** is: `{class_name}.{view_type}.{view_name}.json`

* `class_name`: the class name of the entity the view relates to (e.g. default form view for  `core\User` is stored as `packages/core/views/User.form.default.json`)
* `view_type`: Possible values are :'*list*', '*form*','*chart*','*dashboard*'
* `view_name`: As a convention, classes should always have a 'default' view for types 'list' and 'view'.



Here is a recap for the `core\User` entity :

| **FILENAME**       | **ENTITY** | **VIEW TYPE** | **VIEW NAME** | **VIEW ID** | **Workbench Name**
| --------------------------------- | --------- | --------- | --------- | --------- | --- |
| `core\views\User.list.default.json` | core\User | list | default | list.default | core\User:list.default
| `core\views\User.form.default.json` | core\User | form | default | form.default | core\User:form.default


## Front-end logic

A **View** relates to an entity and has a type and a name. The view itself requests the corresponding data from the server (template or translation) when loading the layout at which a domain can be specified.
Within a view, a layout defines the way in which the Items are linked to the model. The view is synchronized with the model during modifications.

Keep in mind that if the view's class extends another class, which will be called the parent, then it should also contain all the fields from this parent class except the computed ones and the ones that are already present in this child class.

A **Model** is a collection of objects of a given entity. This class keeps the full schema of the entity with the default values that are then updated by this model after it requests the corresponding data from the server.

A **Layout** is the layout associated with a given view. It is always linked to a Model.

A **Item** is responsible for displaying the value of an object's field (in 'view' or 'edit' mode). It synchronizes its value with the Model to which it is associated via the Layout and the View that is using it.


## List and Form view

#### Form views

**Forms** allow to view and edit individual objects. It is possible to define as many views as desired, and a given entity should always have default form view (`{entity}.form.default.json`).

Forms views are JSON objects that describe how to render a specific view related to a given entity.

#### List views

List views are used to display collections of items. It contains the same properties mentioned in the ```Form View``` section, such as `name`, `description`, `layout` and additional ones, specific to Lists.
Clicking on a row in the list redirects you to the form view related to the targeted entity.
Ticking one or more checkboxes triggers the display of a list of available actions that can be applied on the selection.

---

### View (root of the structure)

This is the root of the structure of a view.

#### Structure summary

| **PROPERTY**| **TYPE**| **DESCRIPTION**|
| --- | --- | --- |
| name | `string` | Name of the view that will be displayed in the final app
| description | `string` | description of the view and it's purpose
| domain | `array`>`domain` | domain to filter which item of the input collection will be displayed
| filters | [Filter](#filters) | associative array of couple (name,clauses |
|controller| `string`>`orm/entity` | controller used to fetch the object from the db. needs to be a data-controller that extends of core_model_collect (by default is core_model_collect)
| header | [Header](#header) | Override the default header of the view |
| actions | list of [Actions](#actions) | Custom action that can be applied on the context of the view |
| routes | list of [Routes](#routes) | Contextual link to other part of the application. |
| access|  [Access](#access)| Define the ability to see the view for an user group or a user|

##### Attributes for list-views

| **PROPERTY**| **TYPE**| **DESCRIPTION**|
| --- | --- | --- |
| order        | `string`   | (optional) `asc` or  `desc`  |
| sort        |   `string`  | (optional)  field name(s) used to sort the view by default |
| limit   |  `integer`>`number/natural`      | (optional)  number of item fetched     |
| group_by | [Group by](#group_by) | (optional)        |
|  operation | Associative array (name,[Operation](#operation)) | (optional) make calculation on the whole fetcthed data do display it
|  export | [Export](#export)

### order

String holding the name(s) of the field to sort results on, separated with commas.
Example :

```json
"order": "sku,product_model_id"
```

### controller

The optional **controller**  property specifies the controller that must be requested for fetching the Model collection that will feed the View (either a single object or a collection of objects).

The default values is `model_collect` (which is an alias for `core_model_collect`)

### group_by

A `group_by` array can be set to describe the way the objects have to be grouped.
Each item in the array is either a field name or the descriptor of an operation to perform on a specific field.

When objects are grouped on fields, they're sorted on the field when it is a string, or on the `name` field when it is an object. In the latter case, the field to be used for sorting can be modified using the `order`  property.

Example :

```json
"group_by": ["date"]
```

The operations items have the following structure :

```json
{
    "field": "product_id",
    "operation": ["SUM", "object.qty"],
    "order": "name"
}
```

Another example :

```json
"group_by": ["date", {"field": "product_id", "operation": ["SUM", "object.qty"]}]
```

#### Binary operators
|**OPERATOR**|**RESULT**|**SYNTAX**|
|--|--|--|
|+|Sum of `a` and `b`.|`['+', a, b]`|
|-|Difference between `a` and `b`.|`['-', a, b]`|
|*|Product of `a` by `b`.|`['*', a, b]`|
|/|Division of `a` by `b`.|`['/', a, b]`|
|%|Modulo `b` of `a`.|`['%', a, b]`|
|^|`a` at power  `b`.|`['^', a, b]`|

#### Unary operators

|**OPERATOR**|**SYNTAX**|
|--|--|
|SUM|`['SUM', object.field]`|
|AVG|`['AVG', object.field]` (which is a shortcut for `['/', ['SUM', object.field], ['COUNT', object.field]]`)|
|COUNT|`['COUNT', object.field]`|
|MIN|`['MIN', object.field]`|
|MAX|`['MAX', object.field]`|



----


### domain

The **domain** property allows to conditionally display the data  (More Info: [domain](../architecture-concepts/domains.md)).

 Example:

 ```json
  "domain": ["type", "<>", "I"]
 ```

---

### access

Associative map for restriction the access of the view to specific users.

| **PROPERTY** | **DESCRIPTION**                                       |
| --------------- | ------------------------------------------------------------ |
| groups     | Array of group names whose members are granted for using the view. |
| users | Array of logins of users that are granted for using the view. |

Example :

```json
"access": {
    "groups": ["users"]
}
```

----

### operations

This property allows to apply a series of operations on one or more columns, for the displayed records set.

Each entry of the `opearations` object associates a name (ID of an operation - which will allow to group the results), with an associative array mapping field names with operation descriptors.

In turn, each descriptor accepts the following properties :

#### Structure Summary

| **PROPERTY** |**TYPE**| **DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ---- |
| operation    | `string` | The operation to apply on the related field (see operation syntax below). |
| usage        | `string` | The `usage` of the operation as hint for displaying the result. (see) Example : `amount/money:2`, `numeric/integer` |
| suffix       | `string` | (optional) string to append to the result.                   |
| prefix       | `string` | (optional) string to prepend to the result.                  |

#### Operation Syntax

```
Unary operators : [ OPERATOR, {FIELD | OPERATION} ]
```
OR
```
Binary operators : [ OPERATOR, {FIELD | OPERATION}, {FIELD | OPERATION} ]
```


Examples:

```json
"operations": {
    "total": {
        "total_paid": {
            "id": "operations.total.total_paid",
            "label": "Total received",
            "operation": "SUM",
            "usage": "amount/money:2"
        },
        "total_due": {
            "operation": "SUM",
            "usage": "amount/money:2"
        }
    }
}
```



```json
"operations": {
    "total": {
        "rental_unit_id": {
           "operation": "COUNT",
           "usage": "numeric/integer",
           "suffix": "p."
        }
    }
}
```


----

### action

The optional **actions**  property  contains a list of objects defining a custom list of possible actions attached to the view.

Each action item  relates to a button, displayed in the right side of the header, which, when clicked, relays a request to a given controller. Upon successful completion of the action, the view is automatically refreshed.

!!! note "Distinction between actions and header.actions"
    Make sure not to mix up the "actions" section with the header "actions" subsection. The former lists the actions that are available for the whole view (genrally form views) while the latter can be used to allow or prevent specific actions on selected objects (generally list views).

#### Structure summary

| **PROPERTY**  | **TYPE**  | **DESCRIPTION**                                       |
| --------------- | ---- | --------------------------------------------------------- |
| id  | `string`    | Identifier of the action for translation purpose (can be set in the i18n related file). |
| description| `string` | The description that is displayed to the user when (s)he clicks on the related button. |
| label |  `string`| Label assigned to the view.                                  |
| controller | `string>orm/entity`  | Controller to invoke when the user confirms the action. By default, the `id` of the current object is sent as a parameter. |
| visible | `array>domain`    | (optional) Domain (array) of conditions to meet in order to make the action button visible. Example: `"visible": ["status", "=", "quote"]` |
| confirm | `boolean`    | (optional) Boolean flag. If set to true, a confirmation dialog is displayed before relaying the request to the controller. |
| params  | `array` | (optional) Associative array mapping fields with their values. Values can be assigned by referencing a property of the current user (e.g. `user.login`) or current object (for form views). |
| access | [Access](#access) | Associative array (similar to the one that can be applied on the view) to limit access to the action (this is used to display or hide the action in the list, but the actual permission to invoke the related controller must be set in the controller itself). |


If target controller requires one or more parameter, the view will generate a dialog asking the user for the values to be assigned to each parameter.

If no parameter is required but the `confirm` property is set to `true`, then a confirmation dialog is displayed before performing the action.

Here below is a flow diagram that recaps the interactions between the controller and the `confirm` property.

<center><img src="/assets/img/eq_confirm_diagram.png" /></center>

<a name="common_controller"></a>

Example:

```json
"actions": [
  {
    "id": "action.option",
    "label": "Set as Option",
    "description": "Rental units will be blocked but no funding will be claimed yet.",
    "controller": "lodging_booking_option",
    "visible": ["status", "=", "quote"],
    "confirm": true,
    "params": {
        "id": "object.id"
    }
  },
  {
    "id": "action.validate",
    "label": "Confirm Booking",
    "description": "Rental units will be blocked and the invoicing plan will be set up.",
    "controller": "lodging_booking_confirm",
    "visible": ["status", "in", ["quote", "option"]]
  },
  {
    "id": "action.checkin",
    "label": "Check In",
    "description": "The host has arrived: the rental units will be marked as occupied.",
    "controller": "lodging_booking_checkin",
    "visible": ["status", "=", "validated"]
  },
  {
    "id": "action.checkout",
    "label": "Check Out",
    "description": "The host is leaving: the rental units will be marked for cleaning.",
    "controller": "lodging_booking_checkout",
    "visible": ["status", "=", "checkedin"]
  },
  {
    "id": "action.invoice",
    "label": "Invoice",
    "description": "The host has left and room has been reviewed: emit the invoice.",
    "controller": "lodging_booking_invoice",
    "visible": ["status", "=", "checkedout"]
  }
]
```

---

##### Filter
The `filters` property allows to customize the filtering features available in the header of the view.

| **PROPERTY** | **TYPE** | **DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ---- |
| id| `string` | Unique ID used for translation |
| label| `string` | default name is no translation is set |
| clause | `array`>`clause` | Clause that will be added to the view domain to search with the filter |

```json
"filters": [
    {
        "id": "lang.french",
        "label": "French",
        "description": "French speaking people",
        "clause": ["language", "=", "fr"]
    }
]```
```

<a name="list_header"></a>



Custom actions can have an arbitrary ID, while default actions use the common actions IDs :
```
ACTION.EDIT
ACTION.EDIT_BULK
ACTION.EDIT_INLINE
ACTION.CLONE
ACTION.ARCHIVE
ACTION.DELETE
```

!!! note "Hiding a specific default action"
    Default actions can be hidden by using the targeted ID and setting the `visible` property to false.

---

### exports

Printing a document such as a contract can be done in the `list` view. Multiple fields will have to be added such as the `id` of the contract, the `label`, the `icon` of the printer also known as "print" is added. Also, a small `description`, a `controller` having the value "model_export-print" used to trigger the printing action, the `view` which corresponds to the specific view "print.default" and finally `visible` field should be displayed as well.

All these fields are added inside of the <em>exports</em> section of list view, which is an array of objects that has the below structure:

```json                                           
"exports": [
        {
            "id": "export.print.contract",
            "label": "Print contract",
            "icon": "print",
            "description": "Print contract related to the booking.",
            "controller": "lodging_booking_print-contract",
            "view": "print.default",
            "visible": ["status", "=", "quote"]
        }
    ]
```

---

### header <a id="view_commons_header"></a>

The **header** section allows to override the default behavior of the view.

##### **Structure summary**

| **PROPERTY** | **TYPE** | **DESCRIPTION**                                              |
| ------------ | ---- | ------------------------------------------------------------ |
| actions      | [Actions](#header-actions) | This property allows to customize the actions buttons shown in the left part of the View header. |
| selection | [Selection](#selection) | Action that applies on item selection (*this property is intended for list views only*). |
| visible      | `boolean` or `array`>`domain`                  | |
| advanced_search | `boolean` or descriptor |  |

---

#### actions (header)

!!! note "Distinction between root `actions` property and header's `actions` sub section"
    Make sure not to mix up the "actions" section with the header "actions" subsection. The former lists the actions that are available for the whole view (generally form views) while the latter can be used to allow or prevent specific actions on selected objects (generally list views).

The actions property can be used for 3 purposes: to force action buttons visibility; to define the order of the actions for buttons having multiple actions ("split buttons"); and to override the configuration of the subsequent Views (for relational fields).

* For **forms**, default actions are : `ACTION.EDIT`, `ACTION.SAVE`, `ACTION.CREATE`, `ACTION.CANCEL`
* For **lists**, default actions are : `ACTION.CREATE`, `ACTION.SELECT`, `ACTION.OPEN`

Each action item is either a boolean or an array of items describing the order of the buttons and parameters for subsequent views.


!!! Note "Action descriptor"
    Actions descriptors used in the header has the same structure than View actions. Therefore, it is possible to force the view to use a specific controller when a standard button is clicked.

Empty arrays or items set to false mean that the action is not available for the View. If action is an array with multiple items, the related button will be displayed as a split-button (note: in that case, the order of the items is maintained).

Here is the exhaustive list of the actions ID that are supported by the views. Each ID corresponds to a button that will be present (or not, according to the config of the view) in the header of the view. `header.actions` allows to map these actions ID with predefined or custom action items  (note that custom action ID are not accepted).

##### **Structure summary**

| **PROPERTY** | **TYPE** | **DESCRIPTION**                                              |
| ------------ | ---- | ------------------------------------------------------------ |
| id | `string` | Id of the predefined action, define the behavior of the action |
| description | `string` ||
| domain | `array`>`domain` ||
| view | `string` | id of the view used if the action require displaying a view|

**Possible actions**

| **ACTION** | **DESCRIPTION** | **ID(S)** |
| ---- | ---- |---- |
| ACTION.EDIT | For forms in view mode, allows to edit the current object. |  `EDIT`|
| ACTION.SAVE | For forms in edit mode, **ACTION.SAVE** is the action used for storing the changes made to the current object. |`SAVE_AND_CLOSE`, `SAVE_AND_VIEW`, `SAVE_AND_CONTINUE`|
| ACTION.CREATE | For list views, **ACTION.CREATE** is the action used for creating a new object of the current entity. |`CREATE`, `ADD`|
| ACTION.CANCEL | For forms in edit mode, allows to cancel the changes made to the current object. |`CANCEL_AND_CLOSE`, `CANCEL_AND_VIEW`|
| ACTION.SELECT | For relational fields, allows to select or add one or many objects and relay selection to parent View. | `SELECT` |
| ACTION.OPEN |  | `OPEN` |

##### Predefined actions

| **ACTION** | **DESCRIPTION** | **CONTROLLER** |
| ---- | ---- | ---- |
| SAVE_AND_CLOSE | The view is saved and close. The user is brought to the previous context. | |
| SAVE_AND_CONTINUE | The view is saved and left open allowing the user to perform further changes. A snackbar is given as feedback to the user. | |
| SAVE_AND_VIEW | The view is saved and closed. The user is brought to the 'view' version (SAVE action can only occur in 'edit' mode). In most cases, the 'view' version is the parent. If not, a new (similar) context is opened in 'view' mode. | |
| SAVE_AND_EDIT | The view is saved and closed. The user is brought to a cloned context (still in 'edit' mode). | |
| CREATE |  | model_create |
| SELECT | An object in the current list view has been selected (selection might consist in several selected objects). |  |
| ADD | A context is requested for adding one or more object to a many2many list. | model_update |
| CANCEL | The action relating to the current view (form in edit mode : CREATE, EDIT), has been cancelled by the user. |  |
| OPEN | A request for opening a form view for a given object has been received by the list. |  |

##### Usage example

```json
    "header": {
        "actions": {
            "ACTION.CREATE": [
                {
                    "view": "form.create",
                    "description": "Overload form to use for objects creation.",
                    "domain": ["parent_status", "=", "object.status"],
                    "access": {
                        "groups":["admin"]
                    },
                    "controller": "custompackage_mode_update"
                }
            ],
            "ACTION.SELECT": false,
            "ACTION.SAVE": [ {"id": "SAVE_AND_CONTINUE"}, {"id": "SAVE_AND_CLOSE"} ],
            "ACTION.CANCEL": [ {"id": "CANCEL_AND_VIEW"}]
        }
    }
```

---

#### selection
The `selection` property allows to customize the list of bulk actions that are available when one or more items are selected.

| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| **default**  | `boolean` | (optional)telling if the default actions have to be present in the available action to apply on current selection. (default = false) |
| **actions**  | list of [Action](#action) |(optional) An array of action items that can be applied on current selection. |


Examples :

a. Prevent selecting items within the list:

```json
"header": {
    "selection": false
}
```

b. Hide default actions for the selection, allow only `ACTION.CLONE`, and add a custom action :

```json
"header": {
    "actions": {
        "ACTION.CREATE" : false
    },
    "filters": {
        "custom": true,
        "quicksearch": false
    },
    "selection": {
        "default" : false,
        "actions" : [
            {
                "id": "header.selection.actions.mark_ignored",
                "label": "Mark as ignored",
                "icon": "",
                "controller": "lodging_sale_booking_bankstatementline_bulk-ignore"
            },
            {
                "id": "ACTION.CLONE",
                "visible": false
            }
        ]
    }
}
```

---

#### advanced_search
In the case where the specified controller for data collection is not the default controller (`core_mode_collect`), the UI checks if a view is associated with this controller (that must extend `core_model_collect`). Since controllers can be handled as an entity. In that case, its parameters are considered as fields that can be assigned in an "advanced search" form.

However, in the header, it is always possible to explicitly tell if the advanced search must be shown or not, and if it should be open when view loads (default behavior is closed).

Examples : 
```
    "advanced_search": false
```

```
    "advanced_search": {
        "show": true,
        "open": true
    }
```

---

### layout

The **layout** is the part of the view that contains all the information needed to display the Model in the view

#### Structure summary

##### Form View

| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| Groups | list of [Group](#group) | Groups of Section used to split the page horizontally |

##### List View

| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| Items | list of [Item](#item) | List of the column to display |

---

### Group

**Groups** are horizontal part of a form view, containing sections.

#### Structure summary

| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| id | `string` | unique, used for translations.
| label | `string` | (optional) name used when no translation is available.
| sections |  list of [Section](#section) | list of section, represented as tabs |

---

### Section

**Section** Are tabs that contains information about an instance of a model

#### Structure summary

| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| id | `string` | unique id, used for translations.
| label | `string` | (optional) name used when no translation is available.
| rows |  list of [Row](#row) |  |
|visible | `array`>`domain` | Domain that use the context to allow display of the section or not |

---

### Row

#### Structure summary

| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| id | `string` | unique id, used for translations.
| label | `string` | (optional) name used when no translation is available.
| rows |  list of [Column](#column) |  |
|visible | `array`>`domain` | Domain that use the context to allow display of the section or not |

---

### Column

#### Structure summary

| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| id | `string` | unique id, used for translations.
| label | `string` | (optional) name used when no translation is available.
| rows |  list of [Item](#item) |  |
|visible | `array`>`domain` | Domain that use the context to allow display of the section or not |
| width  | `integer`>`number/natural:100` | width of the column |

---

### Item

**Items** are a descriptor of the way a **field** or a **label** of a **Model** is displayed in a view.

#### Structure summary

| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| id | `string` | (optional) unique id, used for traductions (traductions override label and value) |
| label | `string` | (optional) override the value for the title value of the item |
| type | `string` | type of item (either `field` or `label`) |
| width |  `integer`>`number/natural:100`| width of the field (in percentage its parent) |
| readonly | `boolean` | tell if the item is editable in a creation or edtition context of the usage of the view |
| visible | `boolean` or `array`>`domain` | tell if the item should be displayed |
| widget | [Widget](#widget) | Give properties to the item, depends on the type of view |

---

### Widget

Widget are used to set properties that depends on the type of view.

#### Structure Summary

##### Form view

| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| link | `boolean` | Flag telling if the content of the item a link. |
| heading | `boolean`| Emphasizes the item (large version of the widget). |
| type | `string` | edit the type of display of the item, depends on the type of the field. |
| values | list of `string` | deprecated, do not use. |
| usage | `string` | override the usage of the field to display it. |
| header | [Header](#header) | *Associative Field Only*.  Override the header of the view to display the relation. |
| domain | `array`>`domain` | *Associative Field Only*.  Override the domain of the view to display the relation. |
| view | `string` | *Associative Field Only*. Provide the id of the view to use to display the relation. |
| autoselect | `boolean` | *One2Many Field Only*. Item is automatically selected when list contains only 1 (default = true). |


##### List view

| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| link | `boolean` | is the content of the item a link ? |
| sortable | `boolean`| can the user sort the list by this item ? |
| type | `string` | edit the type of display of the item, depends on the type of the field |
| values | list of `string` | deprecated, do not use. |
| domain | `array`>`domain` | |
| usage | `string` | override the usage of the field to display it|

---

## View Inheritance

Classes may be used in different packages (extending parent classes with the possibility of adding new fields) and therefore, they also may have different view files.

Here is an example of an extended class.


```php
<?php
namespace lodging\sale\booking;

class Contact extends \sale\booking\Contact {

    public static function getName() {
        return "Contact";
    }

    public static function getDescription() {
        return "Booking contacts are persons involved in the organisation of a booking.";
    }

    public static function getColumns() {

        return [
            'owner_identity_id' => [
                'type'              => 'many2one',
                'foreign_object'    => 'lodging\identity\Identity',
                'description'       => 'The organisation which the targeted identity is a partner of.',
                'default'           => 1
            ],

            'partner_identity_id' => [
                'type'              => 'many2one',
                'foreign_object'    => 'lodging\identity\Identity',
                'description'       => 'The targeted identity (the partner).',
                'onupdate'          => 'identity\Partner::onupdatePartnerIdentityId',
                'required'          => true
            ],

            'booking_id' => [
                'type'              => 'many2one',
                'foreign_object'    => 'lodging\sale\booking\Booking',
                'description'       => 'Booking the contact relates to.',
                'required'          => true
            ],

            'owner_identity_id' => [
                'type'              => 'many2one',
                'foreign_object'    => 'lodging\identity\Identity',
                'description'       => 'The organisation which the targeted identity is a partner of.',
                'default'           => 1
            ]

        ];
    }
}
```

To decide which view will be used, eQual uses a system of **class inheritance**, where the child view (*class extending the parent*) **replaces** the parent (*class being extended*) view file.

To understand how it works, here is the code :

```php
<?php
list($params, $providers) = announce([
    'description'   => "Returns the JSON view related to an entity (class model), given a view ID (<type.name>).",
    'params'        => [
        'entity' =>  [
            'description'   => 'Full name (including namespace) of the class to return (e.g. \'core\\User\').',
            'type'          => 'string',
            'required'      => true
        ],
        'view_id' =>  [
            'description'   => 'The identifier of the view <type.name>.',
            'type'          => 'string',
            'default'       => 'list.default'
        ]
    ],
    'response'      => [
        'content-type'  => 'application/json',
        'charset'       => 'utf-8',
        'accept-origin' => '*'
    ],
    'providers'     => ['context', 'orm']
]);


list($context, $orm) = [$providers['context'], $providers['orm']];

$entity = $params['entity'];

// retrieve existing view meant for entity (recurse through parents)
while(true) {
    $parts = explode('\\', $entity);
    $package = array_shift($parts);
    $file = array_pop($parts);
    $class_path = implode('/', $parts);
    $file = QN_BASEDIR."/packages/{$package}/views/{$class_path}/{$file}.{$params['view_id']}.json";

    if(file_exists($file)) {                               //(1)
        break;
    }

    $parent = get_parent_class($orm->getModel($entity));   //(2)

    if(!$parent || $parent == 'equal\orm\Model') {         //(3)
        break;
    }

    $entity = $parent;
}

if(!file_exists($file)) {
    throw new Exception("unknown_view_id", QN_ERROR_UNKNOWN_OBJECT);
}

if( ($view = json_decode(@file_get_contents($file), true)) === null) {
    throw new Exception("malformed_view_schema", QN_ERROR_INVALID_CONFIG);
}

$context->httpResponse()
        ->body($view)
        ->send();
```

**What it does :**

- **(1)** If the file exists in the view folder of the package where we are, the loop stops and uses that view.

- **(2)** Otherwise, it loops through the parent classes one by one, and takes the first view file we encounter.

- **(3)** This scenario goes on until we reach the root-parent class **`"equal\orm\Model"`**.



## Form views Example


### Minimal example

`Example.form.default.json`

```json
{
  "name": "Example",
  "description": "Simple form for displaying a basic objects",
  "actions": [],
  "layout": {
    "groups": [
      {
        "label": "",
        "sections": [
          {
            "rows": [
              {
                "columns": [
                  {
                    "width": "50%",
                    "items": [
                      {
                          "type": "field",
                          "value": "id",
                          "width": "50%"
                      },
                      {
                          "type": "field",
                          "value": "name",
                          "width": "50%"
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


#### Real life example

A real example of a form view is shown below, which is the Category form of a package having multiple `sections` (tabs) each having a label(Categories, Product Models and Booking Types) and an id(`section.categories_id`, `section.product_models`, `section.booking_types`) to be able to be translated in using the "i18n". The field called `name` has a `widget` property with an attribute `heading` set to true which emphasizes it by displaying it a little bigger.

The view's name is `Category.form.default.json` and is as follows:

```json
{
  "name": "Category",
  "description": "Categories are not related to Families and allow a different way of grouping Products.",
  "layout": {
    "groups": [
      {
        "sections": [
          {
            "label": "Categories",
            "id": "section.categories_id",
            "rows": [
              {
                "columns": [
                  {
                    "width": "50%",
                    "items": [
                      {
                        "type": "field",
                        "value": "name",
                        "width": "100%",
                        "widget": {
                            "heading": true
                        }
                      },
                      {
                        "type": "field",
                        "value": "description",
                        "width": "100%"
                      }
                    ]
                  }
                ]
              }
            ]
          },
          {
            "label": "Product Models",
            "id": "section.product_models",
            "rows": [
              {
                "columns": [
                  {
                    "width": "100%",
                    "items": [
                      {
                        "type": "field",
                        "value": "product_models_ids",
                        "width": "100%"
                      }
                    ]
                  }
                ]
              }
            ]
          },
          {
            "label": "Booking Types",
            "id": "section.booking_types",
            "rows": [
              {
                "columns": [
                  {
                    "width": "100%",
                    "items": [
                      {
                          "type": "field",
                          "value": "booking_types_ids",
                          "width": "100%"
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



## List View Example


### Minimal example

The name of file is displayed like so: `packages/core/views/User.list.default.json`
A list view is defined according to the following structure:

```json
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

The list view of the Category form mentioned in the above section contains the `name` of the list which is Categories, a `description` and the main fields to be displayed for quick use like the "name" and the "description" of the categories.
The list view is named *Category.list.default.json* and has the following structure:

```json
{
    "name": "Categories",
     "description": "This view is intended for displaying the list of categories.",
     "layout": {
         "items": [
            {
                 "type": "field",
                 "value": "name",
                 "width": "15%"
            },
            {
                 "type": "field",
                 "value": "description",
                 "width": "25%"
            }
        ]
    }
}
```



## Menu Views

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

Example:

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



## Dashboard Views

Dashboard views are control panels, opening the possibility to show multiple views on the same page.

The following example displays 4 different views to simplify the management of informations.

| **PROPERTY** | **DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ |
| id           | Identifier of the item (used for translations).              |
| label        | Title of the item to display within the menu.                |
| description  | (optional) Short string explaining the purpose of the item (the view it leads to). |
| width        | Width of the field.                                          |
| type         | (mandatory) either 'entry' or 'parent'. In case an item is a 'parent', it also have a 'children' property. |
| entity       | Entity to which relates the view to show.                    |
| view         | ID of the view to use for showing the targeted entities.     |
| domain       | (optional) Domain to apply to specified view.                |


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



#### list.dashboard

This example demonstrates the possibility to name views the way we want.

Here we use a new `list view`, that could, for example, have a different domain** (`"domain": ["creator", "=", "user.id"]`) than the `Booking.list.default.json` view.

```json
{
    "name": "Booking list",
    "description": "This view displays the list of bookings: the most recent on top.",
    "access": {
        "groups": ["booking.default.user"]
     },
    "order": "created",
    "sort": "desc",
    "domain": ["center_office_id", "in", "user.center_offices_ids"],
    "layout": {
        "items": [
            {
            }
        ]
    }
}
```




## Charts

Charts enable us to visually compare multiple sets of data. It can be very helpful to display statistics.

Below is an example of a chart view, the proprerties are very similar to the ones we can find in menus (check the section above this one).

### The properties
The access property is allowing the people that belong to the group to see this chart. The layout describes how the items are going to be displayed.

The range_interval property allows us to choose which period of time will delimit the data that we want to see, the possibilities are : "day, week, semester, year".

The range_to & range_from properties allow us to choose, when the range starts and when it stops (linked with the range_interval property).
Possibilities : date.[this|prev|next].[day|week|month|quarter|semester|year].[first|last].

The dataset property is about the data that will be shown in the graph, we have the label property that will allow us to the name the element displayed.

The operation property that will use the operations talked about in the above sections, will allow us to display a certain type of data.

At last, the domain property allows us to filter the data even more.


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

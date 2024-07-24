# Views common stucture

## Structure summary

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


Some attributes are meant to specific view types.
The lists below recap the attributes specific to each view type.


### List

| **PROPERTY**| **TYPE**| **DESCRIPTION**|
| --- | --- | --- |
| order        | `string`   | (optional) `asc` or  `desc`  |
| sort        |   `string`  | (optional)  field name(s) used to sort the view by default |
| limit   |  `integer`>`number/natural`      | (optional)  number of item fetched     |
| group_by | [Group by](#group_by) | (optional)        |
|  operation | Associative array (name,[Operation](#operation)) | (optional) make calculation on the whole fetcthed data do display it
|  export | [Export](#export)

### Menu

**Items specifics : **  

| **PROPERTY**| **TYPE**| **DESCRIPTION**|
| --- | --- | --- |
| type        | `string`   | (mandatory) either 'entry' or 'parent'. In case an item is a 'parent', it also have a 'children' property. |

### Dashboard

**Items specifics : **  

| **PROPERTY**| **TYPE**| **DESCRIPTION**|
| --- | --- | --- |
| width        | `string`   | Width of the item (as percent value).  |

## Attributes descriptors

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




### domain

The **domain** property allows to conditionally display the data  (More Info: [domain](../architecture-concepts/domains.md)).

 Example:

 ```json
  "domain": ["type", "<>", "I"]
 ```


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

### actions

The optional **actions**  property  contains a list of objects defining a custom list of possible actions attached to the view.

Each action item  relates to a button, displayed in the right side of the header, which, when clicked, relays a request to a given controller. Upon successful completion of the action, the view is automatically refreshed.

!!! note "Distinction between actions and header.actions"
    Make sure not to mix up the "actions" section with the header "actions" subsection. The former lists the actions that are available for the whole view (genrally form views) while the latter can be used to allow or prevent specific actions on selected objects (generally list views).

**Structure:**

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

### filters
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



### header <a id="view_commons_header"></a>

The **header** section allows to override the default behavior of the view.

#### common structure

| **PROPERTY** | **TYPE** | **DESCRIPTION**                                              |
| ------------ | ---- | ------------------------------------------------------------ |
| actions      | [Actions](#header-actions) | The `actions` property allows to customize the actions buttons shown in the left part of the View header. |
| visible      | `boolean` or `array`>`domain`                  |The `visible` property allows to place a condition in order to make the header visible or not. |




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

##### **Structure**

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

**Predefined actions**

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

##### Examples

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


#### visible
The `visible` property allows to place a condition in order to make the header visible or not.

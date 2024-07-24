# Lists

## header

The **header** section allows to override the default behavior of the view.

Here are the properties are specific to views.

(for the common properties of the Views header, see [views common strcucture](./common-structure)).

| **PROPERTY** | **TYPE** | **DESCRIPTION**                                              |
| ------------ | ---- | ------------------------------------------------------------ |
| selection | [Selection](#selection) | Action that applies on item selection (*this property is intended for list views only*). |
| visible      | `boolean` or `array`>`domain`                  | |
| advanced_search | `boolean` or descriptor |  |




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




## layout 
Lists views hold a **layout**  element that contains all the information needed to display the a list of objects that must be presented  in the view.

At the root of their layout descriptor, list views have a single array of Items descriptors. Each Item descriptor defines the structure of a row in the table, including the fields or attributes of the object to be displayed.

When rendered, a list view consists of a table with each of its columns showing the value for the corresponding object's attributes as defined in the Item descriptors. Users can interact with the table, such as sorting rows based on specific column values or filtering based on certain criteria.

| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| Items | list of [Item](#item) | List of the column to display |



### Items


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



### Widget

Widget are used to set properties that depends on the type of view.

**Structure Summary**



| **PROPERTY** | **TYPE** |**DESCRIPTION**                                              |
| ------------ | ------------------------------------------------------------ | ----- |
| link | `boolean` | is the content of the item a link ? |
| sortable | `boolean`| can the user sort the list by this item ? |
| type | `string` | edit the type of display of the item, depends on the type of the field |
| domain | `array`>`domain` | |
| usage | `string` | override the usage of the field to display it|




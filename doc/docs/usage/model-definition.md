# Model definition

Each Model is defined in a `.class.php` file , located in the `/packages/{package_name}/classes` file of the package it relates to (see [Directory Structure](directory-structure.md)). All classes inherit from a common ancestor: the `Model` class, declared in the `equal\orm` namespace and defined in  `/lib/equal/orm/Model.class.php`.

A class is always referred to as an **entity** and belongs to a package. Packages and their subdirectories are used as namespaces.

The syntax is : `{{package_name}}\{{class_name}}` (ex. :`core\setting\SettingValue`).

A class consists of a series of fields definition, along with specific methods.


Here is an example of a `Category ` class having multiple fields for which we will then show how to write its ```Form View``` and ```List View```.

```php
<?php 
// [...]

class Category extends Model {
    
    public static function getColumns() {
      return [
        'name' => [
          'type'              => 'string',
          'description'       => "Name of the category (for all variants).",
          'required'          => true
        ],

        'description' => [
          'type'              => 'string',
          'description'       => "A few details about category purpose and usage."
        ],
        
        'product_models_ids' => [ 
          'type'              => 'many2many', 
          'foreign_object'    => 'sale\catalog\ProductModel', 
          'foreign_field'     => 'categories_ids', 
          'rel_table'         => 'sale_product_rel_productmodel_category', 
          'rel_foreign_key'   => 'productmodel_id',
          'rel_local_key'     => 'category_id',
          'description'       => 'List of product models assigned to the category.'
        ]

      ];
    }
}
```



##  Classes

Objects are grouped into classes that define the structure and methods of those objects. 

Classes definitions from a same package are placed into the folder `//packages/[package_name]/classes//.`

Here is an example of the class definition syntax :
```php
<?php
namespace school;
use equal\orm\Model;

class Student extends Model {
  public static function getColumns() {
    return [
      'firstname'  => ['type' => 'string'],
      'lastname'   => ['type' => 'string'],
      'birthdate'  => ['type' => 'date']
    ];
  }
}
```



## Consistency with Database

Classes are mapped with database tables. Each table having structure (columns) matching the fields defined in the model. 

Consistency between models (`*.class.php` files) and database schema is done at package initialization (columns types must be compatible). It must be maintained manually in case of change.

When a new class is created or the schema of a class is modified, the SQL schema must be adapted consequently. Controllers `core_init_package` and `utils_sql-schema` are made to help with this task.

Also, Action controller `core_test_package-consistency` can help to spot any incompatibility or inconsistency in the definition the classes from a given package.


## Fields categories
| **CATEGORY**   | **DESCRIPTION**                                              |
| -------------- | ------------------------------------------------------------ |
| simple    | Direct field that holds a value of a given type. Such fields are stored as is in database and are automatically converted (SQL/PHP). |
| relational | Field whose value targets one or more objects. Supported relations are : `one2many`, `many2many`, and `many2one` (which behaves like a simple field) |
| computed   | Indirect field that results from a computation based on other values. Computed fields have a final type that can be either simple or relational. |

## Fields types <a id="definition_field_types"></a>

The type property sets the type of the field.

The following types are supported by the ORM : 

* `boolean`
* `integer`
* `float`
* `string`
* `text`
* `date`
* `time`
* `datetime`
* `binary`
* `many2one`
* `one2many`
* `many2many`
* `computed`
* `alias`

!!! note "usage property"
	The `usage` property is complementary to the `type` property and allows to refine the way the field should be handled by the ORM and the DBMS, and how it should be rendered in the UI. 
	

#### boolean

Numeric value of Boolean type (**true** or **false**)

!!! note "booleans notation"
	For booleans, we use the PHP built-in constant : true and false (when using '0', '1', 0 or 1, value is converted to a bool).

#### integer

Signed numeric value (negative or positive).

By default, strings are stored in DBMS using `INT(11)`.

!!! note "about integer precision" 
	PHP depends on the platform for integers size allocation (`PHP_INT_SIZE`), while SQL `INT` will always be 32 bits.

#### float (double)

Floating point numeric value (float or double)

By default, strings are stored in DBMS using `DECIMAL(10,2)`.

#### string

Short string without formatting/carriage returns (ex: lastname, place, …)

By default, strings are stored in DBMS using `VARCHAR(255)`.

Strings can potentially be long and include formatting, and some behavior require a specific combination of type and usage.

For instance:
```php
<?php
[
    'type'	=> 'string',
    'usage'	=> 'text/plain'
]
```
will be stored as MEDIUMTEXT and the UI will know it has to display the field as a TEXTAREA rather than as an INPUT.

whereas :


```php
<?php
[
    'type'	=> 'string',
    'usage'	=> 'phone'
]
```
will be stored as `VARCHAR(20)`.

#### text

By default, texts are stored in DMBS as `text`.

The TEXT data type has a size of string characters upto 65,535 bytes to manage classic long-form contents of the text.


#### binary

Any binary value (ex : a picture, a document, …)

Binary values can either be stored in the database or within the filesystem, under the `/bin` folder of the installation.

This behavior can be set using the `FILE_STORAGE_MODE` and `FILE_STORAGE_DIR` configuration parameter.

#### file

A file value is stored in the DB (like binaries) as a `LONGBLOB`.


#### date, time & datetime

When stored to the DBMS, those types follow the standard SQL format:

* date: YYYY-mm-dd
* time: HH:mm:ss
* datetime: YYYY-mm-dd HH:mm:ss

!!! Tip "Datetime manipulations using eQual"
	Within PHP scripts, eQual handles dates, times and datetimes as UTC timestamps. These are adapted to SQL format or JSON format when needed.

#### many2one
Relational field used for fields holding a N-1 relation. The stored value is an integer that holds the identifier of the pointed object.

Example:

```php
<?php
// from todolist\Task
// Each task has only 1 user assigned at a time
'user_id' => [
    'type'           => 'many2one',
    'foreign_object' => 'todolist\User'
]
```

#### one2many

1-N relation, related to a many2one object

		foreign_object -> 
		foreign_field -> select amongst fields of other class
				+ ability to create a new one (many2many) : 
				temporary creation in one of both ways !
		rel_table -> check amongst existing rel tables 
		({package}_rel_local_foreign or rel_foreign_local)
				if no table yet -> create
		rel_local_key -> {local_class}_id
		rel_foreign_key -> {foreign_class}_id


```php
<?php
// todolist\User
// Each user can have many tasks
'tasks_ids'  => [
    'type'           => 'one2many',
    'foreign_object' => 'todolist\Task',
    'foreign_field'  => 'user_id'
]
```

#### many2many

M-N relation

* foreign_object : allows to select amongst other classes + ability to create symmetrical one (one2many)
```php
<?php
// school\Teacher
'courses_ids' => [
    'type' 			  => 'many2many', 
    'foreign_object'  => 'school\Course', 
    'foreign_field'	  => 'teachers_ids', 
    'rel_table'		  => 'school_rel_course_teacher', 
    'rel_foreign_key' => 'course_id', 
    'rel_local_key'	  => 'teacher_id'
]
```

#### computed


* result_type : select ('boolean', 'integer', 'float', 'string', 'text', 'html' )
*  function: string holding name of a callable method
* store : boolean

Computed fields are not stored in the DB, unless the `store` attribute is set to true.

To get it,  we use the 'function' key, that will point at any function.

 It will return a processed value and afterwards, it can be stored inside the DB.

> Most of the time the use of the store attribute requires that field(s) on which depends the computed value, has an onchange event, triggering the update of the calculated field (see example). 

When trying to load a computed field, 

  * if 'store' is not defined or set to false, it computes the value using the provided method each time the field value is requested
  * if 'store' is set to true and the field isn't in the DB (NULL), it computes the value using the provided method
  * if 'store' is set to true and the field is in the DB, it uses the DB value

```php
<?php
// Example from core\Permission
// ...
    'rights' => [
        'type'		   => 'integer',
        'onupdate'	   => 'onchangeRights'
    ],
    'rights_txt' => [
        'type'		   => 'computed', 
        'store'		   => true, 
        'result_type'  => 'string', 
        'function'	   => 'calcRightsTxt'
    ],
// ...


public static function onupdateRights($om, $ids, $values, $lang) {
    $om->update(__CLASS__, $oids, ['rights_txt' => null, $lang);
}

public static function calcRightsTxt($om, $ids, $lang) {
    $res = array();
    $values = $om->read('core\Permission', $ids, array('rights'), $lang);
    foreach($ids as $oid) {
        $rights_txt = array();
        $rights = $values[$oid]['rights'];
        if($rights & QN_R_CREATE)   $rights_txt[] = 'create';
        if($rights & QN_R_READ)     $rights_txt[] = 'read';
        if($rights & QN_R_WRITE)    $rights_txt[] = 'write';
        if($rights & QN_R_DELETE)   $rights_txt[] = 'delete';
        if($rights & QN_R_MANAGE)   $rights_txt[] = 'manage';
        $res[$oid] = implode(', ', $rights_txt);
    }
    return $res;
}
```





## Fields properties

#### Common properties

| **PROPERTY**    | **DESCRIPTION**                                              |
| --------------- | ------------------------------------------------------------ |
| type        | The type of field:  one of the value listed as <a href="#definition_field_types">fields types</a>. |
| usage       | (string) Specifies additional information about the format of the field (@see [Usages](../architecture-concepts/usages.md) for more details) |
| description | (string) Brief about the field (max 65 chars).              |
| visible     | (optional, boolean \| array) [Domain](../architecture-concepts/domains.md) holding the conditions that must be met in order for the field to be relevant (and shown in UI). |
| default     | (optional) (mixed) Tells how to get the default value of the field. Can be either a value (of the same type than the one target by `type`) or a callable (string). |
| readonly    | (boolean) Marks the field as non-editable (default = false). |
| required    | (boolean) Marks the field as mandatory (trying to store an object without a value for that field raises error) (default = false). |
| multilang   | (boolean) Marks the field as translatable (default = false). |
| onupdate        | (optional, string) Name of the method to invoke when field is updated.<br/>Format: `package\Class::method`<br />Signature : `public static function onupdateFieldName($orm, $oids, $values, $lang) {}` |
| domain  | (only relational fields, array) [Domain](../architecture-concepts/domains.md) holding the additional conditions to apply on the set of objects targeted by the relation. |
| dependencies | (optional, array) list of computed fields (relative to current object or through relations traversal) that must be reset when the value of the field (the property is part of) is updated. |



#### Type-related properties

| **TYPE** | **ATTRIBUTE** |**USAGE**                    |
| - | -|-|
| **string** |  | |
|  | type        | Type of the field. Accepted types are: *alias*, *computed*, *many2one*, *many2many*, *one2many*, *integer*, *string*, *float*, *boolean*, *text*, *date* and *datetime*. |
|              | multilang | (optional, boolean) Tells if field is [translatable](i18n.md) (default = false) |
|              | selection | (optional, array) Pre-defined list or associative array holding the possible values for the field.   <a href="#anchor">Example</a> |
| **alias** |  |  |
|        | alias |(string) Targets another field whose value must be returned when fetching the field value. By default, the `name`field is an alias for the `id` field.|
| **many2one** |  |  |
|     | foreign_object     | Full name of the class toward which field is pointing back. |
|     | ondelete | (string ['null', 'cascade'])Tells how to behave when the parent object is deleted.<br /><em>cascade</em>: delete the children  too.<br /><em>null</em> : void the pointer and keep the children. |
| **one2many** |  |  |
|     | foreign_object     | (string) Class toward which current field is pointing back. |
|     | foreign_field     | (string) Name of the field of the pointed class that is pointing back toward the current class. |
|     | foreign_key     | (optional, string) Name of the field that serves as identifier for objects pointed by the relation (default = 'id') |
|     | order     | (optional) Name of the field pointed objects must be sorted on. |
|     | sort     | (optional, string) direction fort sorting (possible values: 'asc', 'desc') |
|     | ondetach | (string ['null', 'delete']) Tells how to handle the children objects when the relation is removed: either set the pointer to `null` or `delete` the children objects. (default = null) |
| **many2many** |  |  |
|     | foreign_object     | Full name of the class the relation points to. |
|     | foreign_field | Name of the field of the pointed class that is pointing back toward the current class. |
|     | rel_table | Name of the SQL table dedicated to the m2m relation (recommended syntax: `package_rel_class1_class2`) |
|     | rel_local_key | Name of the column in `rel_table` holding the identifier of the current object. |
|     | rel_foreign_key | Name of the column in de `rel_table` holding the identifier of the pointed object. |
| **computed** |  |  |
|  | result_type     | Specifies the type of the result returned by the field function, which can be any of the allowed types. |
|     | store     | (optional) boolean telling if the result must be stored in database (in that case, the related table must contain a column for the field). By default, this attribute is set to **false**. |
|     | function  | String holding the name of the method to invoke for computing the value of the field.<br /> Syntax: `method` (relative to current class) or `package\Class::method` (absolute notation) |
|              | multilang | (optional, boolean) Flag telling if field can be translated (default value: false). This applies only for stored fields. |
| | onrevert | (optional, string) Name of the method to invoke when field is reverted to NULL. |



<a name="anchor">Examples of fields using the `selection` attribute:</a> 

```php 
<?php
'field_a' => [
    'type'      => 'string',
    'selection' => [
        'choice1', 
        'choice2',
        'choice3'
    ]
],
// associative arrays are also allowed for assinging distinct labels on values
'field_b' => [
    'type'      => 'string',
    'selection' => [
        'choice1' => 'First choice', 
        'choice2' => 'Second choice',
        'choice3' => 'Third choice'
    ]
]

```

!!! note "Key-Value mapping"
    In case of an associative array, if keys are numeric values (i.e. something that can be converted to an integer), the map will be handled as an array when converted to JSON. Therefore, it is advised not to use numeric values for keys to avoid the UI mixing up values and labels.

## System fields

Some fields are mandatory, and defined in the `Model` class.

| **NAME** | **TYPE**       | **ROLE**                                                     |
| -------- | -------------- | ------------------------------------------------------------ |
| id       | integer        | Unique identifier of the object.                             |
| name     | string         | Name to use when referring to the object (in views). By default, this field is an alias of `id`. |
| status   | string         | 'draft', 'instance', 'archive'.                              |
| created  | datetime       | Date at which the object was created. |
| creator  | foreign_object | `core\User`                                                  |
| modified | datetime       | Date date on which the object was last modified. |
| modifier | foreign_object | `core\User`                                                  |
| deleted | boolean | Marks the object as soft-deleted.               |

!!! note "About 'name field'"
    Le champ name peut être redéfini comme un alias ou un champ calculé.

### optional system fields

Some fields are reserved but optional (with a convention of use):

* state : this field is used when a workflow applies on an entity.
* alert: this field is used in conjunction with the core\alert entities. If defined, it is expected to be a computed field.

## Getters methods

| **NAME**         | **DESCRIPTION**                                              |
| ---------------- | ------------------------------------------------------------ |
| getName()        | Get Model readable name.                                     |
| getDescription() | Get Model description.                                       |
| getType()        | Provide the list of unique rules (array of combinations of fields). |
| getLink()        | Get the URL associated with the class.                       |
| getColumns()     | Returns the user-defined part of the schema (i.e. fields list with types and other attributes). |
| getConstraints() | Returns a map of constraint items associating fields with validation functions. |
| getUnique()      | Provide the list of unique rules (list of arrays of combinations of fields). |
| getFields()      | Returns all fields names.                                    |
| getValues()      | Returns values of static instance.                           |
| getDefaults()    | Return default values.                                       |
| getTable()       | Return the name of the DB table to be used for storing objects of current class. |
| getWorkflow()    | Returns the workflow associated with the entity. For more details @see [Workflows](../advanced/workflows.md) section. |
| getRoles()       |                                                              |
| getActions()     |                                                              |
| getPolicies()    |                                                              |



## Overridable methods

| **NAME**          | **DESCRIPTION**                                              |
| ----------------- | ------------------------------------------------------------ |
| cancreate()    | Check wether an object can be created. |
| oncreate()    | Hook invoked after object creation for performing object-specific additional operations. |
| canupdate()   | Check wether an object can be updated.                       |
| onupdate()    | Hook invoked before object update for performing object-specific additional operations. |
| candelete()   | Check wether an object can be deleted.                       |
| ondelete()    | Hook invoked before object deletion for performing object-specific additional operations. |
| canclone()    | Check wether an object can be cloned.                        |
| onclone()     | Hook invoked after object cloning for performing object-specific additional operations. |
| onchange()    | Handler for providing consistency in model by reacting to front-end form edition (automatic changes). |


# Custom methods
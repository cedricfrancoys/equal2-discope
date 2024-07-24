# ORM

eQual comes with an object-relational mapper (ORM) that greatly eases the interactions with the database by handling the
data conversions, classes inheritance and objects relations.

The ObjectManager service is dedicated to Object-Relational Mapping. It handles all tasks that relate to objects search
and manipulations and offers an abstract layer for DBMS queries.

The ORM can be used in order to :

- **create**, **update** or **delete** one or more objects (based on the ID field)
- **retrieve** a single entity or a list of entities (both based on the ID field)
- **retrieve** a list of IDs of entities that match some criteria

ObjectManager methods are for low-level manipulations (no data conversion nor validation, and no user permission check
at this level). These are mostly used in methods of classes definition.

Controllers mostly require high-level manipulations (including data conversion, validation and permission checks) and
therefore use Collections returned by class autoloader.

## Object Definition

Each object definition inherits from the Model Class that defines methods common to all objects.

The 3 methods that should be present in all object classes are presented below (for a full list of the Model class
available methods, see @):

* `getName()`
* `getDescription()`
* `getColumns()`

Model fields are defined using the `getColumns ()` method, which returns an associative array that maps the fields names
with their definitions.

```php
<?php
public static function getColumns() {
	return [];
}
```

## ObjectManager methods

Objects manipulations are made on selections of objects, described as an array of identifiers.

All methods can either return an array (in case of success), or an integer (in case of error, the integer is an error
code).

### read

Fetches specified field values for the selected objects.

#### Description

```php
<?php
mixed read( string $class [, int[] $ids=null, string[] $fields=null, 
string $lang=DEFAULT_LANG] )
```

#### Parameters

| **PARAMETER** | **DESCRIPTION**                                              |
| ------------- | ------------------------------------------------------------ |
| class         | Class of the objects we want to retrieve.                    |
| ids           | List of identifiers of the objects we want to retrieve.      |
| fields        | Array holding the names of the fields we want to retrieve .  |
| lang          | Language under which return fields values (only relevant for multilang fields). |

#### Returned value

Returns an associative array containing, for every object id, a sub array mapping each field to its value.  
Returns an integer (error code) if an error occurred.

### update

Sets new values for one or more object instances.

!!! note "multilang fields"
While saving in a specific language, no test is done to check that specified fields are defined as multilang (it means
that saving non-multilang fields in a non-default language will result in a loss of data).

#### Description

```php
<?php
mixed update( string $object_class, int[] $ids [, array[] $values=null, 
string $lang=DEFAULT_LANG, boolean $create=false] )
```

#### Parameters

| **PARAMETER** | **DESCRIPTION**                                              |
| ------------- | ------------------------------------------------------------ |
| object_class  | Class of the objects we want to update.                      |
| ids           | Ids of the objects to update.                                |
| fields        | Array mapping fields names with their new values .           |
| lang          | Language to wich apply the changes (affects only multilang fields). |
| session_id          | Identifier of the session holding user data (by default, the current session). |

#### Returned value

Returns an array containing ids of newly created objects (if any).  
Returns an integer (error code) if an error occurred.

### delete

Deletes an object permanently or puts it in the "trash bin" (i.e. setting the 'deleted' flag to 1).

#### description

```php
<?php
mixed remove( string $class, array $ids [, boolean $permanent=false] )
```

#### Parameters

* **class** : class of the objects to delete
* **ids** : ids of the objects to delete
* **permanent** : flag for marking deletion as soft-deletion (default) or hard-deletion. In the latter case, the record
  is removed from DB.

#### Returned value

Returns an associative array containing ids of the objects actually deleted.  
Returns an integer (error code) if an error occurred.


### search

Search for objects matching the domain criteria.

#### Description

```php
<?php
mixed search( string $class [, array $domain=null, string $order='id', string 
$sort='asc', string $start='0', string $limit='0', string $lang=DEFAULT_LANG] )
```

#### Parameters

| **PARAMETER** | **DESCRIPTION**                                              |
| ------------- | ------------------------------------------------------------ |
| class         | Class of the objects we want to look for.                    |
| domain        | Search criteria that objects have to match.                  |
| order         | Field on which the resulting list must be sorted .           |
| sort          | Sorting order.                                               |
| start         | Position in the global resulting list from which we want the ids. |
| limit         | Amount of ids to return.                                     |
| lang          | Language under which search applies (only relevant for multilang fields). |

#### Returned value

Returns an array of objects ids.  
Returns an integer (error code) if an error occurred.

### validate

Checks whether the values of a given object fields are consistent with the related model definition.

#### Description

```php
<?php
mixed validate(string $class, int[] $ids, array[] $values, 
boolean $check_unique=false, boolean $check_required=false)
```

#### Parameters

| **PARAMETER** | **DESCRIPTION**                                       |
| ------------- | ----------------------------------------------------- |
| class         | Class of the object we want to validate.              |
| values        | Associative array containing fields and their values. |

#### Returned value

Returns an associative array containing invalid fields with their associated error_message_id (thus an empty array means
no invalid fields).  
Returns an integer (error code) if an error occurred. 

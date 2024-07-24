# Manipulating entities

To get an overview on CREATE, READ, UPDATE, DELETE commands, see [*Building a REST API*](../howtos-and-examples/rest-api.md).

The following examples show the minimal syntax required to operate CRUD manipulations:

!!! note "multilang fields"
	For fields whose value varies from one language to another (multilang), an additional `$lang` parameter is used. See [i18n](i18n.md) for more information.

## CRUD operations

CRUD operations are part of the ORM foundations and are handled by the ObjectManager service.

All methods return either an integer or an array (when relevant).

By convention, in case of error, a negative integer is returned.

### Supported errors codes

!!! note "error codes"
    All codes are prefixed with `QN_ERROR_` (not show in the table to avoid redundancy). So, for instance,  `UNKNOWN` will appear as `QN_ERROR_UNKNOWN` in code.

| **NAME**    | **VALUE** | **DESCRIPTION** |
| ----------- | ----- | --------- |
|`UNKNOWN`|-1| Something went wrong in an unexpected way. |
|`MISSING_PARAM`|-2| One or more mandatory parameters are missing. |
|`INVALID_PARAM`|-4| One or more parameters have invalid or incompatible value. |
|`SQL`|-8| An error occurred while building SQL query or processing it. |
|`UNKNOWN_OBJECT`|-16| The request targets an unknown resource (class, object, view, ...). |
|`NOT_ALLOWED`|-32| Action violates some rule (including UPLOAD_MAX_FILE_SIZE for binary fields) or user don't have required permissions. |
|`LOCKED_OBJECT`|-64| Object is currently locked by another process. |
|`CONFLICT_OBJECT`|-128| Version conflict. |
|`INVALID_USER`|-256| Authentication failure. |
|`UNKNOWN_SERVICE`|-512| Server error : missing service. |
|`INVALID_CONFIG`|-1024| Server error : faulty configuration. |



### Create

```php
<?php
/**
 * Creates a new instance of given class and, if given, assigns values to targeted fields.
 *
 * @param  string       $entity       Class of the object to create.
 * @param  array        $fields       Associative array mapping each field to its assigned value.
 * @param  string       $lang         Language in which to store multilang fields.
 * @param  boolean      $use_draft    If set to false, disables the re-use of outdated drafts.
 * @return integer      Identfier of the newly created object or, in case of error, the code of the error that was raised.
 */
function create($entity, $fields, $lang=null, $use_draft=true)
    
```



### Read

```php
<?php
/**
 * Reads a collection of objects from a given class, based on a list of identfiers.
 *
 * @param   string     $class       Class of the objects to retrieve.
 * @param   mixed      $ids         Identifier(s) of the object(s) to retrieve (accepted types: array, integer, string).
 * @param   mixed      $fields      Name(s) of the field(s) to retrieve (accepted types: array, string).
 * @param   string     $lang        Language under which return fields values (only relevant for multilang fields).
 * @return  int|array  Resulting associative array mapping ids with objects, or error identifier.
 */
function read($entity, $ids, $fields, $lang=null)
```



### Update

```php
<?php
/** 
 * Updates specified fields of seleced objects and stores changes into database.
 *
 * @param   string    $class        Class of the objects to write.
 * @param   mixed     $ids          Identifier(s) of the object(s) to update (accepted types: array, integer, numeric string).
 * @param   mixed     $fields       Array mapping fields names with the value (PHP) to which they must be set.
 * @param   string    $lang         Language under which fields have to be stored (only relevant for multilang fields).
 * @return  int|array Returns an array of updated ids, or an error identifier in case an error occured.
*/
function update($class, $ids, $fields, $lang=null)

```



### Delete

```php
<?php
/**
 * Deletes an object permanently or put it in the "trash bin" (i.e. setting the 'deleted' flag to 1).
 *
 * @param   string  $class          Class of the object to delete.
 * @param   array   $ids            Array of ids of the objects to delete.
 * @param   boolean $permanent      Flag for soft deleted (marked as deleted) or hard deletion (removed from DB).
 *
 * @return  integer|array   Returns a list of ids of deleted objects, or an error identifier in case an error occured.
 */
function delete($entity, $ids, $permanent=false)
```



## Querying entities

In addition to classic CRUD operations, eQual comes with a  `search()` method dedicated to querying entities.

### Search method

```php
<?php
/**
 * Searches for the objects that comply with the domain (series of criteria).
 *
 * @param   string     $class       Class of the objects to search for.
 * @param   array      $domain      Domain (disjunction of conjunctions) defining the criteria the objects have to match.
 * @param   array      $sort        Associative array mapping fields and orders on which result have to be sorted.
 * @param   integer    $start       The offset at which to start the segment of the list of matching objects.
 * @param   string     $limit       The maximum number of results/identifiers to return.
 *
 * @return  integer|array   Returns an array of matching objects ids.
 */
function search($class, $domain=null, $sort=['id' => 'asc'], $start='0', $limit='0', $lang=null) {
```



### Collections 

eQual implements Collections through `Collection` objects that are instantiated using entities (model).

Collections give the ability to apply chained actions on multiple objects at a time while checking the permission of the current user (returned by the `Authentication` service).



Example :


```php
<?php
use core\User;

User::search(['firstname', 'ilike', '%sam%'])
    ->from(0)
    ->limit(5)
    ->get();
```



Retrieving a collection is through API calls is available using the standard controller `core_model_collect`.

The purpose of the "collect" operation is to perform a `search()` and a `read()` in a single call.



#### Querying sub-objects

##### a) dot notation

```
$ ./equal.run \
--get=model_collect \
--entity=core\\User \
--fields=[id,name,groups_ids.name,groups_ids.description]
```

##### b) array notation
```
$ ./equal.run \
--get=model_collect \
--entity=core\\User \
--fields="{id,name,groups_ids:{name,description}}" 
```


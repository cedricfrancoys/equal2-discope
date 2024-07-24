# Built-in utilities and stand-alone scripts

Some tools are included in the `core` package to ease management and development of packages.


Utility scripts allow to achieve tasks that are not handled by the ORM.

These act like some sort of plugins, written in PHP and located in the folder: `packages/core/data` and `packages/core/actions`.

This page lists an inventory of available scripts, grouped by category.



!!! note "Calling the scripts"
    Calls shown in examples must be made at the root folder of eQual installation (where the `run.php` script is located).



## Installation & Config utilities


#### `db-connectivity`

| **PATH**        | `core\actions\test\db-connectivity.php`                      |
| --------------- | ------------------------------------------------------------ |
| **URL**         | `?do=test_db-connectivity`                                   |
| **CLI**         | `$ ./equal.run --do=test_db-connectivity`                    |
| **DESCRIPTION** | Tests connectivity to the DBMS server (check if we're able to establish a TCP/IP connection). |



#### `db-access`

| **PATH**        | `core\actions\test\db-access.php`                            |
| --------------- | ------------------------------------------------------------ |
| **URL**         | `?do=test_db-access`                                         |
| **CLI**         | `$ ./equal.run --do=test_db-access`                          |
| **DESCRIPTION** | Tests access to the database specified in the config file. This controller uses db-connectivity before trying to access the database. |



#### `fs-consistency`

| **PATH**        | `core\actions\test\fs-consistency.php`                       |
| --------------- | ------------------------------------------------------------ |
| **URL**         | `?do=test_fs-consistency`                                    |
| **CLI**         | `$ ./equal.run --do=test_fs-consistency`                     |
| **DESCRIPTION** | Checks current installation directories integrity. The controller checks if all mandatory directories are present, and if their permissions allow the apache process to read/write as required.|



#### `init_db`

|**PATH**|`core\actions\init\db.php`|
|-|-|
|**URL**|`?do=init_db`|
|**CLI**|`$ ./equal.run --do=init_db`|
|**DESCRIPTION**|Creates a database using the details provided in config file. This controllers calls db-connectivity and if connection can be established with the host, it requests the creation of the database, if it does not exist yet.|



## Package utilities


#### `init_package`

|**PATH**|`core\actions\init\package.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?do=init_package&package=core`|
|**CLI**|`$ ./equal.run --do=init_package --package=core`|
|**DESCRIPTION**|Initialize database for given package. If no package is given, initialize core package. Compile the apps (`apps folder`) of the package and copy them in the public folder.|




#### `package-consistency`

|**PATH**|`core\actions\test\package-consistency.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?do=test_package-consistency&package=core`|
|**CLI**|`$ ./equal.run --do=test_package-consistency --package=core --level=warn`|
|**DESCRIPTION**|Performs consistency checks between DB and class as well as syntax validation for classes (PHP), views and translation files (JSON).|

 


> The level property has 3 options : 
>
> - **'error'** (ex: `missing property 'entity' in file:  "packages\/lodging\/views\/sale\booking\InvoiceLine.form.default.json"`);
> - **'warn'** (ex: `WARN  - I18 - Unknown field 'object_class' referenced in file "packages\/core\/i18n\/en\/alert\MessageModel.json")`;
> - **\*** (error & warn).






#### `test_package`

|**PATH**|`core\actions\test\package.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?do=test_package&package=core`|
|**CLI**|`$ ./equal.run --do=test_package --package=core`|
|**DESCRIPTION**|This controller checks the presence of test units for a given package and runs them, if any. (page :['Testing'](./testing.md)).|




## Data manipulation utilities

### Models meta data

#### `model_schema`

|**PATH**|`core\data\model\schema.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?get=model_schema&entity=core\User`|
|**CLI**|`$ ./equal.run --get=model_schema --entity=core\\User`|
|**DESCRIPTION**|Returns the schema of given class (model) in JSON.|




#### `model_view`

|**PATH**|`core\data\model\view.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?get=model_view&entity=core\User&view_id=list.default`|
|**CLI**|`$ ./equal.run --get=model_view --entity=core\\User --view_id=list.default`|
|**DESCRIPTION**|Returns the JSON view related to an entity (class model), given a view ID.|




### Manipulate data

#### `model_create`

|**PATH**|`core\actions\model\create.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?do=model_create&entity=core\Group&fields[name]=Admin`|
|**CLI**|`$ ./equal.run --do=model_create --entity=core\\Group --fields[name]=Admin`|
|**DESCRIPTION**|Create a new object using given fields values.|




#### `model_update`

|**PATH**|`core\actions\model\update.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?do=model_update&entity=core\Group&id=45&fields[name]=Worker`|
|**CLI**|`$ ./equal.run --get=model_view --entity=core\\User --id=45 --fields[name]=Worker`|
|**DESCRIPTION**|Update (fully or partially) the given object.|




#### `model_delete`

|**PATH**|`core\actions\model\delete.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?do=model_delete&entity=core\Group&id=48`|
|**CLI**|`$ ./equal.run --get=model_delete --entity=core\\Group --id=48`|
|**DESCRIPTION**|Deletes the given object(s).|



#### `model_search`

|**PATH**|`core\data\model\search.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?get=model_search&entity=core\Group&domain=[name,=,Admin]`|
|**CLI**|`$ ./equal.run --get=model_search --entity=core\\Group --domain=[name,=,Admin]`|
|**DESCRIPTION**|Returns a list of identifiers of a given entity, according to given domain (filter), start offset, limit and order.|

  

#### `model_read`

|**PATH**|`core\data\model\read.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?get=model_read&entity=core\Group&fields=[created,description]&ids=[1,2]`|
|**CLI**|`$ ./equal.run --get=model_read --entity=core\\Group --fields=[created,description] --ids=[1,2]`|
|**DESCRIPTION**|Returns values map of the specified fields for object matching given class and identifier.|



#### `model_collect`

|**PATH**|`core\data\model\collect.php`|
| --------------- | ------------------------------------------------------------ |
|**URL**|`?get=model_collect&entity=core\Group&fields=[created,description]&domain=[id,=,1]`|
|**CLI**|`$ ./equal.run --get=model_collect --entity=core\\Group --fields=[created,description] --domain=[id,=,1]`|
|**DESCRIPTION**|Returns a list of entities according to given domain (filter), start offset, limit and order.|




## Rights management utilities

To determine Groups and Users permissions, we use a list of rights : `create`, `read`, `update`, `delete`, `manage`

They are also defined inside the `eq.lib.php`file with a value attached :

```php
<?php
/**
 * Users & Groups permissions masks (value attached to the mask) 
 * #memo - we use powers of 2 for permission mask so that the addition of 
 numbers never leads to a colliding values
 */
    define('QN_R_CREATE',    1);
    define('QN_R_READ',      2);
    define('QN_R_WRITE',     4);
    define('QN_R_DELETE',    8);
    define('QN_R_MANAGE',   16);
```

**Use** :

Those values are used, for example, inside `Permission.class.php` on the `rights` field, to determine which rights from the list are selected. 

```php
<?php
'rights' => [
    'type' 	            => 'integer',
    'onupdate'          => 'onupdateRights',
    'description'       => "Rights binary mask (1: CREATE, 2: READ, 4: WRITE,
    8 DELETE, 16: MANAGE)"
],
```

Those permissions are used as properties by `User.class.php` & `Group.class.php` to determine the rights available.

### Users

#### `user_grant`

| **PATH**        | `core\actions\user\grant.php`                                |
| --------------- | ------------------------------------------------------------ |
| **URL**         | `?do=user_grant&right=create&user=cedric@equal.run`          |
| **CLI**         | `$ ./equal.run --do=user_grant --right=create --user=cedric@equal.run --entity=core\\Task` |
| **DESCRIPTION** | Grant additional privilege to given user.                    |

> Only one right can be granted at a time to one user over one entity.




#### `user_revoke`

| **PATH**        | `core\actions\user\revoke.php`                               |
| --------------- | ------------------------------------------------------------ |
| **URL**         | `?do=user_revoke&right=create&user=cedric@equal.run&entity=core\Task` |
| **CLI**         | `$ ./equal.run --do=user_revoke --right=create --user=cedric@equal.run --entity=core\\Task` |
| **DESCRIPTION** | Revoke privilege from a given user.                          |

> Only one right can be revoked at a time to one user over one entity.




### Groups

#### `group_grant`

| **PATH**        | `core\actions\group\grant.php`                               |
| --------------- | ------------------------------------------------------------ |
| **URL**         | `?do=group_grant&right=create&group=users&entity=core\Task`  |
| **CLI**         | `$ ./equal.run --do=group_grant --right=create --group=users --entity=core\\Task` |
| **DESCRIPTION** | Grant additional privilege to given group.                   |

> Only one right can be granted at a time to one group over one entity.




#### `group_revoke`

| **PATH**        | `core\actions\group\revoke.php`                              |
| --------------- | ------------------------------------------------------------ |
| **URL**         | `?do=group_revoke&right=create&group=users&entity=core\Task` |
| **CLI**         | `$ ./equal.run --do=group_revoke --right=create --group=users --entity=core\\Task` |
| **DESCRIPTION** | Revoke privilege from a given group.                         |

> Only one right can be revoked at a time to one group over one entity.



#### `group_add-user`

| **PATH**        | `core\actions\group\add-user.php`                            |
| --------------- | ------------------------------------------------------------ |
| **URL**         | `?do=group_add-user&group=users&user=cedric@equal.run`       |
| **CLI**         | `$ ./equal.run --do=group_add-user --group=users --user=cedric@equal.run` |
| **DESCRIPTION** | Add a user as member of a given group.                       |




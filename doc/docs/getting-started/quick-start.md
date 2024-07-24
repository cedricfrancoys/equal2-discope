# Quick Start

You've just installed eQual. And now what ?

## Init your environment 

You can now test your installation by calling the `test_db-connectivity` test tool :

| **PATH**        | `core\actions\test\db-connectivity.php`  |
| --------------- | ---------------------------------------- |
| **URL**         | `?do=test_db-connectivity`               |
| **CLI**         | `$ ./equal.run --do=test_db-connectivity` |
| **DESCRIPTION** | Tests connectivity to the DBMS server (check if we're able to establish a TCP/IP connection). |

If no error message is returned (the command ends with a `0` exit code), you can create your database by calling the `init_db` tool :

| **PATH**        | `core\actions\init\db.php`               |
| --------------- | ---------------------------------------- |
| **URL**         | `?do=init_db`                            |
| **CLI**         | `$ ./equal.run --do=init_db`             |
| **DESCRIPTION** | Creates a database using the details provided in config file. This controllers calls db-connectivity and if connection can be established with the host, it requests the creation of the database, if it does not exist yet. |

eQual holds a native `core` package that holds a few classes and operations. All packages depend on the ORM layer, which is responsible of storing the objects into the database. So, in order to start using a package that defines object classes, you have to initialize it. 

This can be done using the `init_package` tool :

| **PATH**        | `core\actions\init\package.php`          |
| --------------- | ---------------------------------------- |
| **URL**         | `?do=init_package&package=core`          |
| **CLI**         | `$ ./equal.run --do=init_package --package=core` |
| **DESCRIPTION** | Initialize database for given package. If no package is given, initialize core package. Compile the apps (`apps folder`) of the package and copy them in the public folder. |




## Create your first package

This section covers the first steps to setup a backend service or API using eQual.

### 1. Create a new package

In `/packages`, create a **new folder**. For this example, we'll name it *"mypackage"*.
In `/packages/mypackage`, create 2 new folders named `classes` and `data`.

Directory should look like this :  

```
/packages
    /mypackage
        /classes
        /data
```


### 2. Define some custom classes

In `/packages/mypackage/classes/`, add a new **.class.php** file for each class we want to use. 
In this example, we define the class **Task** for a todo-list app :

`/packages/mypackage/classes/Task.class.php`

```php
<?php  
namespace mypackage;
use equal\orm\Model;
    
class Task extends Model {
    public static function getColumns() {
        return [
            'title'     => ['type' => 'string'],
            'content'   => ['type' => 'text']
        ];
    }
}
```

*Note: ID key generation is handled by eQual*

But, what if we want to establish a relation between two classes, like nesting the **User** of `User.class.php` as a parameter?

That's where the types **many2many**, **one2many**, and **many2one** come in handy (*see [Understanding DBMS relationships](https://afteracademy.com/blog/what-are-the-different-types-of-relationships-in-dbms)*).

```php
<?php
// Task
// ...
      return [
        // ...
        'user_id'	=> [
            'type'           => 'many2one', 
            'foreign_object' => 'mypackage\User'
        ]
      ];
```

We also need to do the opposite in `User.class.php` :

```php
<?php
// User
// ...
      return [
        // ...
        'task_ids'	=> [
            'type'           => 'one2many', 
            'foreign_object' => 'mypackage\Task',
          	'foreign_field' => 'user_id'
        ]
      ];
```



### 3. Initialize the new package

For this step, we use mySQL with a dedicated database (See [Configuration](configuration.md)).

First we **initiate the core component**; this is required every time we want to use eQual in a new DB.

Open your CLI at the root of eQual's folder and use this :

```bash
$ ./equal.run --do=init_package --package=core
```

Then we do the same for our package. It will automatically create **one table per associated class**.

```bash
$ ./equal.run --do=init_package --package=mypackage
```

Now the database should have the following tables:  

- `core_user`  

* `core_group`  
* `core_rel_group_user`  
* `core_log`  
* `core_permission`  
* `core_translation`  
* `core_version`  
* `mypackage_task`  
* `mypackage_user`  



#### Consistency checks

When writing new classes, you can take advantage of the consistency controllers in order to check the validity of the files just created.

The checks cover the schema consistency as well as syntax validity for classes (PHP), views and translation files (JSON).

In case something is wrong or missing, an error or a warning  is emitted.

You can run the same command with your package's name instead of "core", see if the problem lies in here.

| **PATH**        | `core\actions\test\package-consistency.php` |
| --------------- | ---------------------------------------- |
| **URL**         | `?do=test_package-consistency&package=mypackage` |
| **CLI**         | `$ ./equal.run --do=test_package-consistency --package=mypackage` |
| **DESCRIPTION** | Consistency checks between DB and class as well as syntax validation for classes (PHP), views and translation files (JSON). |



### 4. Grant CRUD permissions

By default you don't have any rights to read, write or delete in the DB. This is for security reasons.

If you want to bypass this you have 2 options :

- Use the CLI to grant the permissions

- Debug mode (for testing purpose)

### CLI commands

When using CLI commands, no permission checks are made and all requests are handled in super-suer mode. It is thus the preferred way to grant and revoke rights.

The rights available are **create**, **read**, **update**, **delete**, **manage**.

For instance, we'll continue with our todolist example and grant the permission to **read** for the group of objects **Task** :

```bash
$ ./equal.run --do=group_grant --group=default --right=read --entity=mypackage\Task
```

**You can only grant one right at a time**, it means we'll need to repeat this command for every permission we want to give (i.e. create, read, write, delete, manage).

If you want to target **all the classes** of a package, you can specify with ``` --entity=mypackage\* ```.

### Debug mode

Alternatively, you can grant all rights to all users (for testing purpose): 

In the `/config` folder, open or create the `config.json` file and add or adapt the "DEFAULT_RIGHTS" value.

Replace this :
```json
    "DEFAULT_RIGHTS": 0
```
By this :
```json
    "DEFAULT_RIGHTS": "QN_R_CREATE | QN_R_READ | QN_R_DELETE | QN_R_WRITE"
```



Note: eQual uses binary masks for granting rights.

- 0 means **no rights** 
- 1 is **create** 
- 2 is **read** 
- 4 is **write/update**
- 8 is **delete**
- 16 is **manage**



## More learning ressources

See [*Usage*](../usage/directory-structure.md) and [*Howtos*](../howtos-and-examples/generic-cheat-sheet.md)


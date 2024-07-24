# Collections

Collections are widely used throughout the framework: they ease manipulations of entities (objects) and allow to execute a same operation on several objects in a single call.

In eQual, a collection is a series of partial objects (the ORM loads only requested fields) on which one or more [CRUD operations](../usage/crud.md) can be applied.

When a collection is given as an array, it consists of a associative array mapping objects ids with their related (partial) value (which is also an array).

Example:

```php
<?php
[
    5 => [ 'id' => 5, 'name' => 'Huey' ],
    6 => [ 'id' => 6, 'name' => 'Dewey' ],
    7 => [ 'id' => 7, 'name' => 'Louie' ]
]
```
## Manipulating collections

eQual uses both ObjectManager service and Collection objects for manipulating entities.

Methods from the ObjectManager can be considered as "system" calls while Collections offer an additional layer to make easier dealing with user permissions, objects consistency and data adaptation.

Methods defined in Model classes (i.e. classes that inherit from `equal\orm\Model`) are always invoked by the ObjectManager service, and their signature holds a reference to the service itself. 



On the other hand, in order to provide Collections eQual also uses PHP's `__callstatic` magic method on Model classes with special (reserved) methods dedicated to instanciating new Collections :

* `ids()`
* `create()`
* `search()`



To illustrate this, here below are 2 pieces of code that provide an equivalent result. 


```php
<?php
use equal\orm\ObjectManager;
$orm = ObjectManager::getInstance();
$users = $orm->read('core\User', [5,6,7], ['id', 'name']);
```


```php
<?php
use core\User;
$users = User::ids([5,6,7])->read(['id', 'name'])->get();
```

However, the first snippet results in calling directly the `read()` method of the ObjectManager, which will be directly translated into a SQL query to fetch the requested fields. 

While the second snippet also checks the current user (i.e. the user from who's the request is originating, if known) and make sure that s·he has enough rights to perform the operation.

Also, in case of error, in the first example, $users will hold an error code, while in the second example, an Exception will be thrown and an HTTP response with an error status will be returned.




Using Collections allows method chaining which makes code in controllers shorter and easier to read.

Methods all have a similar return signature 


||**INSTANCE**|**RETURN VALUE**|**ERROR/EXCEPTION**|
|--|--|--|--|
|ObjectManager|dependency injection|Array|In case of error, an error is returned (and a message is sent to the Reporter). ObjectManager methods don't raise Exceptions.|
|Collections|magic method|Collection|Methods raises exceptions: the process is interrupted and the exception is converted to an HTTP error.|

Both mecanism offer the following methods : 

* `create()`
* `read()`
* `update()`
* `delete()`
* `search()`


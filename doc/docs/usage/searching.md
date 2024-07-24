# Searching

Searching is handled by the ORM, which offers a `search()` method, and can be performed either through a Collection or using the ObjectManager service. 

The `search` method returns a list of objects identifiers of the targeted class.

* that list of identifiers can then be used for reading values of the filtered objects;
* additional parameters (order, sort, start, limit) allow to refine the search behavior.

In order to search amongst existing objects, the `search` method  uses a  `domain` argument describing the search criteria.



!!! note "About domains"
    To understand or learn more about domains, please refer to the  [`domain`](../architecture-concepts/domains.md)  section.

![searching](../assets/img/searching.drawio.png)



### ORM search

The ObjectManager service offers a dedicated method for searching amongst Objects. 

#### signature

```php
<?php
/*
 * @param   string     $class       Class of the objects to search for.
 * @param   array      $domain      Domain (disjunction of conjunctions) defining the criteria the objects have to match.
 * @param   array      $sort        Associative array mapping fields and orders on which result have to be sorted.
 * @param   integer    $start       The offset at which to start the segment of the list of matching objects.
 * @param   string     $limit       The maximum number of results/identifiers to return.
 *
 * @return  integer|array   Returns an array of matching objects ids, or a negative integer in case of error.
 */
public function search($class, $domain=NULL, $sort=['id' => 'asc'], $start='0', $limit='0', $lang=DEFAULT_LANG)
```



#### usage

Example:

```php
<?php
$res = $orm->search('core\User', ['login', '=', $login]);
```



### Collection search

In controllers, searching can be invoked either by calling the ObjectManager service or through a collection. 

#### signature

```php
<?php
public function search(array $domain=[], array $params=[], $lang=DEFAULT_LANG)
```



#### usage
Example:

```php
<?php
use core\User;

$collection = User::search([
        ['login', 'like', '%john%'],
        ['validated', '=', 'true']				
    ]);
```

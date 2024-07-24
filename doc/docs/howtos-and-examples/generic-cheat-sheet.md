# Generic cheat-sheet

This section presents common questions along with some relevant examples.

## eQual Apps

### Console

```
http://equal.local/console.php
```

### Workbench

```
http://equal.local/workbench
```

## CLI commands

Should be located at the root of eQual (folder containing file `run.php`)

#### Grant DB rights

Available rights:

- create
- read
- update
- delete
- manage

You can grant one right for one entity at a time:

```bash
./equal.run --do=group_grant --group=default --right=update --entity="core\User"
```

#### Test package consistency

This controller runs some consistency checks and works with any package:

| **PATH**        | `core\actions\test\package-consistency.php`                                                                                 |
|-----------------|-----------------------------------------------------------------------------------------------------------------------------|
| **URL**         | `?do=test_package-consistency&package=core`                                                                                 |
| **CLI**         | `$ ./equal.run --do=test_package-consistency --package=core --level=warn`                                                   |
| **DESCRIPTION** | Consistency checks between DB and class as well as syntax validation for classes (PHP), views and translation files (JSON). |

> The level property has 3 options :
>
> - **'error'** (
    ex: `missing property 'entity' in file:  "packages\/lodging\/views\/sale\booking\InvoiceLine.form.default.json"`)
> - **'warn'** (
    ex: `WARN - I18 - Unknown field 'object_class' referenced in file "packages\/core\/i18n\/en\/alert\MessageModel.json"`
> - **\*** (error & warn).

#### Initiate eQual core in DB

| **PATH**        | `core\actions\init\package.php`                                                         |
|-----------------|-----------------------------------------------------------------------------------------|
| **URL**         | `?do=init_package&package=core`                                                         |
| **CLI**         | `$ ./equal.run --do=init_package --package=core`                                        |
| **DESCRIPTION** | Initialise database for given package. If no package is given, initialize core package. |

> (this step is mandatory for every new installation)

#### Initiate your package in DB

| **PATH**        | `core\actions\init\package.php`                                                         |
|-----------------|-----------------------------------------------------------------------------------------|
| **URL**         | `?do=init_package&package=myPackage`                                                    |
| **CLI**         | `$ ./equal.run --do=init_package --package=myPackage`                                   |
| **DESCRIPTION** | Initialise database for given package. If no package is given, initialize core package. |

#### Initiate your package with initial data in DB

| **PATH**        | `core\actions\init\package.php`                                                         |
|-----------------|-----------------------------------------------------------------------------------------|
| **URL**         | `?do=init_package&package=myPackage&import=true`                                        |
| **CLI**         | `$ ./equal.run --do=init_package --package=myPackage --import=true`                     |
| **DESCRIPTION** | Initialise database for given package. If no package is given, initialize core package. |

#### Run package test unit

| **PATH**        | `core\actions\test\package.php`                                                                                                       |
|-----------------|---------------------------------------------------------------------------------------------------------------------------------------|
| **URL**         | `?do=test_package&package=core`                                                                                                       |
| **CLI**         | `$ ./equal.run --do=test_package --package=core`                                                                                      |
| **DESCRIPTION** | The controller checks the presence of test units for a given package and runs them, if any. (page :['Testing'](../usage/testing.md)). |

## Invoking Controllers

### Data provider

| **PATH** | `/packages/mypackage/data/my-controller.php`                     |
|----------|------------------------------------------------------------------|
| **URL**  | `?get=mypackage_my-controller`                                   |
| **CLI**  | `$ ./equal.run --get=model_collect --entity="mypackage\MyClass"` |
| **PHP**  | ```run('get', 'mypackage_MyClass')```                            |

*Collect is the name of the controller, and model the directory to which it belongs.*

### Action handler

| **PATH** | `/packages/mypackage/actions/subdir/my-action.php`                                                     |
|----------|--------------------------------------------------------------------------------------------------------|
| **URL**  | `?do=mypackage_subdir_my-action`                                                                       |
| **CLI**  | `$ ./equal.run --do=model_update --entity=mypackage\MyObject --fields=[ids]=1 --fields=[name]=example` |
| **PHP**  | ```run('do', 'mypackage_myobject_action', [/* parameters */])```                                       |

## Howtos

### How to create a new object?

```php
<?php
use core\User;

User::create(['firstname'=>'Bart']);
// which is equivalent to
User::create()->update(['firstname'=>'Bart']);
```

> Note: When creating an object, by default, the `state` fields is assigned to 'instance', unless another value is given
> as parameter (ex. state=draft)

### How to check if a given object does exist?

```php
<?php
// count the number of items returned by the search method
if(count(search($object_class, array(array(array('id', '=', $object_id)))))) {...}
```

### How to browse all objects of a given class?

```php
<?php
// note: ensure the specified class does actually exist
$res = browse($object_class, search($object_class));
```

### How to add a clause to every condition?

```php
<?php
// example: add the (deleted = 1) clause to every condition
for($i = 0, $j = count($domain); $i < $j; ++$i)
	$domain[$i] = array_merge($domain[$i], array(array('deleted', '=', '1')));
```

### How to obtain output (json/html) from another script ?

```php
<?php
// There are 2 possibilities :

// either use a HTTP request
load_class('utils/HttpRequest');
$request = new HttpRequest(FClib::get_url(true, false).
'?get=core_objects_list&object_class=School%5CTeacher&rp=20&page=1&sortname
=id&sortorder=asc&domain%5B0%5D%5B0%5D%5B%5D=courses_ids&domain%5B0%5D%5B
0%5D%5B%5D=contains&domain%5B0%5D%5B0%5D%5B2%5D%5B%5D=1&fields%5B%5D=id&
fields%5B%5D=firstname&fields%5B%5D=lastname');
$json_data = $request->get();

// or use PHP output buffering (to prevent scope collision, remember to 
// embed such code into a function)
function get_include_contents($filename) {
	ob_start();	
	include($filename); // assuming  parameters required by the script 
    // being called are present in the current URL 
	return ob_get_clean();
}
$result = get_include_contents('packages/core/data/objects/list.php');
```

### How to sort the result of the browse method (without calling search method)?

```php
<?php
// $order is an array containing fields names on which we want the result 
// set sorted 
// $result is an array returned by a call to the browse method

foreach($order as $ofield) {
	uasort($result, function ($a, $b) use($ofield){
		if ($a[$ofield] == $b[$ofield]) return 0;
		return ($a[$ofield] < $b[$ofield]) ? -1 : 1;
	});
}
```

### How to request fields from all sub-objects at once?

```php
<?php
$pages_values = $orm->read('icway\Page', $pages_ids, array('url_resolver_id'), 
$lang);			
$url_ids = array_map(function($a){return $a['url_resolver_id'];}, $pages_values);
$url_values = $orm->read('core\UrlResolver', $url_ids, ['human_readable_url']);
```

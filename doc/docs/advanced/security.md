# Rights management

The 'Permission' (`packages/core/classes/Permission.class.php`) class is dedicated to the rights management: for each object class (including the 'Permission' class itself), rights can be assigned to each existing group.

## Users 
The structure is defined inside the `core\User` class (`packages/core/classes/User.class.php`).

Each User object holds a list of groups to which it belongs.

!!! Note
    All users belong to the group "users", which is the default group (see `DEFAULT_GROUP_ID` in `/eq.lib.php`).

```php
<?php
public static function getColumns() {
	return [
		'firstname'		=> ['type' => 'string'],        
		'lastname'		=> ['type' => 'string'],
		'login'			=> ['type' => 'string', 'label' => 'Username'],
		'password'		=> ['type' => 'string', 'label' => 'Password'],
		'language'		=> ['type' => 'string'],
		'groups_ids'	=> ['type' => 'many2many', 
						  'foreign_object'	=> 'core\Group', 
						  'foreign_field'	=> 'users_ids', 
						  'rel_table'		=> 'core_rel_group_user', 
						  'rel_foreign_key'	=> 'group_id', 
						  'rel_local_key'	=> 'user_id']
	];
}
```



## Groups

The structure is defined inside the `core\Group` class (`packages/core/classes/Group.class.php`).

There, you will find informations about the users inside the group, and also the rights attached to these groups. 

```php
<?php
public static function getColumns() {
	return [
		'name'			=> ['type' => 'string'],
        
		'users_ids'		=> ['type' => 'many2many', 
						  'foreign_object'	=> 'core\User', 
						  'foreign_field'	=> 'groups_ids', 
						  'rel_table'		=> 'core_rel_group_user', 
						  'rel_foreign_key'	=> 'user_id', 
						  'rel_local_key'	=> 'group_id'],
                            
		'permissions_ids'	=> ['type' => 'one2many', 
						'foreign_object'	=> 'core\Permission', 
						'foreign_field'		=> 'group_id']
	];
}
```



## ACL 

The structure is defined inside the `core\Permission`(`packages/core/classes/Permission.class.php`).

In resume of the previous sections, Users are inside groups, and those groups have different rights (property **group_id**).

They are similar to the **classical roles** (admin,...) to which you can assign users.

```php
<?php
public static function getColumns() {
	return [
		'class_name'	=> ['type' => 'string'],
		'group_id'		=> [
						'type'				=> 'many2one', 
						'foreign_object'	=> 'core\Group', 
						'foreign_field'		=> 'permissions_ids'
					],
		'rights'		=> ['type' => 'integer']
	];
}
```



The field 'rights' of the `Permission` class is a binary mask (logical OR) of the rights given to the related group.
If a user belongs to several groups, the permission set will result in the most permissive combination of the rights from all its groups.

Rights values that can be assigned are defined in the file `/eq.lib.php` :
```php
<?php
    
define('R_CREATE',	1);	
define('R_READ',	2);	
define('R_WRITE',	4);	
define('R_DELETE',	8); 	
define('R_MANAGE',	16); 	// autorisation to manage the rights 
```

!!! Notes "Default rights"
    All users receive the default permissions, defined in the configuration file (`/config.json`) through setting `DEFAULT_RIGHTS`.

### AccessController.php

This file is the built-in "control tower" of eQual, and is located in **/lib/equal/access/**. What it does is granting the permission (or not) to perform CRUD actions depending on a few set criteria. This service is called by default and you usually don't have to think about it.

#### Examples :

* The controller tells us that the root_user has all rights.

* On the other hand, the **filter** method checks if a specific **user** has the rights to execute a specific operation.

* Own objects :

  For example, if the user created the object himself, then he will have the rights to **read** it.

> Remember : The permission object has the rights property, where you decide which rights are available for which users/groups

* The User also has the rights to **read and write(update)** his own object.

* If you use collection methods from the collection class, you often need rights to perform them.

   *for example*:

   The search method checks if you have the rights to perform the **read** operation, with a sub-condition : if you have the right to perform the **create** operation, you as a consequence, also have the right to perform the **read** operation.  

  

#### Custom security rules

If you need custom security rules, you can also **overwrite AccessController** (at your own risks). This is particularly useful to establish future-proof settings, as well as an alternative to core_permission (see [Cheat Sheet > Grant DB rights](../howtos-and-examples/generic-cheat-sheet.md)).

In the following section we'll see how to proceed:

### Overriding AccessController

The default AccesController service is defined in  `/lib/equal/access/AccessController.class.php` , and can be overridden by a custom service to match any specific logic.



In **/lib**, create a folder by the name of your project, you want a directory similar to this: **`/lib/myapp/access/AccessController.class.php`**

Then, in the **config.inc.php** file of your package (located at`/packages/myapp/config.inc.php`), add this line :

```php
<?php
namespace config;
register('access', 'myapp\access\AccessController');
```

Finally, open your newly created `AccessController.class.php` and copy paste this :

```php
<?php
namespace myapp\access; // change 'myapp' with actual name
use equal\organic\Service;
use equal\services\Container;

class AccessController extends \equal\access\AccessController {
    
  // rewrite functions here to override their default behavior
    
  // non-exhaustive example with filter:
  filter($operation, $object_class='*', $object_fields=[], $object_ids=[]){
    $user_id = $this->container->get('auth')->userId();
    // grant READ rights over 'User' class when an user is 
    // authenticated (0 = guest_user)
    if($object_class == 'myapp\User') {
      if($operation == QN_R_READ) {
        if($user_id > 0) {
          return $object_ids;
        }    
      }
    }
  }
    
}
```



## Authentication



### Access token

eQual uses JWT tokens that are exchanged between the back-end and the client (browser ) as HttpOnly cookie.

!!! Note "using CLI"
    There is no authentication using CLI: in command-line context, user is identified as root with full privileges.

During authentication (via the signin controller) a token is generated, according to the AUTH_ACCESS_TOKEN_VALIDITY parameter, and stored by the browser.

The duration defined in AUTH_ACCESS_TOKEN_VALIDITY corresponds to the maximum duration of inactivity of a user session.

When the token's validity limit has been exceeded, the token is deleted by the browser and the user must identify himself again.

!!! Note "Extension of validity"
    Each time a valid session token is used, it authenticates the user for a minimum of 1 hour. The validity of the token is extended if necessary.


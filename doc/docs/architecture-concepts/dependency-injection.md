# Dependency injection

Scripts that act as controllers have a scope of their own.

On the other hand, services are instantiated using a Container that is defined in the global scope.

eQual offers a series of services that can be used to ease the writing of controllers (for instance, the 'context' service can provide the HTTP request the current thread is responding to).


!!! note "About controllers"
    Under the hood, controllers are always included by the the eQual::run() method. That means that they have an access to global vars, but all the variables declared within a controller remains separate from the global scope.

This can be done by using the `eQual::announce` method, which allows dependency injection.

Dependency injection consists of receiving two kind of data :

1) the parameters received from the HTTP requests (or CLI equivalent call).
2) the instances to the services that the controller needs in order to perform its required tasks.



```php
<?php
list($params, $providers) = announce([
    'description'	=>	"",
    'response'      => [
        'content-type'  => 'application/json',
        'charset'       => 'utf-8',
        'accept-origin' => '*'
    ],
    'params' 		=>	[
    	[...]
    ],
    'providers'     => ['context', 'orm']   // list of services to inject
]);

/**
 * @var \equal\php\Context          $context
 * @var \equal\orm\ObjectManager    $orm
 */
list($context, $orm) = $providers['context', 'orm'];
```

!!! Note "About arguments order"
    The order in which the services are declared has no impact on the way they are fetched. The returned `providers` structure is a maps that can be accessed by using the service names as keys.

## List of eQual predefined services

```
                'report'    => 'equal\error\Reporter',
                'auth'      => 'equal\auth\AuthenticationManager',
                'access'    => 'equal\access\AccessController',
                'context'   => 'equal\php\Context',
                'validate'  => 'equal\data\DataValidator',
                'adapt'     => 'equal\data\DataAdapter',
                'orm'       => 'equal\orm\ObjectManager',
                'route'     => 'equal\route\Router',
                'log'       => 'equal\log\Logger',
                'cron'      => 'equal\cron\Scheduler',
                'dispatch'  => 'equal\dispatch\Dispatcher'
```



## Injection of custom services

In some case, it is useful to design a custom service with a logic of its own.
In other situation, the Access Control Management might be different than the one implemented by eQual.

In such cases, it is possible to inject dependencies by specifying their class (in addition to their names).
By doing so, we can perform 2 operation at a time :



1) inject a custom service ;
2) replace the default service with the custom one (by using an existing predefined name to override the service).

For instance, here is how the Access controller and Authentication manager can be overridden:

```php
<?php
list($params, $providers) = eQual::announce([
    'response'      => [
        'content-type'  => 'application/json',
        'charset'       => 'utf-8',
        'accept-origin' => '*'
    ],
    'params' 		=>	[...],
    'providers'     =>  [
        'auth',
        'access'  => custom\AccessController
    ]
]);

/**
* @var equal\auth\AuthenticationManager		$auth
* @var custom\AccessController			    $access
*/
list($auth, $access)  = [$providers['auth'], $provider['access']];
```


By doing so, all calls made within the Collection class will use the `custom\AccesController` to check the permissions of the current user for CRUD requests.

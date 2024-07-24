# Services

## Built-in services

Here is the full list of eQual built-in services with their related classes :

|**ID**|**DESCRIPTION**|
|--|--|
|report|**`equal\error\Reporter`**<br />Intercepts, handles and filters error and exceptions messages and stores them into a log file. |
|auth|**`equal\auth\AuthenticationManager`**<br />Manages the credentials & authentication tokens. |
|access|**`equal\access\AccessController`**<br /> Manages the access/permissions of User & Groups to entities & controllers. |
|context|**`equal\php\Context`**<br /> Manages the HTTP requests & responses. |
|validate|**`equal\data\DataValidator`**<br /> Checks the fields consistency of entities & controllers. |
|adapt|**`equal\data\DataAdapterProvider`**<br />DAP provides DataAdapters for transforming the data to the needed type (example: string to date) according to a specific type (JSON, SQL, TXT). |
|orm|**`equal\orm\ObjectManager`**<br /> Manages the objects (classes). |
|route|**`equal\route\Router`**<br /> Returns an existing route. |
|spool|**`equal\email\EmailSpooler`**<br /> Manages the emails. |

!!! note "Orm service"
    The **'orm'** service may be accessed without DB, which is not the case, for example, of the **'auth'** service which needs User objects.

## How services are instantiated

The services are listed at `eq.lib.php` in a container responsible to instantiate them.

```php
<?php
$container->register([
    'report'    => 'equal\error\Reporter',						
    'auth'      => 'equal\auth\AuthenticationManager',
    'access'    => 'equal\access\AccessController',
    'context'   => 'equal\php\Context',
    'validate'  => 'equal\data\DataValidator',
    'adapt'     => 'equal\data\DataAdapter',
    'orm'       => 'equal\orm\ObjectManager',
    'route'     => 'equal\route\Router',
    'log'       => 'equal\log\Logger',
    'spool'     => 'equal\email\EmailSpooler',
    'cron'      => 'equal\cron\Scheduler',
    'dispatch'  => 'equal\dispatch\Dispatcher'
]);
```

If a controller requires the use of a service, it needs to invoke it :

```php
<?php
list($params, $providers) = announce([
    'description'	=>	"Attempts to log a user in.",
    'params' 		=>	[
        'login'		=>	[
            'description'   => "user name",
            'type'          => 'string',
            'required'      => true
        ],
        'password' =>  [
            'description'   => "user password",
            'type'          => 'string',
            'required'      => true
        ]
    ],
    'response'      => [
        'content-type'      => 'application/json',
        'charset'           => 'utf-8',
        'accept-origin'     => '*'
    ],    
    'providers'     => ['context', 'auth', 'orm'],     // Services are invoked                                   
    
    'constants'     => ['AUTH_ACCESS_TOKEN_VALIDITY', 'AUTH_REFRESH_TOKEN_VALIDITY',
                        'AUTH_TOKEN_HTTPS']    
]);

// Services are assigned to variables to be used in the script
list($context, $om, $auth) = [ 
    $providers['context'], 
    $providers['orm'], 
    $providers['auth']
]; 

```

Every service is given an arbitrary name that can be overwritten (*limited to the use of the controller*).

Example : 

```php
<?php
    [...]
     // contextDefiner instead of context
	'providers'     => ['contextDefiner' => 'equal\php\Context'] 
```

If the service is present in the global `config.inc.php`, it can also be overwritten inside the `config.inc.php` files of the packages.

If a controller calls an other controller, the called controller won't access the services the calling controller has access to. There is no inheritance for services between controllers.



For more info (custom services & service invocation), go to [*Dependency injection*](dependency-injection.md).


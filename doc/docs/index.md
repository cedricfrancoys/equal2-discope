![](assets/img/equal_logo_w_bg.png)

# eQual Framework

eQual is a versatile, language-agnostic, and web-oriented framework, aiming to elegantly manage interactions between front-end Apps and Business Logic involved in modern Web Applications.

**Natively Secure**  
eQual offers native HTTP Auth support (JWT, CORS) to secure every endpoint, along with various Access Control strategies (ACL, RBAC, ABAC, PBAC).

**Server-Side Scripting**  
eQual allows to implement custom logic on any route endpoint, for quick creation of controllers and **micro-services**.

**Instant APIs Without Code**  
eQual provides tools for automatic generation of fully-featured ReST APIs with live documentation, accessible for any kind of service.


```php
<?php
// Tired of steep learning curves?
echo "This is a valid Contoller!"
```



## Why eQual

While being a solid framework that has everything you need to do almost anything, eQual has been designed to oppose as little constraints as possible to developers.

Most frameworks require a long time to master and, while learning them, you might find yourself struggling to achieve simple things.  
With eQual, taking advantage of the native features is entirely up to you.

Actually, since you're reading this, you probably already know everything needed to start using it. So, just use it your way!   


> *What I value the most in eQual is its high level of re-usability and its way of defining controllers by announcing what they do, what they expect and what response they provide, which relieves me from documenting boredom and having to remember everything.*

*— Cédric Françoys*


## Sneak peek

### Controller
`/packages/demo/data/simple.php`:

```php
<?php
/**
 * (near) Zero learning-curve development
 *
 */
echo 'This is a valid HTTP controller';
```

When needed, use existing components, or not. (using the ones you already know is encouraged!)
`/packages/demo/data/reuse.php`:

```php
<?php
/**
 * Code re-usability
 *
 */
echo run('get', 'demo_simple');
```


### HTTP request

`wget http://localhost/index.php?get=demo_simple`

> Response:  
> Header excerpt:  
> Status Code: 200 OK  
> Content-Length: 26  
> Content-Type: text/html; charset=UTF-8  
> Body:  
> This is a valid controller!

### CLI
```bash
$ ./equal.run --get=demo_simple
```

> This is a valid controller!


## Real life action
```bash
$ ./equal.run --get=demo_image
```

`/packages/demo/data/image.php`:

```php
<?php
use equal\http\HttpRequest;

/**
 * HTTP native support
 *
 */
list($params, $providers) = eQual::announce([
    'description'   => 'Get picture data from imgur.com using imgur API.',
    'params'        => [
        'id' => [
            'description'   => 'Hash of the image to retrieve',
            'type'          => 'string',
            'default'       => 'a5NE1kW'
        ]
    ],
    'response'      => [
        'content-type'  => 'application/json',
        'charset'       => 'utf-8'
    ],
    'providers'     => ['context']
]);

$request = new HttpRequest("GET https://api.imgur.com/3/image/{$params['id']}");

$response = $request
    ->header('Authorization', "Client-ID 34030ab1f5ef12d")
    ->send();
            
$providers['context']
    ->httpResponse()
    ->body(['result' => $response->body()])
    ->send();
```



## In short

eQual has a robust set of **native features** : 

* CQRS architecture (Action handlers, Data providers, App providers)
* I/O as HTTP messages
* CRUD operations through ORM & Collections
* Model definition with inheritance support
* Dependency injection
* Data Adaptation
* Authentication Management
* Access Control Management
* Services overloading
* Cascading Configuration 
* Unit testing
* Logging
* Debugging console



## More


This framework has been designed for those who : don't want to always re-invent the wheel (but might occasionally enjoy it); dislike learning entire frameworks at once; and loathe dealing with dozens of files to achieve simple things.

In most situations you don't need everything a framework offers. For that reason, eQual does not require you to comply with a specific logic or structure, and allows you to use it the way you want.


eQual learning curve is actually more like learning stairs : one can use it without having to start from scratch or even without knowing it.



### Key features

* **client-server oriented**: HTTP native support, intended for REST API development and back-end processing.
* **code reusability**: easy integration of existing controllers results (controllers can be used as functions).
* **self-documented code**: available tools invite to be descriptive about data structure: how it is structured, what is expected, what errors can occur.
* **self-explanatory API** : each route comes with a description (announcement) of its dependencies and expected parameters, which eases greatly the communication between front-end and back-end developers.


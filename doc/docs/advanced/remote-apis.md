## Remote-apis

Example of remote-api :

```php
<?php
$body = [
    'access_token'  => MAPBOX_KEY
];

// Send query to Mapbox geocoding API
$request = new HttpRequest('/geocoding/v5/mapbox.places/'.urlencode
($params['address']).'.json', ['Host' => 'api.mapbox.com:443']);

$response = $request
            ->setBody($body)
            ->send();

/* expected response match a JSON array of geo+json Features 
(https://tools.ietf.org/html/rfc7946) */
$response->getBody();
```



An other example :

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

**Use cases of remote-API'S in eQual** :

- eQual is used to relay indirectly (use of a remote-API) informations to the User.

  Example :

  ```php
  $request = new HttpRequest('/geocoding/v5/mapbox.places/'.urlencode
  ($params['address']).'.json', ['Host' => 'api.mapbox.com:443']);
  ```

  In this example, eQual sends 'address information' and expects to get 'gps informations' from an API.

- eQual is used as proxy

  Some API's expect identifiers, and to keep those secret, the API-call can be done in eQual.

- eQual is used as an adapter

  Example :

  if an API-response is in XML, an eQual controller could transform(ex: to JSON)/read the file to the expected value, needed for the frontend.  





### Authentication to external social networks

The `oauth controller` attempts to authenticate a user from an external social network.

The controller expects two parameters :

```php
'params' 		=>	[
        'network_name'  =>  [
            'description'   => 'name of the social network to address 
            oauth request.',
            'type'          => 'string', 
            'required'      => true
        ],
        'network_token' =>  [
            'description'   => 'valid acess token for oauth.',
            'type'          => 'string',
            'required'      => true
        ]
    ],
```

And gives an `access token` as a response, that will be used to encrypt the messages sent to the server.

Those tokens have a limited life expectancy and must be renewed.
<?php
/*
    This file is part of the eQual framework <http://www.github.com/cedricfrancoys/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2021
    Licensed under GNU GPL 3 license <http://www.gnu.org/licenses/>
*/
list($params, $providers) = announce([
    'description'   => "Returns the schema of given class (model)",
    'params'        => [
                        'package' => [
                            'description'   => 'Name of the package for which the schema is requested',
                            'type'          => 'string',
                            'required'      => true
                        ],
                        'xml' => [
                            'description'   => 'Updated XML of the schema for given package',
                            'type'          => 'string',
                            'required'      => true
                        ],                        
    ],
    'response'      => [
        'content-type'      => 'application/json',
        'charset'           => 'utf-8',
        'accept-origin'     => '*'
    ],
    'providers'     => ['context', 'auth'] 
]);


list($context, $auth) = [ $providers['context'], $providers['auth'] ];

// retrieve related cache-id
$cache_id = md5($auth->userId().'::'.'get'.'::'.'utils_sqldesigner_schema');

// update cached response, if any
$cache_filename = QN_BASEDIR.'/cache/'.$cache_id;
if(file_exists($cache_filename)) {
    // retrieve headers
    list($headers, $result) = unserialize(file_get_contents($cache_filename));
    file_put_contents($cache_filename, serialize([$headers, $params['xml']]));
}

$context->httpResponse()
        ->status(204)
        ->body('')
        ->send();
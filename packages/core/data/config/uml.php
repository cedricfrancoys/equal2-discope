<?php
/*
    This file is part of the eQual framework <http://www.github.com/cedricfrancoys/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2024
    Licensed under GNU LGPL 3 license <http://www.gnu.org/licenses/>
*/

list($params, $providers) = eQual::announce([
    'description'   => "Attempts to create a new package using a given name.",
    'params'        => [
        'package'   => [
            'description'   => 'Name of the package of the new model.',
            'type'          => 'string',
            'required'      => true
        ],
        'path' =>  [
            'description'    => 'Relative path to the file inside `packages/{package}/`.',
            'type'          => 'string',
            'required'      => true
        ],
        'type'      => [
            'description'   => 'Type of the UML data.',
            'type'          => 'string',
            'required'      => true,
            'selection'     => [
                'erd'
            ]
        ]
    ],
    'response'      => [
        'content-type'  => 'text',
        'charset'       => 'utf-8',
        'accept-origin' => '*'
    ],
    'access' => [
        'visibility'        => 'protected',
        'groups'            => ['admins']
    ],
    'providers'     => ['context', 'orm', 'access']
]);

/**
 * @var \equal\php\Context              $context
 * @var \equal\orm\ObjectManager        $orm
 * @var \equal\access\AccessController  $ac
 */
list($context, $orm, $ac) = [$providers['context'], $providers['orm'], $providers['access']];

$response_code = 200;

$package = $params["package"];

// Checking if package exists
if(!file_exists(QN_BASEDIR."/packages/{$package}")) {
    throw new Exception('missing_package_dir', QN_ERROR_INVALID_PARAM);
}

// Checking if package exists
if(!file_exists(QN_BASEDIR."/packages/{$package}/uml")) {
    throw new Exception('malformed_package', QN_ERROR_INVALID_CONFIG);
}

$path_arr = explode('/', $params['path']);
$filename = array_pop($path_arr);
$path = implode("/", $path_arr);

$path = str_replace("..", "", $path);

$extension = ".{$params["type"]}.json";

if(substr($filename, -strlen($extension)) != $extension) {
    $filename = $filename.$extension;
}

if(!file_exists(QN_BASEDIR."/packages/{$package}/uml/{$path}/{$filename}")) {
    throw new Exception('io_error', QN_ERROR_INVALID_CONFIG);
}

$context->httpResponse()
        ->body(file_get_contents(QN_BASEDIR."/packages/{$package}/uml/{$path}/{$filename}"))
        ->status(200)
        ->send();

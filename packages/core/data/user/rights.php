<?php
/*
    This file is part of the eQual framework <http://www.github.com/cedricfrancoys/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2021
    Licensed under GNU LGPL 3 license <http://www.gnu.org/licenses/>
*/
use core\User;

list($params, $providers) = announce([
    'description'   => 'Retrieve current permission that a user has on a given entity.',
    'response'      => [
        'content-type'  => 'application/json',
        'charset'       => 'UTF-8',
        'accept-origin' => '*'
    ],
    'constants'     => ['DEFAULT_RIGHTS'],
    'params'        => [
        'user' =>  [
            'description'   => 'login (email address) or ID of targeted user.',
            'type'          => 'string',
            'required'      => true
        ],
        'entity' =>  [
            'description'   => 'Entity on which operation is to be granted.',
            'type'          => 'string',
            'default'       => '*'
        ]
    ],
    'providers'     => ['context', 'access']
]);

list($context, $access) = [ $providers['context'], $providers['access'] ];

// retrieve targeted user
if(is_numeric($params['user'])) {
    $ids = User::search(['id', '=', $params['user']])->ids();
}
else {
    // retrieve by login
    $ids = User::search(['login', '=', $params['user']])->ids();
}

if(!count($ids)) {
    throw new Exception("unknown_user", QN_ERROR_UNKNOWN_OBJECT);
}

$user_id = array_shift($ids);
$rights = $access->getUserRights($user_id, $params['entity']);

// convert ACL value to human string
$rights_txt = [];
$operations = [
    QN_R_CREATE => 'create',
    QN_R_READ   => 'read',
    QN_R_WRITE  => 'update',
    QN_R_DELETE => 'delete',
    QN_R_MANAGE => 'manage'
];

foreach($operations as $op => $name) {
    if($rights & $op) {
        $rights_txt[] = $name;
    }
}

$result = [
    'user_id'       => $user_id,
    'entity'        => $params['entity'],
    'rights'        => $rights,
    'rights_txt'    => $rights_txt
];

$context->httpResponse()
        ->status(200)
        ->body($result)
        ->send();

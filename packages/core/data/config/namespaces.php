<?php
/*
    This file is part of the eQual framework <http://www.github.com/cedricfrancoys/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2021
    Licensed under GNU LGPL 3 license <http://www.gnu.org/licenses/>
*/
list($params, $providers) = announce([
    'description'   => 'Returns the list of namespaces defined in specified package.',
    'params'        => [
        'package' => [
            'description'   => 'Name of the package for which the list is requested',
            'type'          => 'string',
            'default'       => '*'
        ],
        'path' => [
            'description'   => 'Path within the package for limiting the result to.',
            'type'          => 'string',
            'default'       => ''
        ]
    ],
    'response'      => [
        'content-type'      => 'application/json',
        'charset'           => 'utf-8',
        'accept-origin'     => '*'
    ],
    'providers'     => ['context']
]);


list($context) = [$providers['context']];


/**
 * Utilities for retrieving classes within a package namespace and sub-namespaces.
 *
 */
// prevent multi-declaration in global scope
if(!function_exists('get_namespaces')) {

    // recurse within the `classes` directory
    function get_dirs($dir) {
        $result = [];
        if(is_dir($dir) && $list = scandir($dir)) {
            foreach($list as $node) {
                if(!in_array($node, ['.', '..']) && is_dir($dir.'/'.$node)) {
                    $data = get_dirs($dir.'/'.$node);
                    $result[] = $node;
                    foreach($data as $subnode) {
                        $result[] = "$node\\$subnode";
                    }
                }
            }
        }
        return $result;
    }

	function get_namespaces($package, $path='') {
		$data = [];
        $path = trim($path, '/');
		$package_dir = 'packages/'.$package.'/classes';
        if(strlen($path)) {
            $package_dir .= '/'.$path;
        }
		if(is_dir($package_dir) && ($list = scandir($package_dir))) {
            $data = get_dirs($package_dir);
		}
		return $data;
	}
}

$data = array();

// if no package is given, return a map having packages as keys and arrays of related classes as values
if($params['package'] == '*') {
	// get listing of existing packages
	$json = run('get', 'core_config_packages');
	$packages = json_decode($json, true);
	foreach($packages as $package) {
		try {
			$data[$package] = get_namespaces($package);
		}
		catch(Exception $e) {
			// ignore package with no class definition
			continue;
		}
	}
}
else {
	// if a package is specified, return an array of all related classes
	$data = get_namespaces($params['package'], $params['path']);
}


$context->httpResponse()
        ->body($data)
        ->send();
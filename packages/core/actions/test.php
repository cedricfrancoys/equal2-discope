<?php

use equal\data\adapt\DataAdapterProviderSql;
use equal\http\HttpRequest;
use equal\http\HttpResponse;

list($params, $providers) = eQual::announce([
    'description'   => '',
    'params'        => [],
    'providers'     => ['context', 'orm'],
    'response'      => [
        'content-type'  => 'application/json',
        'charset'       => 'UTF-8',
        'accept-origin' => '*'
    ]
]);

list($context, $orm) = [$providers['context'], $providers['orm']];


// $url = $endpoint.'/me';

$subdomain = 'ced123';

if(record_exists($subdomain)) {
    throw new Exception('record already exists', EQ_ERROR_CONFLICT_OBJECT);
}

$result = create_record($subdomain, "51.38.239.56");

if(!$result) {
    echo 'unable to create record';
}
else {
    print_r($result);
}

$context->httpResponse()
    ->send();



/**
 * @return HttpResponse
 */
function api_ovh_request($method, $route, $payload = []) {
    $application_key = 'ae458065b77f806f';
    $application_secret = '1f0cc8e9ae830bd58206d5d9f43f4a25';
    $consumer_key = 'fb6f61e612747fef6f86d93b293518d0';

    $endpoint = 'https://eu.api.ovh.com/1.0';

    $url = $endpoint.$route;

    $timeResponse = (new HttpRequest("GET $endpoint/auth/time"))->send();
    $now = $timeResponse->body();

    $request = new HttpRequest("$method $url");

    if($method == 'GET') {
        $body = '';
    }
    else {
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $request->body($body);
    }

    $fingerprint = $application_secret . '+' . $consumer_key . '+' . $method . '+' . $url . '+' . $body . '+' . $now;

    $request
        ->header('X-Ovh-Timestamp', $now)
        ->header('X-Ovh-Consumer', $consumer_key)
        ->header('X-Ovh-Signature', '$1$'.sha1($fingerprint))
        ->header('X-Ovh-Application', $application_key)
        ->header('Content-Type', "application/json");

    return $request->send();
}


function record_exists($subdomain) {
    $response = api_ovh_request('GET', '/domain/zone/yb.run/record?fieldType=A&subDomain='.$subdomain);

    // check response status
    $status = $response->getStatusCode();

    if($status != 200) {
        return null;
    }

    $data = $response->body();

    return count($data);
}

function create_record($subdomain, $target) {
    $response = api_ovh_request('POST', '/domain/zone/yb.run/record', [
            "fieldType" => "A",
            "subDomain" => $subdomain,
            "target"    => $target,
            "ttl"       => 0
        ]);

    // check response status
    $status = $response->getStatusCode();

    if($status != 200) {
        return null;
    }

    $data = $response->body();

    api_ovh_request('POST', '/domain/zone/yb.run/refresh');
    return $data;
}

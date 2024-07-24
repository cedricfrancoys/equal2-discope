<?php
/*
    This file is part of the eQual framework <http://www.github.com/cedricfrancoys/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2021
    Licensed under GNU LGPL 3 license <http://www.gnu.org/licenses/>
*/
namespace equal\http;

use equal\http\HttpUri;
use equal\http\HttpHeaders;


/*
    # Recap about HTTP messages


    ## HTTP request

    @link http://www.ietf.org/rfc/rfc2616.txt

    * method    string
    * URI       object
    * protocol  object

    example: GET http://www.example.com/path/to/file/index.html HTTP/1.0

    getters : method, URI, protocol


    ### URI structure:
    @link http://www.ietf.org/rfc/rfc3986.txt

    generic form: scheme:[//[user[:password]@]host[:port]][/path][?query][#fragment]
    example: http://www.example.com/index.html


    URI __toString

    getters :   scheme, user, password, host, port, path, query, fragment


    ### protocol structure:
        name    always 'HTTP'
        version HTTP version (1.0 or 1.1)

    generic form: name/version
    example: HTTP/1.0

    getters: name (string), version (string)


    ## HTTP response

    protocol
        name    always 'HTTP'
        version HTTP version (1.0 or 1.1)
    status
        code    HTTP status code
        reason  human readable HTTP status

*/


class HttpMessage {

    /** @var string     (ex.: 'POST') */
    private $method;

    /** @var string     (ex.: 'HTTP/1.1') */
    private $protocol;

    /** @var mixed (string | array) */
    // The body part is intended to be an associative array mapping keys (parameters) with their related values.
    // However, if HTTP message content-type cannot be determined, we fallback to raw value.
    private $body;

    /** @var string unprocessed version of the body (as sent or as received) */
    private $raw_body;

    /** @var string */
    private $status;

    /** @var HttpUri */
    private $uri;

    /** @var HttpHeader */
    private $headers;

    /** @var string[] */
    protected static $HTTP_METHODS = ['GET', 'POST', 'HEAD', 'PUT', 'PATCH', 'DELETE', 'PURGE', 'OPTIONS', 'TRACE', 'CONNECT'];

    /**
     * Handle sub objects for deep cloning
     *
     */
    public function __clone() {
        if($this->uri) {
            $this->uri = clone $this->uri;
        }
        if($this->headers) {
            $this->headers = clone $this->headers;
        }
    }

    /**
     *  List from Wikipedia article "List of HTTP status codes"
     *  @link https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     */
    protected static $HTTP_STATUS_CODES = [
        '100' => 'Continue',
        '101' => 'Switching Protocols',
        '102' => 'Processing',
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '207' => 'Multi-Status',
        '208' => 'Already Reported',
        '210' => 'Content Different',
        '226' => 'IM Used',
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '306' => '(none)',
        '307' => 'Temporary Redirect',
        '308' => 'Permanent Redirect',
        '310' => 'Too many Redirects',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Time-out',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested range unsatisfiable',
        '417' => 'Expectation failed',
        '418' => 'I’m a teapot',
        '421' => 'Bad mapping / Misdirected Request',
        '422' => 'Unprocessable entity',
        '423' => 'Locked',
        '424' => 'Method failure',
        '425' => 'Unordered Collection',
        '426' => 'Upgrade Required',
        '428' => 'Precondition Required',
        '429' => 'Too Many Requests',
        '431' => 'Request Header Fields Too Large',
        '449' => 'Retry With',
        '450' => 'Blocked by Windows Parental Controls',
        '451' => 'Unavailable For Legal Reasons',
        '456' => 'Unrecoverable Error',
        '444' => 'No Response',
        '495' => 'SSL Certificate Error',
        '496' => 'SSL Certificate Required',
        '497' => 'HTTP Request Sent to HTTPS Port',
        '499' => 'Client Closed Request',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway or Proxy Error',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Time-out',
        '505' => 'HTTP Version not supported',
        '506' => 'Variant Also Negotiates',
        '507' => 'Insufficient storage',
        '508' => 'Loop detected',
        '509' => 'Bandwidth Limit Exceeded',
        '510' => 'Not extended',
        '511' => 'Network authentication required',
        '520' => 'Unknown Error',
        '521' => 'Web Server Is Down',
        '522' => 'Connection Timed Out',
        '523' => 'Origin Is Unreachable',
        '524' => 'A Timeout Occurred',
        '525' => 'SSL Handshake Failed',
        '526' => 'Invalid SSL Certificate',
        '527' => 'Railgun Error'
    ];

    /**
     *
     * @param $headline string
     * @param $headers  array   associative array with headers names as keys and headers content as values
     * @param $body mixed (array, string)   either associative array (of key-value pairs) or raw text for unknown content-type
     */
    public function __construct($headline, $headers=[], $body='') {
        // $headline is handled in HttpResponse and HttpRequest constructors (which might update protocol and method)
        $this->setStatus(null);
        // headers have to be set before body
        $this->setHeaders($headers);
        // assign body and try to convert raw content to an array, based on content-type from headers
        $this->setBody($body);
        // default protocol
        $this->setProtocol('HTTP/1.1');
        // default method
        $this->setMethod('GET');
        // init URI (this is an invalid URI, so all members will be set to null)
        $this->setUri('');
    }

    public function setMethod($method) {
        $method = strtoupper($method);
        if(in_array($method, self::$HTTP_METHODS) ) {
            $this->method = $method;
        }
        return $this;
    }

    /**
     *
     * @param $headers  array
     */
    public function setHeaders($headers) {
        if($headers instanceof HttpHeaders) {
            $this->headers = $headers;
        }
        else {
            $this->headers = new HttpHeaders((array) $headers);
        }
        return $this;
    }

    public function setHeader($header, $value) {
        $this->headers->set($header, $value);
        // maintain URI consistency
        if(strcasecmp($header, 'Host') === 0) {
            $this->uri->setHost($value);
        }
        return $this;
    }

    public function setCookie($cookie, $value, $params=null) {
        $this->headers->setCookie($cookie, $value, $params);
        return $this;
    }

    public function setCharset($charset) {
        $this->headers->setCharset($charset);
        return $this;
    }

    public function setContentType($content_type) {
        $this->headers->setContentType($content_type);
        return $this;
    }

    /**
     *
     * This method is invoked by the HttpRequest and HttpResponse constructors.
     *
     * @param $uri  string
     */
    public function setUri($uri) {
        $this->uri = new HttpUri($uri);

        // maintain headers consistency
        if($host = $this->uri->getHost()) {
            if($port = $this->uri->getPort()) {
                $host = $host.':'.$port;
            }
            $this->headers->set('Host', $host);
        }
        // maintain body consistency
        if($query = $this->uri->getQuery()) {
            $body = [];
            parse_str($query, $body);
            $this->extendBody($body);
        }
        return $this;
    }

    public function extendBody($params) {
        if(is_array($params)) {
            if(is_array($this->body) && !empty($this->body)) {
                $this->body = array_merge($this->body, $params);
            }
            else $this->body = $params;
        }
        else {
            if(is_array($this->body)) {
                $this->body = array_merge($this->body, (array)$params);
            }
            else $this->body .= $params;
        }
        return $this;
    }

    /**
     * Tries to force conversion to an associative array based on content-type.
     * Falls back to a raw string if conversion is not possible.
     *
     *
     * @param $body mixed (string | array)
     */
    public function setBody($body, $raw=false) {
        $this->raw_body = $body;

        if(!$raw && !is_array($body)) {
            // attempt to convert body to an associative array
            $content_type = $this->contentType();
            // adapt content type to map known MIMES to generic ones (@see https://www.iana.org/assignments/media-types/media-types.xhtml)
            if(stripos($content_type, '+json')) {
                $content_type = 'application/json';
            }
            else if(stripos($content_type, '+xml')) {
                $content_type = 'application/xml';
            }
            switch($content_type) {
                case 'multipart/form-data':
                    // retrieve boundary from content type header
                    preg_match('/boundary=(.*)$/', $this->getHeader('Content-Type'), $matches);
                    // ignore malformed request
                    if(!isset($matches[1]) || !isset($matches[2])) break;
                    $boundary = $matches[1];
                    // split content by boundary and get rid of last -- element
                    $blocks = preg_split("/-+$boundary/", $body);
                    array_pop($blocks);
                    // build request array
                    $request = [];
                    // loop data blocks
                    foreach ($blocks as $id => $block) {
                        // skip empty blocks
                        if (empty($block)) continue;
                        // parse uploaded files
                        if (strpos($block, 'application/octet-stream') !== FALSE) {
                            // match "name", then everything after "stream" (optional) except for prepending newlines
                            preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
                        }
                        // parse all other fields
                        else {
                            // match "name" and optional value in between newline sequences
                            preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
                        }
                        // assign param/value pair to related body index
                        $request[$matches[1]] = $matches[2];
                    }
                    $body = http_build_query($request);
                    // we can now deal with the body as form-urlencoded data
                    // hence no break !
                case 'application/x-www-form-urlencoded':
                    $params = [];
                    // #memo : this function is impacted by ini_get('max_input_vars')
                    parse_str($body, $params);
                    $body = (array) $params;
                    break;
                case 'application/json':
                case 'application/javascript':
                case 'text/javascript':
                    // convert to JSON (with type conversion + fallback to string)
                    $arr = json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
                    if(!is_null($arr)) {
                        $body = $arr;
                    }
                    break;
                case 'text/xml':
                case 'application/xml':
                    libxml_use_internal_errors(true);
                    $xml = simplexml_load_string($body, "SimpleXMLElement", LIBXML_NOCDATA);
                    if($xml !== false) {
                        $body = self::xmlToArray($xml);
                    }
                    break;
                case 'text/plain':
                case 'text/html':
                default:
                    // fallback to raw content
            }
        }

        $this->body = $body;

        return $this;
    }

    public function setProtocol($protocol) {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * @param $status   mixed (string | integer)
     *
     * @return HttpMessage
     */
    public function setStatus($status) {
        // handle single status code argument
        if(is_numeric($status)) {
            // retrieve the 'reason' part
            $reason = isset(self::$HTTP_STATUS_CODES[$status])?' '.self::$HTTP_STATUS_CODES[$status]:'';
            $status = $status.$reason;
        }
        $this->status = $status;
        return $this;
    }

    /**
     *
     * can be forced to cast to string using $as_string boolean
     *
     * @return HttpUri
     *
     */
    public function getUri($as_string=false) {
        if($as_string) {
            return (string) $this->uri;
        }
        return $this->uri;
    }

    /**
     *
     * can be forced to cast to array using $as_array boolean
     *
     * @return HttpHeaders|array
     *
     */
    public function getHeaders($as_array=false) {
        if($as_array) {
            return $this->headers->getHeaders();
        }
        return $this->headers;
    }

    public function getHeader($header, $default=null) {
        return $this->headers->get($header, $default);
    }


    public function getMethod() {
        return $this->method;
    }

    public function getCookie($cookie) {
        return $this->headers->getCookie($cookie);
    }

    public function getCharset() {
        return $this->headers->getCharset();
    }

    public function getContentType() {
        return $this->headers->getContentType();
    }

    /**
     * @param   boolean   $raw  Flag for the version of the body to return. If set to true, the raw/unprocessed version of the body is returned.
     * @return  mixed           Associative array or raw data of the body/payload currently stored in the HTTP message.
     */
    public function getBody($raw=false) {
        return ($raw)?$this->raw_body:$this->body;
    }

    public function getProtocol() {
        return $this->protocol;
    }

    public function getProtocolVersion() {
        return (float) explode('/', $this->getProtocol())[1];
    }

    public function getStatus() {
        return $this->status;
    }

    public function getStatusCode() {
        $parts = explode(' ', $this->status, 2);
        return (int) $parts[0];
    }

    /**
     * Retrieve a value from message body based on a given param name
     *
     * @return mixed If $param is an array of parameters names, returns an associative array containing values for each given parameter, otherwise returns the value of a single parameter. If given parameter is not found, returns specified default value (fallback to null)
     */
    public function get($param, $default=null) {
        if(is_array($param)) {
            $res = [];
            foreach($param as $p) {
                if(isset($this->body[$p])) {
                    $res[$p] = $this->body[$p];
                }
                else {
                    $res[$p] = null;
                }
            }
        }
        else {
            $res = $default;
            if(isset($this->body) && is_array($this->body)) {
                if(isset($this->body[$param])) {
                    $res = $this->body[$param];
                }
            }
        }
        return $res;
    }

    /**
     * Assign a new parameter to message body, or update an existing one to a new value.
     * This method overwrites raw string value, if any.
     *
     * @param   $param  mixed(string|array)    For single assignment, $param is the name of the parameter to be set. In case of bulk assign, $param is an associative array with keys and values respectively holding parameters names and values.
     * @param   $value  mixed   If $param is an array, $value is not taken under account (this argument is therefore optional)
     * @return  HttpMessage Returns current instance
     */
    public function set($param, $value=null) {
        if(!is_array($this->body)) {
            $this->body = [];
        }
        if(is_array($this->body)) {
            if(is_array($param)) {
                foreach($param as $p => $val) {
                    $this->body[$p] = $val;
                }
            }
            else {
                $this->body[$param] = $value;
            }
        }
        return $this;
    }

    /**
     * Remove a parameter from the message body, if existing
     *
     *
     */
    public function del($param) {
        if(is_array($this->body)) {
            if(is_array($param)) {
                foreach($param as $p) {
                    if(isset($this->body[$p])) {
                        unset($this->body[$p]);
                    }
                }
            }
            else if(isset($this->body[$param])) {
                unset($this->body[$param]);
            }
        }
        return $this;
    }

    // Here-below are additional method using short name and get/set behaviour based on arguments

    public function method() {
        $args = func_get_args();
        if(count($args) < 1) {
            return $this->getMethod();
        }
        else {
            $method = $args[0];
            return $this->setMethod($method);
        }
    }

    public function body() {
        $args = func_get_args();
        if(count($args) < 1) {
            return $this->getBody();
        }
        else {
            return $this->setBody(...$args);
        }
    }

    public function protocol() {
        $args = func_get_args();
        if(count($args) < 1) {
            return $this->getProtocol();
        }
        else {
            $protocol = $args[0];
            return $this->setProtocol($protocol);
        }
    }

    /**
     *
     *
     */
    public function headers() {
        $args = func_get_args();
        if(count($args) < 1) {
            return $this->getHeaders();
        }
        else {
            $headers = $args[0];
            return $this->setHeaders($headers);
        }
    }

    /**
     *
     *
     */
    public function header() {
        $args = func_get_args();
        if(count($args) < 2) {
            $header = $args[0];
            return $this->getHeader($header);
        }
        else {
            list($header, $value) = $args;
            return $this->setHeader($header, $value);
        }
    }

    /**
     * Cookie getter/setter method based on arguments list
     * reminder: cookies are just a special kind of header either 'cookie' or 'set-cookie'
     *
     */
    public function cookie() {
        $args = func_get_args();
        if(count($args) < 2) {
            $cookie = $args[0];
            return $this->getCookie($cookie);
        }
        else {
            return $this->setCookie(...$args);
        }
    }

    /**
     * Charset getter/setter method based on arguments list
     * reminder: charset is stored either in 'content-type' or 'accept-charset' header
     *
     */
    public function charset() {
        $args = func_get_args();
        if(count($args) < 1) {
            return $this->getCharset();
        }
        else {
            $charset = $args[0];
            return $this->setCharset($charset);
        }
    }

    /**
     * Status getter/setter method based on arguments list
     * For setter, argument might be a full status line or a numeric code
     *
     */
    public function status() {
        $args = func_get_args();
        if(count($args) < 1) {
            return $this->getStatus();
        }
        else {
            $status = $args[0];
            return $this->setStatus($status);
        }
    }

    public function statusCode() {
        $args = func_get_args();
        if(count($args) < 1) {
            return $this->getStatusCode();
        }
        else {
            $status = $args[0];
            return $this->setStatus($status);
        }
	}

    /**
     * Content type getter/setter method based on arguments list
     * reminder: content type in stored either in 'content-type' or 'accept' header
     *
     */
    public function contentType() {
        $args = func_get_args();
        if(count($args) < 1) {
            return $this->getContentType();
        }
        else {
            $content_type = $args[0];
            return $this->setContentType($content_type);
        }
    }

    /**
     * Header getter/setter method based on arguments list
     *
     *
     */
    public function uri() {
        $args = func_get_args();
        if(count($args) < 1) {
            return $this->getUri();
        }
        else {
            $uri = $args[0];
            return $this->setUri($uri);
        }
    }

    /**
     * Send method is defined in HttpResponse and HttpRequest classes.
     *
     */
    public function send() {}


    /**
     * Returns true if the request is a XMLHttpRequest.
     *
     * Checks HTTP header for an X-Requested-With entry set to 'XMLHttpRequest'.
     *
     * @link http://en.wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
     *
     * @return bool     Returns true if the request is an XMLHttpRequest, false otherwise.
     */
    public function isXHR() {
        return ($this->headers->get('X-Requested-With') == 'XMLHttpRequest');
    }

   private static function xmlToArray(\SimpleXMLElement $obj) {
        $text = '';
        $attributes = [];
        $children = [];
        $has_children = false;

        if(is_object($obj)) {
            // get info for all namespaces
            $namespaces = $obj->getDocNamespaces(true);
            // append empty namespace
            $namespaces[null] = null;

            foreach( $namespaces as $ns => $nsUrl ) {
                // handle attributes
                $objAttributes = $obj->attributes($ns, true);
                foreach( $objAttributes as $attributeName => $attributeValue ) {
                    $attribName = trim($attributeName);
                    $attribVal = trim((string)$attributeValue);
                    if (!empty($ns)) {
                        $attribName = $ns . ':' . $attribName;
                    }
                    $attributes[$attribName] = $attribVal;
                }

                // handle children
                $objChildren = $obj->children($ns, true);
                $count_children = count($objChildren);
                if($count_children) {
                    $has_children = true;
                    foreach( $objChildren as $childName => $child) {
                        if(!isset($map_children_names[$childName])) {
                            $map_children_names[$childName] = 0;
                        }
                        ++$map_children_names[$childName];
                    }
                    foreach( $objChildren as $childName => $child ) {
                        if( !empty($ns) ) {
                            $childName = $ns.':'.$childName;
                        }
                        $res = self::xmlToArray($child);
                        if($count_children > 1 && $res['has_children']) {
                            $children[] = $res;
                        }
                        elseif($map_children_names[$childName] > 1) {
                            $children[] = $res;
                        }
                        else {
                            $children[$childName] = $res;
                        }
                    }
                }
                else {
                    $text = trim((string)$obj);
                    if( strlen($text) <= 0 ) {
                        $text = null;
                    }
                }
            }
        }

        return [
            'name'          => $obj->getName(),
            'value'         => $text,
            'attributes'    => $attributes,
            'has_children'  => $has_children,
            'children'      => $children
        ];
    }
}

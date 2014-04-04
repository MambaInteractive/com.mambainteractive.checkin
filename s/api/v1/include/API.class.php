<?php
require_once 'DataProvider.php';
require_once 'Response.php';
abstract class API
{
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     */
    protected $verb = '';
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
    /**
     * Property: file
     * Stores the input of the PUT request
     */
     protected $file = Null;

     protected $dp = Null;
    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request) {
        $this->dp = new DataProvider();
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }

        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }

        switch($this->method) {
        case 'DELETE':
        case 'POST':
            $this->request = $this->_cleanInputs($_POST);
            break;
        case 'GET':
            $this->request = $this->_cleanInputs($_GET);
            break;
        case 'PUT':
            $this->request = $this->_cleanInputs($_GET);
            $this->file = file_get_contents("php://input");
            break;
        default:
            $res = new Response('Error','','','json',new Error("Invalid Method","405"));
            $this->_response($res, 405);
            break;
        }
        }

public function processAPI() {
        if ((int)method_exists($this, $this->endpoint) > 0) {
            $res = $this->{$this->endpoint}($this->args);
            if(false){
                $res = new Response([new Error("Invalid Action","501")],"json");
                return $this->_response($res,501);
            }else{
                return $this->_response($res);
            }
        }
        $res = new Response([new Error("","400")],"json");
        return $this->_response($res, 400);
    }

    private function _response($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        $encData = $this->_encode($data);
        header("Content-Length: ".strlen($encData));
        header("Content-Type: ".$this->_getMIME($data->Type));
        return $encData;
    }
    private function _getMIME($name){
        switch($name)
        {
            case "json":
                return "application/json";
            case "xml":
                return "application/xml";
            case "csv":
                return "text/csv";
            default:
                return "text/html";
        }
    }
    private function _encode($data){
        switch($data->Type)
        {
            case "json":return json_encode($data);
            case "xml": return $this->_toXML($data);
            case "csv": return $this->_toCSV($data->Data);
            default: return $data->Data;
        }
    }
    private function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    private function _requestStatus($code) {
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported');
        return ($status[$code])?$status[$code]:$status[500];
    }
    private function _toCSV($array)
    {
                // Notice, you can only use a single character as a delimiter
        $delimiter = ',';

        if (count($array) > 0) {
            // prepare the file
            $fp = fopen('php://temp', 'r+');

            // Save header
            $header = array_keys((array)$array[0]);
            fputcsv($fp, $header);

            // Save data
            foreach ($array as $element) {
                // foreach((array)$element as $prop){
                //     fputcsv($fp, (array)$prop);
                // }
                    fputcsv($fp, (array)$element);
            }
            rewind($fp);
        return stream_get_contents($fp);

        }else{
            return "";
        }
    }
    private function _toXMLInner($array, $node_name) {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key=>$value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }

                $xml .= '<' . $key . '>' . "\n" . $this->_toXMLInner($value, $node_name) . '</' . $key . '>' . "\n";
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES) . "\n";
        }

        return $xml;
    }

    private function _toXML($array, $node_block='ITEMS', $node_name='ITEM') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

        $xml .= '<' . $node_block . '>' . "\n";
        $xml .= $this->_toXMLInner($array, $node_name);
        $xml .= '</' . $node_block . '>' . "\n";

        return $xml;
    }
}
?>
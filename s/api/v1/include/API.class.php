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
            $this->request = cleanInputs($_POST);
            break;
        case 'GET':
            $this->request = cleanInputs($_GET);
            break;
        case 'PUT':
            $this->request = cleanInputs($_GET);
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
        header("HTTP/1.1 " . $status . " " . r($status));
        $encData = encode($data);
        header("Content-Length: ".strlen($encData));
        header("Content-Type: ".getMIME($data->Type));
        return $encData;
    }
    

    
}
?>
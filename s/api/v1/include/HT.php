<?php
	 static $status = array(
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
	function r($code){
        return ($status[$code])?$status[$code]:$status[500];
	}
	function getMIME($name){
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
    function encode($data){
        switch($data->Type)
        {
            case "json":return json_encode($data);
            case "xml": return toXML($data);
            case "csv": return toCSV($data->Data);
            default: return $data->Data;
        }
    }
    function toCSV($array)
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
    function toXMLInner($array, $node_name) {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key=>$value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }

                $xml .= '<' . $key . '>' . "\n" . toXMLInner($value, $node_name) . '</' . $key . '>' . "\n";
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES) . "\n";
        }

        return $xml;
    }

    function toXML($array, $node_block='ITEMS', $node_name='ITEM') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

        $xml .= '<' . $node_block . '>' . "\n";
        $xml .= toXMLInner($array, $node_name);
        $xml .= '</' . $node_block . '>' . "\n";

        return $xml;
    }
    function cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

?>
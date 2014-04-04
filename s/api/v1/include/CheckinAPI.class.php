<?php

require_once 'API.class.php';
require_once 'PEAR.php';
class CheckinAPI extends API
{
    protected function inf()
    {
        return new Response('Success','',phpinfo(),'html');
    }
    protected function getcheckins()
    {
        if ($this->method == 'GET') {
            $res = $this->dp->GetAllCheckins();
            if ($res) {
                    switch(strtolower($this->verb)) {
                        case "csv":
                            return new Response('Success','',$res,'csv');
                        case "xml":
                            return new Response('Success','',$res,'xml');
                        case "json":
                        default:
                            return new Response('Success','',$res,'json');
                    }
                }
            } else {
                return new Response("Error","Only accepts GET requests",'','json',new Error("Invalid HTTP Method","Only accepts Get Requests"));
            }

        return 'No Checkins';
    }
    protected function checkin()
    {
        if ($this->method == 'GET') {
            $this->dp->CheckInStudent($this->verb, $this->args[0]);

            return new Response("Success","User Checked In",'',"json");
        } else {
            return new Response("Failed","Only accepts GET requests",'',"json");
        }
    }
}

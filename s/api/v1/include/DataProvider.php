<?php
require_once 'Zebra_Database.php';

class DataProvider
{
    private $db;

    public function __construct()
    {
        $host = 'mombaCheckin.db.11798615.hostedresource.com';
        $user = 'mombaCheckin';
        $pass = 'Hz2rvcer!#';
        $database = 'mombaCheckin';
        $this->db = new Zebra_Database();
        $this->db->debug = true;
        $this->db->connect($host, $user, $pass, $database);
        $this->db->set_charset();

    }
    public function CheckInStudent($grade, $name)
    {
        $this->db->insert(
            'CheckIns',
            array(
                'id'=>null,
                'grade'=>$grade,
                'name'=>$name
                )
            );

        return $this->db->insert_id();
    }
    public function GetAllCheckins()
    {
        $this->db->select('*', 'CheckIns');
        $res = $this->db->fetch_obj_all();
        return $res;
    }
}

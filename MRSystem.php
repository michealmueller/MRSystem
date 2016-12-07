<?php

/**
 * Created by PhpStorm.
 * User: msqua
 * Date: 12/6/2016
 * Time: 8:13 PM
 */
class mrsystem
{
    public function DBConnect()
    {
        $host = 'localhost';
        $user = 'MRS_root';
        $pass = 'AAdksfK8+dry';
        $DB = 'MRSystem';

        try{
            $mysqli = new mysqli($host, $user, $pass, $DB);
        }catch(Exception $e){
            return array('status'=>'Could not connect to '.$DB.' on '.$host);
        }
        return $mysqli;
    }
    public function register($first_name, $last_name, $reference_number)
    {

    }
    public function Login($username, $password)
    {

    }
    public function EditMember($user_id)
    {

    }
    public function RemoveMember($user_id)
    {

    }
    public function CreateMember()
    {

    }
    public function SelectRandom()
    {

    }
    public function ViewSelected()
    {

    }
    public function Export2PDF()
    {

    }
}
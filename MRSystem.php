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
    public function register($first_name, $last_name, $username, $password, $confirm_password, $email, $role='1', $reference_number='0') //Roles: 1:selector, 2:moderator, 3:Admin
    {
        $mysqli = self::DBConnect();
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO MRSystem.users (user_name, password, email, role) VALUES('.$username.', '.$password.', '.$email.', '.$role.')';
        $mysqli->query($sql);
    }
    public function Login($username, $password)
    {
        //todo::pull password from DB, compare that hash to the normal password with passwrod_verify($password, $return_hash);
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
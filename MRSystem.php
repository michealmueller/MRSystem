<?php

/**
 * Created by PhpStorm.
 * User: msqua
 * Date: 12/6/2016
 * Time: 8:13 PM
 */
//require_once 'dompdf\autoload.inc.php';

class mrsystem
{
    public function DBConnect()
    {
        $host = '107.180.47.62';
        $user = 'mrs_root';
        $testuser = 'root';
        $pass = 'AAdksfK8+dry';
        $testpass = '';
        $DB = 'mrsystem';

        try{
            $mysqli = new mysqli($host, $user, $pass, $DB);
        }catch(Exception $e){
            return array('status'=>'Could not connect to '.$DB.' on '.$host);
        }
        return $mysqli;
    }
    public function register($first_name, $last_name, $username, $password, $email, $role='1', $reference_number='000000000') //Roles: 1:selector, 2:moderator, 3:Admin
    {
        $mysqli = self::DBConnect();
        //escape input cept for password as its going to be hashed anyhow.
        $first_name = $mysqli->escape_string($first_name);
        $last_name = $mysqli->escape_string($last_name);
        $username = $mysqli->escape_string($username);
        $email = $mysqli->escape_string($email);

        $reference_number = '';
        for($i=0;$i<=8; $i++){
            $reference_number .= mt_rand(0,9);
        }

        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO members (first_name, last_name, reference_number, date_created, user_name, password, email, role) VALUES("'.$first_name.'", "'.$last_name.'", "'.$reference_number.'", NOW(), "'.$username.'", "'.$password.'", "'.$email.'", "'.$role.'")';
        if (!$result = $mysqli->query($sql)){
            
            return false;
        }

        $mysqli->close();
        return true;
    }
    public function Login($username, $password)
    {
        $mysqli = self::DBConnect();
        $username = $mysqli->escape_string($username);
        $sql = 'SELECT id, password, role FROM members WHERE user_name="'. $username.'"';
        if(!$result = $mysqli->query($sql)){
            
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        while($row = $result->fetch_assoc()){
            $pass = $row['password'];
            $id = $row['id'];
            $role = $row['role'];
        }
        $verified = password_verify($password, $pass);

        if($verified){
            $mysqli->close();
            return array('user_id' => $id, 'role' => $role);
        }else{
            $mysqli->close();
            return false;
        }
    }
    public function GetMemberInfo($user_id)
    {
        //Reference has to be all numeric!
        $mysqli = self::DBConnect();

        $sql = 'SELECT * FROM members WHERE id="'.$user_id.'"';
        if(!$result = $mysqli->query($sql)){
            
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        $row = $result->fetch_assoc();
        $mysqli->close();
        return $row;
    }

    public function GetMembers($perpage)
    {
        $mysqli = self::DBConnect();

        $sql = 'SELECT * FROM members LIMIT '.$perpage;
        if(!$result = $mysqli->query($sql)){

            die('There was an error running the query [' . $mysqli->error . ']');
        }
        foreach($result as $row)
        {
            $members[] = $row;
        }

        $mysqli->close();
        return $members;
    }

    public function UpdateMemberInfo($user_id, $first_name, $last_name, $reference_number, $role)
    {
        $mysqli = self::DBConnect();

        $first_name = $mysqli->escape_string($first_name);
        $last_name = $mysqli->escape_string($last_name);
        $reference_number = is_numeric($mysqli->escape_string($reference_number));
        $role = is_numeric($mysqli->escape_string($role));

        $sql = 'UPDATE members SET first_name="'.$first_name.'", last_name="'.$last_name.'", reference_number="'.$reference_number.'", role="'.$role.'" WHERE id="'.$user_id.'"';
        if(!$result = $mysqli->query($sql)){
            
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        $mysqli->close();
        return true;
    }

    public function RemoveMember($user_id)
    {
        $mysqli = self::DBConnect();

        $sql = 'DELETE FROM members WHERE id="'.$user_id.'"';
        if(!$result = $mysqli->query($sql)){
            
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        $mysqli->close();
        return true;

    }

    public function CreateMember($first_name, $last_name, $username, $password, $confirm_password, $email, $role='1', $reference_number='0')
    {
        $mysqli = self::DBConnect();
        //escape input except for password as its going to be hashed anyhow.
        $first_name = $mysqli->escape_string($first_name);
        $last_name = $mysqli->escape_string($last_name);
        $username = $mysqli->escape_string($username);
        $email = $mysqli->escape_string($email);

        $reference_number = '';
        for($i=0;$i<=8; $i++){
            $reference_number .= mt_rand(0,9);
        }

        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO members (first_name, last_name, reference_number, date_created, user_name, password, email, role) VALUES("'.$first_name.'", "'.$last_name.'", "'.$reference_number.'", NOW(), "'.$username.'", "'.$password.'", "'.$email.'", "'.$role.'"';
        if (!$result = $mysqli->query($sql)){
            $mysqli->close();
            return false;
        }
        $mysqli->close();
        return true;
    }

    public function GetRandom($num = 4)
    {
        $mysqli = self::DBConnect();

        $count = 'SELECT COUNT(*) FROM members';
        if(!$memcount = $mysqli->query($count)){
            
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        //check if the entered number is able to be fetched.
        foreach($memcount as $item){
            $members = $item;
        }
        if($num > $members){
            
            die('you must go back and select a number equal to or less then '.$memcount);
        }

        $sql = 'SELECT * 
                FROM members
                WHERE date_selected IS NULL 
                ORDER BY RAND( ) 
                LIMIT '.$num;
		if(!$results = $mysqli->query($sql)){
            
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        foreach($results as $row)
        {
            $selected[] = $row;
        }
        $mysqli->close();
		return $selected;
    }
    public function ViewSelected()
    {
        $mysqli = self::DBConnect();
        $sql = 'SELECT * FROM members WHERE date_selected !=null';
        if($results = $mysqli->query($sql)){
            
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        foreach($results as $row){
            $selected[] = $row;
        }
        $mysqli->close();
        return $selected;
    }
    public function Export2PDF($selected, $from)
    {
        $dompdf = new \Dompdf\Dompdf();

        if($from === 'viewexport'){
            $dompdf->loadHtmlFile('export.html');
            $dompdf->render();
        }elseif($from === 'modexport')
        {

        }else{
            return false;
        }
    }
    public function Pagination()
    {
        $mysqli = self::DBConnect();
        $sql = 'SELECT COUNT(*) FROM members';
        //get the record count
        try {
            $rowCount = $mysqli->query($sql);
        }catch(Exception $e)
        {
            die('There was an error! '.$e .'<br><br>' .$mysqli->error);
        }
        //math to figure out how many pages is required for pagination
        $rowCount = $rowCount->fetch_row();
        $perPage = 50;
        $pages = $rowCount[0] / $perPage;
        if($pages < 1)
        {
            $pages = 1;
        }

        return array('pages'=> $pages, 'perpage' => $perPage);
    }
}
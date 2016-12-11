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
        $user = 'mrs_root';
        $testuser = 'root';
        $pass = 'AAdksfK8+dry';
        $testpass = '';
        $DB = 'MRSystem';

        try{
            $mysqli = new mysqli($host, $testuser, $testpass, $DB);
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
        $sql = 'INSERT INTO users (user_name, password, email, role) VALUES("'.$username.'", "'.$password.'", "'.$email.'", '.$role.')';
        if ($mysqli->query($sql)){
            $last_id = $mysqli->insert_id;
            $sql2 = 'INSERT INTO members (user_id, first_name, last_name, reference_number, date_created) VALUES("'.$last_id.'", "'.$first_name.'", "'.$last_name.'", "'.$reference_number.'", NOW())';
            if($mysqli->query($sql2)){
                $mysqli->close();
                return true;
            }
        }
        $mysqli->close();
        return false;
    }
    public function Login($username, $password)
    {
    	$mysqli = self::DBConnect();

        $username = $mysqli->escape_string($username);

    	$sql = 'SELECT password FROM users WHERE user_name="'. $username.'"';
    	if(!$result = $mysqli->query($sql)){
            $mysqli->close();
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        while($row = $result->fetch_assoc()){
    	    $pass = $row['password'];
        }
    	$verified = password_verify($password, $pass);

    	$sql2 = 'SELECT id, role FROM users WHERE user_name="'. $username.'"';
    	if($verified){
    	    $result2 = $mysqli->query($sql2);
    	    while($row = $result2->fetch_assoc()){
    	        $role = $row['role'];
    	        $id = $row['id'];
            }
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

        $sql = 'SELECT * FROM members WHERE user_id="'.$user_id.'"';
        if(!$result = $mysqli->query($sql)){
            $mysqli->close();
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        $row = $result->fetch_assoc();
            $sql2 = 'SELECT role FROM users WHERE id="'.$user_id.'"';
        if(!$result2 = $mysqli->query($sql2)){
            $mysqli->close();
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        $row2 = $result2->fetch_assoc();
        $row['role'] = $row2['role'];

        $mysqli->close();
        return $row;
    }

    public function UpdateMemberInfo($user_id, $first_name, $last_name, $reference_number, $role)
    {
        $mysqli = self::DBConnect();

        $first_name = $mysqli->escape_string($first_name);
        $last_name = $mysqli->escape_string($last_name);
        $reference_number = is_numeric($mysqli->escape_string($reference_number));
        $role = is_numeric($mysqli->escape_string($role));

        $sql = 'UPDATE members SET first_name="'.$first_name.'", last_name="'.$last_name.'", reference_number="'.$reference_number.'" WHERE user_id="'.$user_id.'"';
        if(!$result = $mysqli->query($sql)){
            $mysqli->close();
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        $sql2 = 'UPDATE users SET role="'.$role.'"';
        if(!$result2 = $mysqli->query($sql2)){
            $mysqli->close();
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        $mysqli->close();
        return true;
    }

    public function RemoveMember($user_id)
    {
        $mysqli = self::DBConnect();

        $sql = 'DELETE FROM members WHERE user_id="'.$user_id.'"';
        if(!$result = $mysqli->query($sql)){
            $mysqli->close();
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        $sql2 = 'DELETE FROM users WHERE id="'.$user_id.'"';
        if(!$result2 = $mysqli->query($sql2)){
            $mysqli->close();
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
        $sql = 'INSERT INTO users (user_name, password, email, role) VALUES("'.$username.'", "'.$password.'", "'.$email.'", '.$role.')';
        if ($mysqli->query($sql)){
            $last_id = $mysqli->insert_id;
            $sql2 = 'INSERT INTO members (user_id, first_name, last_name, reference_number, date_created) VALUES("'.$last_id.'", "'.$first_name.'", "'.$last_name.'", "'.$reference_number.'", NOW())';
            if($mysqli->query($sql2)){
                $mysqli->close();
                return true;
            }
        }
        $mysqli->close();
        return false;
    }

    public function GetRandom($num = 3)
    {
        $mysqli = self::DBConnect();

        $count = 'SELECT COUNT(*) FROM members';
        if(!$memcount = $mysqli->query($count)){
            $mysqli->close();
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        //check if the entered number is able to be fetched.
        foreach($memcount as $item){
            $members = $item;
        }
        if($num > $members){
            $mysqli->close();
            die('you must go back and select a number equal to or less then '.$memcount);
        }

        $sql = 'SELECT * FROM members ORDER BY RAND() LIMIT '.$num;
		if(!$results = $mysqli->query($sql)){
            $mysqli->close();
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        foreach($results as $row)
        {
            if ($row['date_selected'] == null)
            {
                $selected[] = $row;
            }else{
                //need to figure out how to tell if its been 1 month since last selection.
            }
        }
        $mysqli->close();
		return $selected;
    }
    public function ViewSelected()
    {
        $mysqli = self::DBConnect();
        $sql = 'SELECT * FROM members WHERE date_selected !=null';
        if($results = $mysqli->query($sql)){
            $mysqli->close();
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
        if($from === 'viewexport')
        {

        }elseif($from === 'modexport')
        {

        }else{
            return false;
        }
    }
}
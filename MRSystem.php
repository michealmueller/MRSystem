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
    public function Register($personel_number, $first_name, $middle_name, $last_name, $ssn, $job_location, $manager, $hr_rep, $field_admin, $drug_pool, $excluded, $role='1') //Roles: 1:user, 2:moderator, 3:Admin
    {
        $mysqli = self::DBConnect();
        //escape input
        $personel_number = $mysqli->escape_string($personel_number);
        $first_name = $mysqli->escape_string($first_name);
        $middle_name = $mysqli->escape_string($middle_name);
        $last_name = $mysqli->escape_string($last_name);
        $ssn = $mysqli->escape_string($ssn);
        $job_location = $mysqli->escape_string($job_location);
        $manager = $mysqli->escape_string($manager);
        $hr_rep = $mysqli->escape_string($hr_rep);
        $field_admin = $mysqli->escape_string($field_admin);
        $drug_pool = $mysqli->escape_string($drug_pool);

        $sql = 'INSERT INTO users (personel_number, first_name, middle_name, last_name, ssn, job_location, manager, 
                  hr_rep, field_admin, drug_pool, excluded, role) VALUES("'.$personel_number.'","'.$first_name.'","'.$middle_name.'","'.$last_name.'","'. $ssn.'","'.$job_location.'","'.$manager.'","'.$hr_rep.'","'.$field_admin.'","'.$drug_pool.'","'.$excluded.'","'.$role.'")';
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
        $sql = 'SELECT id, password, role FROM users WHERE user_name="'. $username.'"';
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

            return array('user_id' => $id, 'role' => $role);
        }else{
            $_SESSION['status'] = 1;
            return false;
        }
    }
    public function Getmembers($perpage)
    {
        $mysqli = self::DBConnect();

        $sql = 'SELECT * FROM users LIMIT '.$perpage;
        if(!$result = $mysqli->query($sql)){

            die('There was an error running the query [' . $mysqli->error . ']');
        }
        foreach($result as $row)
        {
            $users[] = $row;
        }

        $mysqli->close();
        return $users;
    }

    public function RemoveMember($user_id)
    {
        $mysqli = self::DBConnect();

        $sql = 'DELETE FROM users WHERE id="'.$user_id.'"';
        if(!$result = $mysqli->query($sql)){
            
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        $mysqli->close();
        return true;

    }

    public function GetRandom($num = 4)
    {
        $mysqli = self::DBConnect();

        $count = 'SELECT COUNT(*) 
                  FROM users 
                  WHERE date_selected 
                  IS NOT null 
                  AND role = 1';
        if(!$memcount = $mysqli->query($count)){
            
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        //check if the entered number is able to be fetched.
        foreach($memcount as $item){
            $users = $item;
        }
        if($num > $users){
            
            die('you must go back and select a number equal to or less then '.$memcount);
        }

        $sql = 'SELECT * 
                FROM users
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
		return $selected;
    }
    public function MarkSelected($id)
    {
        $mysqli = self::DBConnect();
        $sql = 'UPDATE users 
                SET date_selected=NOW() 
                WHERE id='.$id;
        if(!$result = $mysqli->query($sql)){
            die('Could Not Mark Users as Selected. [' . $mysqli->error . ']');
        }
        return true;
    }

    public function ViewSelected()
    {
        $mysqli = self::DBConnect();
        $sql = 'SELECT * FROM users WHERE date_selected IS NOT null';
        if(!$results = $mysqli->query($sql)){
            
            die('There was an error running the query [' . $mysqli->error . ']');
        }
        foreach($results as $row){
            $selected[] = $row;
        }
        return $selected;
    }
    public function Export2PDF($selected)
    {
        $dompdf = new Dompdf();
        $options = new \Dompdf\Options();
        $options->set('isHTML5ParserEnabled', true);
        $dompdf->loadHtmlFile('export.php');
        $dompdf->render();
        $dompdf->stream();

        return true;

    }
    public function Pagination()
    {
        $mysqli = self::DBConnect();
        $sql = 'SELECT COUNT(*) FROM users';
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

    public function Import($array)
    {
        if(is_array($array))
        {
            foreach($array as $item){
                $exploded[] = explode($item, ' ');
                //todo:: figure this shit out lol
            }
        }
    }
}
<?php

/**
 * Created by   PhpStorm.
 * User:        Micheal Mueller - MuellerTek
 * Web:         http://www.MuellerTek.com
 * Date:        4/05/2017
 * Time:        8:55 AM
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
    public function Register_Admin($username, $password, $role=2)
    {
        $mysqli = self::DBConnect();
        //escape input
        $username= $mysqli->escape_string($username);
        $password = password_hash($password, PASSWORD_BCRYPT);

        $sql = 'INSERT INTO admin (username, password, role) VALUES("'.$username.'","'.$password.'","'.$role.'")';
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
        $sql = 'SELECT id, password, role FROM admin WHERE username="'. $username.'"';
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
    public function GetMemberInfo($user_id, $role)
    {
        //Reference has to be all numeric!
        $mysqli = self::DBConnect();

        if($role == 3){
            $sql = 'SELECT * FROM admin WHERE id="'.$user_id.'"';
        }else{
            $sql = 'SELECT * FROM users WHERE id="'.$user_id.'"';
        }

        if(!$result = $mysqli->query($sql)){

            die('There was an error running the query [' . $mysqli->error . ']');
        }
        $row = $result->fetch_assoc();
        $mysqli->close();
        return $row;
    }
    public function Getmembers($perpage, $pool='enter_default_pool')
    {
        $mysqli = self::DBConnect();

        if($pool !== 'enter_default_pool')
        {
            $sql = 'SELECT * FROM users WHERE drug_pool="'.$pool.'"';
        }else{
            $sql = 'SELECT * FROM users LIMIT '.$perpage;
        }
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

    public function GetRandom($num = 4, $pool='enter_default_pool')
    {
        $mysqli = self::DBConnect();

        $count = 'SELECT COUNT(*) 
                  FROM users 
                  WHERE excluded = 0 AND role = 1';
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
                WHERE excluded = 0 AND drug_pool="'.$pool.'" LIMIT '.$num;
		if(!$results = $mysqli->query($sql)){
            
            die('There was an error running this query [' . $mysqli->error . ']');
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
        //$options = new \Dompdf\Options();
        //$options->set('isHTML5ParserEnabled', true);
        $dompdf->loadHtml($_SESSION['content']);
        $dompdf->setPaper('A4', 'Landscape');
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

    public function Import($file, $deletall=true)
    {
        $records = array();
        //rad the file line by line to get each record
        $file = new SplFileObject($file);
        while (!$file->eof()){
            $contents[] = $file->fgets();
        }
        //sanity check
        if(isset($contents)){
            //explode each record with delimiter
            foreach ($contents as $content) {
                $records[] = explode(',', $content);
            }
            //drop the first array as its the fields.
            unset($records[0]);

        }else{
            die('there was an issue with reading the file.');
        }
        //filter each array
        $mysqli = self::DBConnect();
        $sql = 'INSERT INTO users (personel_number, first_name, middle_name, last_name, ssn, job_location, manager, 
                hr_rep, field_admin, drug_pool, excluded, role)
                VALUES';
        foreach ($records as $record) {
            $sql_array[] = '("'.$mysqli->escape_string($record[0]).'","'.$mysqli->escape_string($record[1]).'",
            "'.$mysqli->escape_string($record[2]).'","'.$mysqli->escape_string($record[3]).'","'.$mysqli->escape_string($record[4]).'",
            "'.$mysqli->escape_string($record[5]).'","'.$mysqli->escape_string($record[6]).'",
                "'.$mysqli->escape_string($record[7]).'","'.$mysqli->escape_string($record[8]).'","'.$mysqli->escape_string($record[9]).'",0, 1)';
        }
        //implode to glue multi insert together
        $sql = $sql . implode(',', $sql_array);
        //clean the input some
        $sql = str_replace("\r\n",'', $sql);
        $sql = str_replace("\\r\\n",'', $sql);
        //$sql = str_replace("@", '-at-', $sql);
        $sql = trim(preg_replace('/\s+/', ' ', $sql));
        $sql = trim(preg_replace('/\s/', ' ', $sql));
        if($deletall == true){
            $mysqli->query('TRUNCATE TABLE users');
        }
        if(!$result = $mysqli->query($sql)){
            return false;
        }else{
            return true;
        }
    }
    public function GetPools()
    {
        $mysqli = self::DBConnect();
        $sql = "SELECT DISTINCT drug_pool FROM users";

        if(!$result = $mysqli->query($sql)){
            return false;
        }
        foreach($result as $row){
            $pools[] = $row;
        }
        return $pools;

    }
}
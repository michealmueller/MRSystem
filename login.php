<?php
/**
 * Created by   PhpStorm.
 * User:        Micheal Mueller - MuellerTek
 * Web:         http://www.MuellerTek.com
 * Date:        12/10/2016
 * Time:        5:31 PM
 */
require_once 'MRSystem.php';

$mrs = new mrsystem();
if($_POST['token'])
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    $Loggedin = $mrs->Login($username, $password);
    $user_id = $Loggedin['user_id'];
    $user_role = $Loggedin['role'];

    if($Loggedin == false){
        header('Location: /login.html');
    }else{
        echo '<h3>Welcome, ' . $username . ' You Are Now Logged In.</h3>';
        echo 'Would you like to edit user <a href="edit.php?user_id='.$user_id.'">'.$username.'</a> ?';
    }
}
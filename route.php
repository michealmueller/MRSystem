<?php
/**
 * Created by   PhpStorm.
 * User:        Micheal Mueller - MuellerTek
 * Web:         http://www.MuellerTek.com
 * Date:        12/10/2016
 * Time:        9:25 PM
 */

require_once 'MRSystem.php';

$mrs = new mrsystem();

if ($_POST['formtype'] === 'reg-form'){
    $username = $_POST['username'];
    if($_POST['confirm_password'] === $_POST['password'])
    {
        $password = $_POST['password'];
    }else{
        $confirm_password = $_POST['confirm_password'];
        $password = $_POST['password'];
    }
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    if($_POST['token'])
    {
        $reg = $mrs->register($first_name, $last_name, $username,$password, $email);
        if ($reg){
            header('Location: login.html');
        }
    }
}elseif ($_POST['formtype'] === 'login-form'){
    if($_POST['token'])
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $Loggedin = $mrs->Login($username, $password);

        if($Loggedin == false){
            header('Location: /login.html');
        }else{
            echo '<h3>Welcome,' . $username . 'You Are Now Logged In.</h3>';
        }
    }
}elseif ($_POST['formtype'] === 'edit-form' && $_POST['delete'] == 'DELETE'){
    $mrs->RemoveMember($_POST['user_id']);
}else{
    echo 'Something is REALLY WRONG! ';
}
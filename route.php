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
            echo '<h3>Welcome,' . $username . 'You Are Now Logged In.</h3><br>';
            echo 'Not only is the username returned here but the role and user_id is as well, <br>Would you like to edit user <a href="edit.php?user_id='.$Loggedin['user_id'].'">'.$username.'</a> ?<br>Select Random Users - (for demonstration i have 6 records in the DB so this will randomly select 3)';
            echo '<br><br>';
            echo '<form role="form" action="route.php" method="post" autocomplete="off">
        <input type="submit" name="selectRandom" class="btn btn-primary" value="Select Random Users">
        <input type="hidden" id="rand-form" name="formtype" value="rand-form">
    </form>';
        }
    }
}elseif ($_POST['formtype'] === 'edit-form' && $_POST['delete'] == 'DELETE'){
    $mrs->RemoveMember($_POST['user_id']);
}elseif($_POST['formtype'] == 'edit-form' && $_POST['update'] == 'Update'){
    $mrs->UpdateMemberInfo($_POST['user_id'], $_POST['first_name'], $_POST['last_name'], $_POST['reference_number'], $_POST['role']);
}elseif($_POST['formtype'] == 'rand-form'){
    $rand = $mrs->GetRandom();
    foreach($rand as $random)
    {
        echo '<br>';
        echo 'First name: '.$random['first_name'].' Last Name: '.$random['last_name'];
        echo '<br>';
    }
}
else{
    echo 'Something is REALLY WRONG! ';
}
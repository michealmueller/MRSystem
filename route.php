<?php
    session_start();
/**
 * Created by   PhpStorm.
 * User:        Micheal Mueller - MuellerTek
 * Web:         http://www.MuellerTek.com
 * Date:        12/10/2016
 * Time:        9:25 PM
 */

require_once 'MRSystem.php';

$mrs = new mrsystem();

if (isset($_POST['formtype']) && $_POST['formtype'] === 'select-form'){
    if(isset($_SESSION['rand-users'])){
        foreach($_SESSION['rand-users'] as $user) {
            $update = $mrs->MarkSelected($user['id']);
        }
        if(isset($update) && $update == true){
            header("Location: export.php");
        }
    }
}elseif (isset($_POST['formtype']) && $_POST['formtype'] === 'edit-form' && $_POST['delete'] == 'DELETE'){
    $mrs->RemoveMember($_POST['user_id']);
}elseif(isset($_POST['formtype']) && $_POST['formtype'] == 'edit-form' && $_POST['update'] == 'Update'){
    $mrs->UpdateMemberInfo($_POST['user_id'], $_POST['first_name'], $_POST['last_name'], $_POST['reference_number'], $_POST['role']);
}elseif((isset($_POST['formtype'])) && $_POST['formtype'] === 'reg-form'){
    if($mrs->CreateMember($_POST['first_name'], $_POST['last_name'], $_POST['reference_number'],$_POST['role'])) {
        header("Location: index.php");
    }
}elseif((isset($_POST['formtype'])) && $_POST['formtype'] === 'manager-reg-form'){
    if($mrs->CreateMember($_POST['first_name'], $_POST['last_name'], $_POST['user_name'], $_POST['password'], $_POST['reference_number'])) {
        header("Location: index.php");
    }
}elseif($_GET['logout'] == 1){
    session_destroy();
    header('Location: login.php');
}elseif($_GET['deluser'] = 1){
    if($mrs->RemoveMember($_GET['user_id'])){
        header("Location: index.php");
    }
}elseif($_GET['export'] == 1){
    if($mrs->Export2PDF($_SESSION['user_info'])){
        echo 'You PDF is Downloading.';
    }else{
        die('Could not export to pdf - contact developer or web admin.');
    }
}
else{
    echo 'Something is REALLY WRONG! ';
}
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
}elseif(isset($_POST['formtype']) && $_POST['formtype'] === 'changepool'){
    header("Location: index.php?pool=".$_POST['pool']);
}elseif (isset($_POST['formtype']) && $_POST['formtype'] === 'edit-form' && $_POST['delete'] == 'DELETE'){
    $mrs->RemoveMember($_POST['user_id']);
}elseif(isset($_POST['formtype']) && $_POST['formtype'] === 'reg-admin'){
    if($mrs->Register_Admin($_POST['user_name'], $_POST['password'], $_POST['role'])) {
        header("Location: index.php");
    }
}elseif(isset($_GET['logout']) && $_GET['logout'] == 1){
    session_destroy();
    header('Location: login.php');
}elseif(isset($_GET['deluser']) && $_GET['deluser'] = 1){
    if($mrs->RemoveMember($_GET['user_id'])){
        header("Location: index.php");
    }
}elseif(isset($_GET['export']) && $_GET['export'] == 1){
    if($mrs->Export2PDF($_SESSION['user_info'])){
        echo 'You PDF is Downloading.';
    }else{
        die('Could not export to pdf - contact developer or web admin.');
    }
}elseif(isset($_GET['import']) && $_GET['import'] == 1)
{
    if(isset($_POST['deleteall']) && $_POST['deleteall'] == 'checked')
    {
        $deleteall = true;
    }else{
        $deleteall = false;
    }
    $mrs->Import($_POST['import'],$deleteall);

}
else{
    echo 'Something is REALLY WRONG! ';
}

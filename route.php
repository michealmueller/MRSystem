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
}elseif(isset($_POST['formtype']) && $_POST['formtype'] === 'manager-reg-form'){
    if($mrs->CreateMember($_POST['first_name'], $_POST['last_name'], $_POST['user_name'], $_POST['password'], $_POST['reference_number'])) {
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
    //check for directory, if it does not exist, create it.
    if(!$exists = file_exists('uploads')){
        mkdir('uploads');
    }

    $errors     =   array();
    $file_name  =   $_FILES['image']['name'];
    $file_size  =   $_FILES['image']['size'];
    $file_tmp   =   $_FILES['image']['tmp_name'];
    $file_type  =   $_FILES['image']['type'];
    $file_ext   =   strtolower(end(explode('.',$_FILES['image']['name'])));

    $expensions= array('csv','txt');

    if(in_array($file_ext,$expensions)=== false){
        $errors[] = "extension not allowed, Only .csv and .txt files.";
    }
    if(empty($errors)){
        move_uploaded_file($file_tmp, 'uploads/'.$file_name);
        $mrs->Import(file('uploads/'.$file_name));
    }else{
        foreach ($errors as $e){
            echo $e . ', ';
        }
        echo '<br><h3><a href="import.php">Go Back</a></h3>';
    }

    $mrs->Import($fileArray);
}
else{
    echo 'Something is REALLY WRONG! ';
}
<?php
session_start();
$user_id = $_GET['user_id'];

require_once 'MRSystem.php';

$mrs = new mrsystem();

$user = $mrs->GetMemberInfo($user_id, $_SESSION['user_info']['role']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <form role="form" action="route.php" method="post" autocomplete="off">
        <?php
        if ($user['role'] === 3){
            echo '
                <input class="form-control" name="user_id" type="hidden" value="'.$_GET['user_id'].'" tabindex="1" >
        <div class="form-group">
            <label for="first-name">
                <strong>First Name:</strong>
            </label>
            <input class="form-control" name="username" type="text" value="'.$user['username'].'" tabindex="2">
        </div>
        <div class="form-group">
            <label for="last-name">
                    <strong>Last Name:</strong>
            </label>
            <input class="form-control" name="role" type="number" value="'.$user['role'].'" tabindex="3">
        </div>
        <div class="form-group">
            <label for="reference_number">
                <strong>Reference Number:</strong>
            </label>
            <input class="form-control" name="reference_number" type="number" value="'.$user['reference_number'].'" tabindex="4">
        </div>
        <div class="text-center">
            <input type="submit" name="update" class="btn btn-success" value="Update" tabindex="8">
            <input type="submit" name="delete" class="btn btn-danger" value="DELETE" tabindex="9">
        </div>
        <input type="hidden" id="token" name="token" value="3u4s649ekh4of0q372ppob2lhl9b06vdmoin">
        <input type="hidden" id="reg-form" name="formtype" value="admin-edit-form">\';
            ';
        }else{
            echo '<input class="form-control" name="user_id" type="hidden" value="'.$_GET['user_id'].'" tabindex="1" >
        <div class="form-group">
            <label for="first-name">
                <strong>First Name:</strong>
            </label>
            <input class="form-control" name="first_name" type="text" value="'.$user['first_name'].'" tabindex="2">
        </div>
        <div class="form-group">
            <label for="last-name">
                    <strong>Last Name:</strong>
            </label>
            <input class="form-control" name="last_name" type="text" value="'.$user['last_name'].'" tabindex="3">
        </div>
        <div class="form-group">
            <label for="reference_number">
                <strong>Reference Number:</strong>
            </label>
            <input class="form-control" name="reference_number" type="number" value="'.$user['reference_number'].'" tabindex="4">
        </div>
        <div class="form-group">
            <label for="role">
                <strong>Role:</strong>
            </label>
            <input class="form-control" name="role" type="text" value="'.$user['role'].'" tabindex="5">
        </div>

        <div class="form-group">
            <label for="date_created">
                <strong>Date Created:</strong>
            </label>
            <input class="form-control" name="date_created" type="text" value="'.$user['date_created'].'" tabindex="6" disabled>
        </div>
        <div class="form-group">
            <label for="date_selected">
                <strong>Date Selected:</strong>
            </label>
            <input class="form-control" name="date_selected" type="text" value="'.$user['date_selected'].'" tabindex="7" disabled>
        </div>
        <div class="text-center">
            <input type="submit" name="update" class="btn btn-success" value="Update" tabindex="8">
            <input type="submit" name="delete" class="btn btn-danger" value="DELETE" tabindex="9">
        </div>
        <input type="hidden" id="token" name="token" value="3u4s649ekh4of0q372ppob2lhl9b06vdmoin">
        <input type="hidden" id="reg-form" name="formtype" value="edit-form">';
        }
        ?>

    </form>
</div>
</body>
</html>
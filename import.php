<?php
    require_once 'MRSystem.php';
    /**
     * Created by   PhpStorm.
     * User:        Micheal Mueller - MuellerTek
     * Web:         http://www.MuellerTek.com
     * Date:        12/18/2016
     * Time:        8:19 PM
     */

    session_start();
    $mrs = new mrsystem();


    if($_SESSION['user_info']['status'] !== 1 || $_SESSION['user_info']['role'] < 2)
    {
        header("Location: index.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>C3 Intelligence, Inc. - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="css/instascreen.css">
    <link rel="stylesheet" type="text/css" href="css/version2.css">
    <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.9.2.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.9.2-overrides.css">

    <link rel="stylesheet" type="text/css" href="css/version2_5.css">
    <link rel="stylesheet" type="text/css" href="css/is-bootstrap-overrides.css">
    <link rel="stylesheet" type="text/css" href="css/is-layout.css">
    <link rel="stylesheet" type="text/css" href="css/is-responsive.css">
    <link rel="stylesheet" type="text/css" href="css/skin-default.css">
    <link rel="stylesheet" type="text/css" href="css/theme.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-color-secondary.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/animate.css">
    <link rel="stylesheet" type="text/css" media="print" href="css/is-printable.css">

    <link href="css/googlefonts.css?family=Roboto:400,400italic,500,500italic,700,700italic" rel="stylesheet" type="text/css">
    <link href="css/googlefonts_roboto_condensed.css?family=Roboto+Condensed:400,400italic,500,500italic,700,700italic" rel="stylesheet" type="text/css">
    <link href="css/font-awesome.css" rel="stylesheet" type="text/css">


    <link rel="stylesheet" type="text/css" href="css/da_net_skin.css">

</head>

<body onload="onloadExecute()" onunload="onunloadExecute()">
<div class="container-fluid">
    <div class="col-md-6">
        <p><h3>Welcome <?php echo $_SESSION['user_info']['username']; ?> You Are Now Logged In.</h3></p>
    </div>
    <div class="col-md-6">
        <p class="pull-right">
            <a href="createMember.php"><button class=" btn-success">Create New Member</button></a>
            <a href="createManager.php"><button class=" btn-success">Create New Manager</button></a>
            <a href="import.php"><button class=" btn-warning">Import Users</button></a>
            <a href="edit.php?user_id=<?php echo $_SESSION['user_info']['id'] ?>"><button class="btn-warning">Edit Account</button></a>
            <a href="route.php?logout=1"><button class="btn-danger">Logout</button></a>
        </p>
    </div>
    <div class="col-md-12">
        <hr>
    </div>
    <div class="col-md-12">
        <form role="form" action="route.php?import=1" method="post" autocomplete="off" class="form-inline">
            <h3><label for="import" >Select File</label></h3>
            <input type="file" id="fileImport" name="import" class="form-control">
            <input type="submit" class="btn btn-primary" id="Import" name="Import" value="Import">
        </form>
    </div>
</div>

</body>
</html>
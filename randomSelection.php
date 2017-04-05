<?php
    require_once 'MRSystem.php';
    $mrs = new mrsystem();
    session_start();
    ob_start();
    /**
     * Created by   PhpStorm.
     * User:        Micheal Mueller - MuellerTek
     * Web:         http://www.MuellerTek.com
     * Date:        12/12/2016
     * Time:        2:55 PM
     */
    $_SESSION['rand-num'] = $_POST['rand-num'];
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
<div class="container">
    <div class="col-md-6">
        <p><h3>Welcome <?php echo $_SESSION['user_info']['username']; ?> You Are Now Logged In.</h3></p>
    </div>
    <div class="col-md-6">
        <p class="pull-right">
            <a href="createMember.php"><button class=" btn-success">Create New Member</button></a>
            <a href="createuser.php"><button class=" btn-success">Create New Manager</button></a>
            <a href="edit.php?user_id=<?php echo $_SESSION['user_info']['id'] ?>"><button class="btn-warning">Edit Account</button></a>
            <a href="route.php?logout=1"><button class="btn-danger">Logout</button></a>
        </p>
    </div>
    <hr>
    <div class="col-md-4" align="center">
        <form name="" role="form" action="randomSelection.php" method="post" autocomplete="off" class="form-inline">
            <label for="rand-num" >Select</label>
            <input type="number" id="rand-num" name="rand-num" class="form-control input-sm" value="<?php if(isset($_SESSION['rand-num'])){echo $_SESSION['rand-num'];}else{echo '4';} ?>">
            <label for="rand-num" >Users</label>
            <input type="submit" name="selectRandom" class="btn btn-primary " value="Random Users">
        </form>
    </div>
    <div class="col-md-4" align="center">
        <form name="selectusers" role="form" action="route.php" method="post" autocomplete="off" class="form-inline">
            <p>Select These <?php echo $_SESSION['rand-num'] ?> users?</p>
            <input type="hidden" id="select-form" name="formtype" value="select-form">
            <input type="submit" name="selectGroup" class="btn btn-success" value="Select Users">
        </form>
    </div>
    <div class="col-md-4" align="center">
        <a href="export.php?export=1"><button class="btn btn-warning">Export to PDF</button></a>
    </div>
    <div class="col-md-12 table-responsive">
        <table class="table table-hover">
            <tr>
                <th>Personel Number</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>LastName</th>
                <th>SSN</th>
                <th>Job Location</th>
                <th>Manager</th>
                <th>HR Rep</th>
                <th>Field Admin</th>
                <th>Drug Pool</th>
            </tr>
        <?php

            $rand = $mrs->GetRandom($_SESSION['rand-num'], $_SESSION['selected_pool']);
            $_SESSION['rand-users'] = $rand;
        foreach ($rand as $random)
        {
            echo '<tr>';
            echo '<td>';
            echo $random['personel_number'];
            echo '</td>';
            echo '<td>';
            echo $random['first_name'];
            echo '</td>';
            echo '<td>';
            echo $random['middle_name'];
            echo '</td>';
            echo '<td>';
            echo $random['last_name'];
            echo '</td>';
            echo '<td>';
            echo $random['ssn'];
            echo '</td>';
            echo '<td>';
            echo $random['job_location'];
            echo '</td>';
            echo '<td>';
            echo $random['manager'];
            echo '</td>';
            echo '<td>';
            echo $random['hr_rep'];
            echo '</td>';
            echo '<td>';
            echo $random['field_admin'];
            echo '</td>';
            echo '<td>';
            echo $random['drug_pool'];
            echo '</td>';
            echo '</tr>';

        }
        ?>
        </table>
    </div>
</div>
</body>
</html>
<?php
    $contents = ob_get_clean();
    $_SESSION['content'] = $contents;
    echo $_SESSION['content'];
<?php
    require_once 'MRSystem.php';
    /**
     * Created by   PhpStorm.
     * User:        Micheal Mueller - MuellerTek
     * Web:         http://www.MuellerTek.com
     * Date:        12/12/2016
     * Time:        8:55 AM
     */
session_start();

$mrs = new mrsystem();

if($_POST['token']) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $Loggedin = $mrs->Login($username, $password);
    if ($Loggedin == false) {
        header('Location: login.html');
    } else {
        $_SESSION['user_info'] = array(
            'username'  => $username,
            'id'        => $Loggedin['user_id'],
            'role'      => $Loggedin['role'],
            'status'    => 1,
        );
    }
}

if($_SESSION['user_info']['status'] !== 1)
{
    header("Location: login.php");
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
        <p class="pull-right"><a href="edit.php?user_id=<?php echo $_SESSION['user_info']['id'] ?>"><button class="btn-warning">Edit Account</button></a></p>
    </div>
        <hr>
    <div class="col-md-4 " align="center">
        <p>Select Random Users</p>
        <form role="form" action="randomSelection.php" method="post" autocomplete="off" class="form-inline">
            <label for="rand-num" >Select</label>
            <input type="number" id="rand-num" name="rand-num" class="form-control" value="4">
            <label for="rand-num" >Users.</label>
            <input type="submit" name="selectRandom" class="btn btn-primary" value="Select Random Users">
            <input type="hidden" id="rand-form" name="formtype" value="rand-form">
        </form>
    </div>
    <div class="col-md-4 pull-right">
        <ul class="pagination">
        <?php
            $pagination = $mrs->Pagination();
            for($i=1;$i<=$pagination['pages'];$i++)
            {
                echo '<li><a href="#">'.$i.'</a></li>';
            }
        ?>
        </ul>
    </div>
    <div class="col-md-12 table-responsive">
        <table class="table table-hover">
            <tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>LastName</th>
                <th>Reference Number</th>
                <th>Date Last Selected</th>
            </tr>
            <?php
            $members = $mrs->GetMembers($pagination['perpage']);
            foreach ($members as $member)
            {
                echo '<tr>';
                    echo '<td>';
                    echo $member['id'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['first_name'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['last_name'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['reference_number'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['date_selected'];
                    echo '</td>';
                echo '</tr>';

            }
            ?>
        </table>
    </div>

</div>
</body>
</html>
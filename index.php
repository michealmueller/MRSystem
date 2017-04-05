<?php
    require_once 'MRSystem.php';
    date_default_timezone_set('UTC');
    /**
     * Created by   PhpStorm.
     * User:        Micheal Mueller - MuellerTek
     * Web:         http://www.MuellerTek.com
     * Date:        12/12/2016
     * Time:        8:55 AM
     */
session_start();
ob_start();
$mrs = new mrsystem();
if(isset($_POST['token'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login = $mrs->Login($username, $password);
    if ($login == false) {
        header('Location: login.php');
    } else {
        $_SESSION['user_info'] = array(
            'username'  => $username,
            'id'        => $login['user_id'],
            'role'      => $login['role'],
            'status'    => 1,
        );
    }
}elseif(isset($_SESSION['user_info']['status']) && $_SESSION['user_info']['status'] !== 1){
    header('Location: login.php');
}
if(isset($_GET['pool'])){
    $_SESSION['selected_pool'] = $_GET['pool'];
}
/*
if($_SESSION['user_info']['status'] !== 1)
{
    header("Location: login.php");
}*/
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
        <p><h3>Welcome <?php if(isset($_SESSION['user_info']['username'])){echo $_SESSION['user_info']['username'];}else{echo 'Admin';} ?> You Are Now Logged In.</h3></p>
    </div>
    <div class="col-md-6">
        <p class="pull-right">
            <a href="createMember.php"><button class=" btn-success">Create New Member</button></a>
            <a href="import.php"><button class=" btn-warning">Import Users</button></a>
            <a href="route.php?logout=1"><button class="btn-danger">Logout</button></a>
        </p>
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
    <form method="post" action="route.php">
        <select name="pool">
            <?php
            $pools = $mrs->GetPools();

            foreach ($pools as $pool) {
                echo '<option value="'.$pool['drug_pool'].'">'.$pool['drug_pool'].'</option>';
            }

            ?>
        </select>
        <input id="change_pool" type="submit" name="change_pool" value="Change Pool"> <!-- do some JS to refresh the page with the format of /index?pool=$_POST['pool']-->
        <input type="hidden" name="formtype" value="changepool">
    </form>
    <div class="col-md-4 pull-right">
        <ul class="pagination">
        <?php
            $pagination = $mrs->Pagination();
        ?>
            <a href="index.php?viewSelected=1&export=1"><button class="btn btn-primary">Export Selected Members</button></a>
        </ul>
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
                <th>Modify</th>
            </tr>
            <?php
                if(isset($_GET['viewSelected']) && $_GET['viewSelected'] == 1){
                    $members = $mrs->ViewSelected();
                }else{
                    if(isset($_GET['pool'])){
                        $pool = $_GET['pool'];
                    }else{
                        $pool = 'enter_default_pool';
                    }
                    $members = $mrs->GetMembers($pagination['perpage'], $pool);
                }

            foreach ($members as $member)
            {
                echo '<tr>';
                    echo '<td>';
                    echo $member['personel_number'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['first_name'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['middle_name'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['last_name'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['ssn'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['job_location'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['manager'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['hr_rep'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['field_admin'];
                    echo '</td>';
                    echo '<td>';
                    echo $member['drug_pool'];
                    echo '</td>';
                    echo '<td>';
                    echo '<div><a href="edit.php?user_id='.$member['id'].'"><div class="glyphicon glyphicon-edit"></div></a> &nbsp;|&nbsp; <a href="route.php?deluser=1&user_id='.$member['id'].'"><div class="glyphicon glyphicon-remove"></div></a></div>';
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
    $_SESSION['content'] = ob_get_contents();
    $contentOrig = ob_get_clean();

    require_once 'lib/html2pdf/vendor/autoload.php';

    if(isset($_GET['export']) && $_GET['export'] == 1) {


        preg_match_all('|(.*table-responsive">)([\s\S]*)|xi', $_SESSION['content'], $output);
        $content = '<html><body><div class="container">'.$output[0][0];


        $_SESSION['content'] = $content;

        $html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en');
        $html2pdf->setDefaultFont('courier');
        $html2pdf->writeHTML($_SESSION['content']);
        if($html2pdf->Output('Selected_Users.pdf','D') == true){
            header('Location: index.php');
        }
    }

    echo $contentOrig;
?>
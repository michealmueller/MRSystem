<?php
/**
 * Created by   PhpStorm.
 * User:        Micheal Mueller - MuellerTek
 * Web:         http://www.MuellerTek.com
 * Date:        12/10/2016
 * Time:        5:31 PM
 */
require_once 'MRSystem.php';
?>
<html>
<head>

</head>
<body>
<form role="form" action="login.php" method="post" autocomplete="off">
  <div class="form-group">
    <label for="l-name">
      <strong>Username:</strong>
    </label>
    <span class="pull-right">
      <small><a class="text-muted" href="reset.html">Forgot Username?</a></small>
    </span>
    <input id="username" name="username" class="form-control" type="text" tabindex="1" maxlength="48" value="">
  </div>
  <div class="form-group">
    <label for="l-pass">
      <strong>Password:</strong>
    </label>
    <span class="pull-right">
      <small><a class="text-muted" href="reset.html">Forgot Password?</a></small>
    </span>
    <input id="password" name="password" class="form-control" type="password" tabindex="2" maxlength="32">
  </div>
  <div class="text-center">
    <input type="submit" class="btn btn-primary" value="Login" tabindex="4">
  </div>
    <input type="hidden" id="token" name="token" value="3u4s649ekh4of0q372ppob2lhl9b06vdmoin">
</form>
<?php
$MRS = new mrsystem();
if($_POST['token'])
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    $Loggedin = $MRS->Login($username, $password);

    if(!$Loggedin){
        header('Location: /login.html');
    }else{
        echo '<h3>Welcome,' . $username . 'You Are Now Logged In.</h3>';
    }
}
?>
</body>
</html>

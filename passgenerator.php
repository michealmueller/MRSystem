<?php
    /**
     * Created by   PhpStorm.
     * User:        Micheal Mueller - MuellerTek
     * Web:         http://www.MuellerTek.com
     * Date:        12/13/2016
     * Time:        4:11 PM
     */

    echo 'To use this generator add this to the end of the url <b>?pass=</b> , then after the = sign type your password out and hit enter, it will then display the hashed version.<br><br>';
    if(isset($_GET['pass']) && $_GET['pass'] !== ''){
        $password = password_hash($_GET['pass'], PASSWORD_DEFAULT);
        echo $password;
    }

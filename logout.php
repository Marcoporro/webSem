<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 04/12/2016
 * Time: 22:47
 */
    session_start();
    session_unset();
    session_destroy();
    header("location: main.php");
    exit();
?>
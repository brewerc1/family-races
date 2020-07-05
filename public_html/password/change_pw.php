<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php');


if (isset($_POST["change_pwd"])) {
    header("Location: /password/reset.php?");
}
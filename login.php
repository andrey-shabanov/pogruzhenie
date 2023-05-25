<?php
session_start();

require_once 'functions.php';

$email = !empty($_POST['email']) ? trim($_POST['email']) : '';
$password = !empty($_POST['password']) ? trim($_POST['password']) : '';

if (login($email, $password)){
    redirectTo('users.php');
}else{
    redirectTo('page_login.php');
}
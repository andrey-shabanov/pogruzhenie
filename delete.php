<?php
session_start();
require_once 'functions.php';


$user_id = $editUserId = $_GET['id'];

if (deleteUser($user_id)){
    if ($user_id == $_SESSION['user']['id']){
        unset($_SESSION);
        redirectTo('page_register.php');
    }else {
        redirectTo('users.php');
        setFlashMessage('success', 'Пользователь удалён!');
    }
}

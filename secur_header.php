<?php
session_start();

require_once 'functions.php';


$editUserId = $_GET['id'];
$email = $_POST['email'];
$password = $_POST['password'];


    if (isset($_POST['submit']) && (!empty($email)) && (!empty($password))) {
        if (editCredentials($editUserId, $email, $password)) {
            setFlashMessage('success', 'Профиль успешно обновлён!!!');
            redirectTo("page_profile.php?id=$editUserId");
        } else {
            setFlashMessage('danger', 'Данный эл. адрес занят!!!');
            redirectTo("security.php?id=$editUserId");
        }
    }else{
        redirectTo("security.php?id=$editUserId");
        setFlashMessage('danger', 'Вводимые поля не могут быть пустыми!!!');
        redirectTo("security.php?id=$editUserId");
    }


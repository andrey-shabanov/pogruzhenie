<?php
session_start();

require_once 'functions.php';

$email = !empty($_POST['email']) ? trim($_POST['email']) : '';
$password = !empty($_POST['password']) ? trim($_POST['password']) : '';


if (!empty(getUserByEmail($email))) {
    setFlashMessage('danger','<strong>Уведомление!</strong> Этот эл. адрес уже занят другим пользователем.');
    redirectTo('page_register.php');
    exit();
} else {
    addUser($email, $password);
    setFlashMessage('success','Регистрация прошла успешно!');
    redirectTo('page_login.php');
    exit();
}









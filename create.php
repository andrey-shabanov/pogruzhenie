<?php
session_start();

require_once 'functions.php';

$email = $_POST['email'];
$password = $_POST['password'];


if (empty($email) || empty($password))
{
    setFlashMessage('danger', 'Поля логин/пароль обязательны');
    redirectTo('create_user.php');
    exit;
}

if (getUserByEmail($email)){
    setFlashMessage('danger', 'Ошибка! Такой пользователь уже существует!');
    redirectTo('create_user.php');
    exit();
}

$user_id = addUser($email,$password);

editProfile($user_id);

uploadAvatar($user_id);

setStatus($user_id);
addSocialLinks($user_id);

setFlashMessage('success', 'Пользователь добавлен!');

redirectTo('users.php');


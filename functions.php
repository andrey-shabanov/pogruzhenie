<?php
session_start();

function getUserByEmail($email){

    $options = [
      PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ];

    $pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8', 'root', 'root', $options);
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $email = $stmt->fetch();
    return $email;
    }

function addUser($email, $password){
    $options = [
      PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ];
    $role = 'user';
    $pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8', 'root', 'root', $options);
    $stmt = $pdo->prepare('INSERT INTO users (email, pass, role) VALUES (?, ?, ?)');
    $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $role]);
    $id = $pdo->lastInsertId();
    return $id;
}

function setFlashMessage($name, $message){
    $_SESSION[$name] = $message;

}

function displayFlashMessage($name){
    echo $_SESSION[$name];
    unset($_SESSION[$name]);
}

function redirectTo($path){
    header("location: $path");
}

function login($email, $password):bool
{
    if (empty($email) || empty($password))
    {
        setFlashMessage('danger', 'Поля логин/пароль обязательны');
        return false;
    }

    $options =[
      PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ];

    $pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8', 'root', 'root', $options);
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if (!$user = $stmt->fetch()){

        setFlashMessage('danger', 'Данный пользователь не зарегистрирован!');
        return false;
    }

    if (!password_verify($password, $user['pass'])){
        setFlashMessage('danger', 'Введённые почта/пароль не верны!');
        return false;
    }else{

        $_SESSION['user']['email'] = $user['email'];
        $_SESSION['user']['role'] = $user['role'];
        $_SESSION['user']['id'] = $user['id'];

        return true;
    }
}

function getAllUsers(){

    $options = [
      PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ];

    $pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8', 'root', 'root', $options);
    $stmt = $pdo->prepare('SELECT * FROM users');
    $stmt->execute();
    $allUser = $stmt->fetchAll();
    return $allUser;
}

function isNotLoggedIn():bool
{
if (empty($_SESSION['user']['email']) && empty($_SESSION['user']['id'])){
    setFlashMessage('danger', 'Вы не авторизованы');
    return true;
}else{
    return false;
}
}

function isAdmin():bool
{
    if (!strcmp($_SESSION['user']['role'], "admin")){
        return true;
    }else{
        return false;
    }
}

function editProfile($user_id):bool
{
    $options = [
      PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ];
    $pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8','root', 'root', $options);
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $id = $stmt->fetchColumn();
    if ($id == $user_id) {
        $stmt = $pdo->prepare("UPDATE `users` SET `username` = '{$_POST['username']}', `job_title` = '{$_POST['job_title']}', `phone` = '{$_POST['phone']}', `address` = '{$_POST['address']}' WHERE id = ?");
        $stmt->execute([$user_id]);
    }
    return true;
}

function uploadAvatar($user_id)
{
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    $pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8', 'root', 'root', $options);
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $base = $stmt->fetch();
    if (!empty($base['picture']) && $base['id'] == $user_id) {
        file_exists('uploads/' . $base['picture']);
        unlink('uploads/' . $base['picture']);}
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $file_name = (uniqid() . '.' . $ext);
        $path = 'uploads/' . $file_name;
        move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $file_name);
        $stmt = $pdo->prepare("UPDATE `users` SET `picture` = '{$file_name}' WHERE id = ?");
        $stmt->execute([$user_id]);
        return $path;
}

function setStatus($user_id){
    $status = $_POST['status'];

$options = [
  PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
];
$pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8', 'root', 'root', $options);
$stmt = $pdo->prepare("UPDATE `users` SET `status` = '{$status}' WHERE id = ?");
$stmt->execute([$user_id]);

return true;
}

function addSocialLinks($user_id)
{
    $telegram = $_POST['telegram'];
    $vk = $_POST['vk'];
    $instagram = $_POST['instagram'];

    $pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8', 'root', 'root');
    $stmt = $pdo->prepare("UPDATE `users` SET `telegram` = '{$telegram}', `vk` = '{$vk}', `instagram` = '{$instagram}' WHERE id = ? ");
    $stmt->execute([$user_id]);
}

function isAuthor($user_id, $editUserId):bool
{
    if ($user_id == $editUserId){
        return true;
    }else{
        return false;
    }
}

function getUserById($editUserId)
{
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];
    $pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8', 'root', 'root', $options);
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$editUserId]);
    $user = $stmt->fetch();
    return $user;
}

function editCredentials($editUserId, $email, $password):bool
{
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    $pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8', 'root', 'root', $options);
$user = getUserById($editUserId);

if (($user['email'] == $email) && ($user['id'] == $editUserId)){
    $pass = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE `users` SET `pass` = '{$pass}' WHERE id = ?");
        $stmt->execute([$editUserId]);
        return true;
}elseif ((!getUserByEmail($email)) && ($user['id'] == $editUserId)){

    $stmt = $pdo->prepare("UPDATE `users` SET `email` = '{$email}', `pass` = '{$password}' WHERE id = ?");
       $stmt->execute([$editUserId]);
       return true;
}else {
        return false;
}
}

function editAvatar($user_id):bool
{
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    $pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8', 'root', 'root', $options);
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $base = $stmt->fetch();

    if (!empty($base['picture']) && $base['id'] == $user_id) {
        if (file_exists('uploads/' . $base['picture'])){
        unlink('uploads/' . $base['picture']);}
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $ext;
        $link = 'uploads/' . $file_name;
        move_uploaded_file($_FILES['file']['tmp_name'], $link);
        $stmt = $pdo->prepare("UPDATE `users` SET `picture` = '{$file_name}' WHERE id = ?");
        $stmt->execute([$user_id]);

        return true;
    }
    if(empty($base['picture']) && $base['id'] == $user_id)
    {
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $ext;
        $link = 'uploads/' . $file_name;
        move_uploaded_file($_FILES['file']['tmp_name'], $link);
        $stmt = $pdo->prepare("UPDATE `users` SET `picture` = '{$file_name}' WHERE id = ?");
        $stmt->execute([$user_id]);
        return true;
    }else{
        return false;
    }
}

function deleteUser($user_id):bool
{
    $options = [
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ];

    $pdo = new PDO('mysql:host=localhost;dbname=my_project_1;charset=utf8', 'root', 'root', $options);
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
if ($user['id'] == $user_id){
    file_exists('uploads/' . $user['picture']);
        unlink('uploads/' . $user['picture']);
        $stmt = $pdo->prepare('DELETE FROM users WHERE id =?');
        $stmt->execute([$user_id]);
        return true;
}
}





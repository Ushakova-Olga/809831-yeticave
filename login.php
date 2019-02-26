<?php
require_once('functions.php');
$is_auth =0;
$user_name = '';
$is_auth = 0;
$user_name = '';

session_start();
if (isset($_SESSION['user'])){
    $u = $_SESSION['user'];
    $is_auth = 1;
    $user_name = $u['name'];
}

$categories = [];
$error = '';

$con = mysqli_connect("localhost", "root", "", "yeticave");
mysqli_set_charset($con, "utf8");

if(!$con) {
    $error="Ошибка подключения: " . mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
} else {
    $sql= "SELECT c.id, c.name FROM categories c";
    $result = mysqli_query($con, $sql);
    if(!$result) {
        $error= mysqli_error($con);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $categories= mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') { //форма была отправлена
        $user_form = $_POST;

        $required = ['email', 'password'];
        $dict = ['email' => 'E-mail', 'password' => 'Пароль'];
        $errors = [];
        foreach ($required as $field) {
            if (empty($user_form[$field])) {
                $errors[$field] = 'Это поле надо заполнить';
            }
        }

        $email = mysqli_real_escape_string($con, $user_form['email']);
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $res = mysqli_query($con, $sql);

        $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

        if (!count($errors) and $user) {
            if (password_verify($user_form['password'], $user['password'])) {
                $_SESSION['user'] = $user;
                $user_name = $user['name'];
                $is_auth = 1;
            }
            else {
                $errors['password'] = 'Вы ввели неверный пароль';
            }
        }
        else {
            $errors['email'] = 'Вы ввели неверный email';
        }

        if (count($errors)) {
            $page_content = include_template('login.php', ['categories' => $categories, 'user' => $user_form, 'errors' => $errors, 'dict' => $dict]);
        }
        else {
            header("Location: index.php");
            exit();
        }
    }
    else { // форма не была отправлена
        if (isset($_SESSION['user'])) { // сессия уже есть
            header("Location: index.php");
            exit();
        }
        else { // нет сессии
            $page_content = include_template('login.php', ['categories' => $categories]);
        }
    }
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'name_page' => 'Вход на сайт',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);

?>

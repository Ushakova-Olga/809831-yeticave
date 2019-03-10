<?php
// Добавление нового лота
require_once('functions.php');
require_once('init.php');

$is_auth = 0;
$user_name = '';
$path = '';

session_start();
if (isset($_SESSION['user'])){
    $is_auth = 1;
    $user_name = $_SESSION['user']['name'];
    $user_id = $_SESSION['user']['id'];
}

$categories = [];
$error = '';
$link = init();
$file_exist = 0;
$tmp_name = '';


// получаем категории для отрисовки на странице
if(!$link) {
    $error="Ошибка подключения: " . mysqli_connect_error();
} else {
    $sql= "SELECT c.id, c.name FROM categories c";
    $result = mysqli_query($link, $sql);
    if(!$result) {
        $error= mysqli_error($link);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $categories= mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

// работа с данными формы если она была отправлена
if (($_SERVER['REQUEST_METHOD'] === 'POST')&&($error === '')) {
    $user = $_POST;
    $required = ['email', 'password', 'name', 'message'];
    $dict = ['email' => 'E-mail', 'password' => 'Пароль', 'name' => 'Имя',
        'message'=>'Контактные данные', 'file'=>'Аватар'];
    $errors = [];
    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (isset($_FILES['photo2']['name'])) {
        if (!empty($_FILES['photo2']['name'])) {
            $file_exist = 1;
        }
    }

    if ($file_exist) {
        $tmp_name = $_FILES['photo2']['tmp_name'];
        $path = $_FILES['photo2']['name'];
        $file_type = mime_content_type($tmp_name);

        switch ($file_type) {
            case "image/jpeg":
                $path = uniqid() . ".jpg";
                break;
            case "image/png":
                $path = uniqid() . ".png";
                break;
            default:
                break;
        }
        if (($file_type !== "image/jpeg")&&($file_type !== "image/png")) {
            $errors['file'] = 'Загрузите картинку в формате GPEG, либо PNG';
        }
    }

    if (isset($user['name'])) {
         if (strlen($user['name']) > 255) {
             $errors['name'] = 'Имя пользователя не должно быть более 255 символов';
         }
    }

    if (isset($user['password'])) {
         if (strlen($user['password']) > 255) {
             $errors['password'] = 'Пароль пользователя не должен быть более 255 символов';
         }
    }

    if (isset($user['message'])) {
         if (strlen($user['message']) > 255) {
             $errors['message'] = 'Контактные данные не должны быть более 255 символов';
         }
    }

    if (isset($user['email'])) {
        $email = mysqli_real_escape_string($link, $user['email']);
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $res = mysqli_query($link, $sql);

        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            $errors['email'] = 'Введен некорректный e-mail';
         }

         if (strlen($email) > 255) {
             $errors['email'] = 'Введенный слишком длинный e-mail';
         }
     } else {
         $errors['email'] = 'email не заполнен';
     }

    if (count($errors)) {// если есть ошибки заполнения формы
        $page_content = include_template('sign-in.php', ['categories' => $categories, 'user' => $user, 'errors' => $errors, 'dict' => $dict]);
    }
    else {//ошибок заполнения формы нет
        if ($file_exist) {
            // перемещаем изображение в папку 'img/'
            move_uploaded_file($tmp_name, 'img/' . $path);
        }
        // добавляем пользователя
        $password = password_hash($user['password'], PASSWORD_DEFAULT);
        //Создание нового пользователя
        $sql = 'INSERT INTO users (date_add, email, name, password, img_url, contacts) VALUES (NOW(), ?, ?, ?, ?, ?)';
        if ($path!='') {
            $path = 'img/' . $path;
        }
        $stmt = db_get_prepare_stmt($link, $sql, [$user['email'], $user['name'], $password, $path, $user['message']]);
        $res = mysqli_stmt_execute($stmt);

        // переход на страницу входа
        if ($res) {
            header("Location: login.php");
            exit();
        } else {
            $error= mysqli_error($link);
        }
    }
}
else {// форма не была отправлена, просто отображаем страницу
    $page_content = include_template('sign-in.php', ['categories' => $categories]);
}

$name_page ='Добавление пользователя';

if($error) {
    $page_content = include_template('error.php', ['error' => $error]);
    $name_page="Yeticave - Ошибка";
}

$layout_content = include_template('layout.php', [
	'content' => $page_content,
	'categories' => $categories,
	'name_page' => $name_page,
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
?>

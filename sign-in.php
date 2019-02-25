<?php
// Добавление нового лота
require_once('functions.php');
//$is_auth = rand(0, 1);
//$user_name = 'Ольга'; // укажите здесь ваше имя

$is_auth = 0;
$user_name = '';
$path = '';

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

// получаем категории для отрисовки на странице
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

    // работа с данными формы если она была отправлена
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            $tmp_name = $_FILES['photo2']['tmp_name'];
            $path = $_FILES['photo2']['name'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $tmp_name);
            if (($file_type !== "image/jpeg")&&($file_type !== "image/png")) {
                $errors['file'] = 'Загрузите картинку в формате GPEG, либо PNG';
            }
            else {
                if ($file_type == "image/jpeg") $path = uniqid() . ".jpg";
                if ($file_type == "image/png") $path = uniqid() . ".png";
                move_uploaded_file($tmp_name, 'img/' . $path);
                $user['path'] = $path;
            }
        } //else// {
            //$errors['file'] = 'Вы не загрузили файл';
         // }
      } //else {
        //$errors['file'] = 'Вы не загрузили файл';
      //}

      $email = mysqli_real_escape_string($con, $user['email']);
      $sql = "SELECT id FROM users WHERE email = '$email'";
      $res = mysqli_query($con, $sql);

      if (mysqli_num_rows($res) > 0) {
          $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
      }

      if (!filter_var($email, FILTER_VALIDATE_EMAIL) ) {
          $errors['email'] = 'Введен некорректный e-mail';
      }

      if (count($errors)) {// если есть ошибки заполнения формы
        $page_content = include_template('sign-in.php', ['categories' => $categories, 'user' => $user, 'errors' => $errors, 'dict' => $dict]);
      }
      else {//ошибок заполнения формы нет, добавляем пользователя
          $password = password_hash($user['password'], PASSWORD_DEFAULT);
          //Создание нового пользователя
          $sql = 'INSERT INTO users (date_add, email, name, password, img_url, contacts) VALUES (NOW(), ?, ?, ?, ?, ?)';
          if ($path!='') {
            $path = 'img/' . $path;
          }
          $stmt = db_get_prepare_stmt($con, $sql, [$user['email'], $user['name'], $password, $path, $user['message']]);
          $res = mysqli_stmt_execute($stmt);

          // переход на страницу входа
          if ($res) {
            //$user_id = mysqli_insert_id($con);
            header("Location: login.php");
            exit();
          } else {
            $page_content = include_template('error.php', ['error' => mysqli_error($con)]);
          }
      }
    }
    else {// форма не была отправлена, просто отображаем страницу
        $page_content = include_template('sign-in.php', ['categories' => $categories]);
    }
}

$layout_content = include_template('layout.php', [
	'content' => $page_content,
	'categories' => $categories,
	'name_page' => 'Добавление пользователя',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
?>

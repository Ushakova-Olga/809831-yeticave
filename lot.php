<?php
require_once('functions.php');
//$is_auth = rand(0, 1);
//$user_name = 'Ольга'; // укажите здесь ваше имя

$is_auth = 0;
$user_name = '';
$user_id='';
session_start();
if (isset($_SESSION['user'])){
    $u = $_SESSION['user'];
    $is_auth = 1;
    $user_name = $u['name'];
    $user_id = $u['id'];

}

$categories = [];
$name_page='';
/////
$rate['cost']= '';
$errors = '';
$dict = '';

$error = '';
$con = mysqli_connect("localhost", "root", "", "yeticave");
mysqli_set_charset($con, "utf8");
$var_404 = 0;

if (!ISSET($_GET['id'])){
    // В запросе отсутствует наш id
    $var_404 = 1;
}

if ($var_404 == 0) {
    //Преобразуем к числу, чтобы избежать SQL- инъекций
    $id= intval($_GET['id']);

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
    }

    if(!$con) {
        $error="Ошибка подключения: " . mysqli_connect_error();
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $sql= "SELECT l.id id, l.name name, l.description description, c.name category, GREATEST(IFNULL(MAX(r.summ),0),l.start_price) price, l.img_url url, l.step step  FROM lots l
        JOIN users u ON u.id=l.user_author_id
        JOIN categories c ON c.id=l.category_id
        LEFT JOIN rates r ON r.lot_id=l.id
        WHERE l.id='$id'
        GROUP BY l.id";

        $result = mysqli_query($con, $sql);
        if(!$result) {
            $error= mysqli_error($con);
            $page_content = include_template('error.php', ['error' => $error]);
        } else {
            $lot_data= mysqli_fetch_array($result, MYSQLI_ASSOC);
            if (count($lot_data) == 0) $var_404 = 1;
        }
    }
}

if ($var_404 == 0) {
    if(!$con) {
        $error="Ошибка подключения: " . mysqli_connect_error();
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $sql= "SELECT r.id id, r.summ summ, u.name name, r.date_add date_add  FROM rates r
        JOIN users u ON r.user_id=u.id
        WHERE r.lot_id='$id'
        ORDER BY r.date_add DESC
        LIMIT 10";

        $result = mysqli_query($con, $sql);
        if(!$result) {
            $error= mysqli_error($con);
            $page_content = include_template('error.php', ['error' => $error]);
        } else {
            $rates_data= mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
}

if ($var_404 == 1) {
    http_response_code(404);
    $page_content = include_template('404.php', [
        'categories' => $categories, 'error' => '404'
    ]);
}

/* Если нет никаких ошибок, то показываем обычную страницу */
if (($error == '')&&($var_404 == 0)) {
      // работа с данными формы по добавлению ставок, если она была отправлена и если пользователь залогинен
      if (($_SERVER['REQUEST_METHOD'] == 'POST')&&($is_auth)){
        $rate = $_POST;
        $required = ['cost'];
        $dict = ['cost' => 'Сумма ставки'];
        $errors = [];
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле надо заполнить';
            }
        }

      if (!is_numeric($_POST['cost'])) {
        $errors['cost'] = 'Сумма ставки должна быть числом';
      } else if ($_POST['cost'] <= 0) {
        $errors['cost'] = 'Сумма ставки должна быть больше нуля';
      } else if ($_POST['cost'] < ($lot_data['price']+$lot_data['step'])) {
        $errors['cost'] = 'Сумма ставки должна быть больше текущей + шаг торгов';
      }
        if (count($errors)) {// если есть ошибки заполнения формы
          $page_content = include_template('lot.php', [
              'categories' => $categories,
              'lot' => $lot_data,
              'rates' => $rates_data,
              'cost' => $rate['cost'],
              'errors' => $errors,
              'dict' => $dict
          ]);
        }
        else {//ошибок заполнения формы нет
          $sql = 'INSERT INTO rates (date_add, summ, user_id, lot_id) VALUES (NOW(), ?, ?, ?)';
          $stmt = db_get_prepare_stmt($con, $sql, [$rate['cost'], $user_id, $id]);
          $res = mysqli_stmt_execute($stmt);

          // Добавили ставку и тепрерь нужно обновить страницу
          if ($res) {
            header("Location: lot.php?id=" . $id . "update=1");
          }
          else {
            $page_content = include_template('error.php', ['error' => mysqli_error($con)]);
          }
        }
    } else {
        //форма не была отправлена, ошибки =0
        $page_content = include_template('lot.php', [
            'categories' => $categories,
            'lot' => $lot_data,
            'rates' => $rates_data,
            'cost' => [],
            'errors' => [],
            'dict' => [],
            'is_auth' => $is_auth
        ]);
    }


    $name_page=$lot_data['name'];

} else {
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

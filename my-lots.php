<?php
require_once('functions.php');

$is_auth = 0;
$user_name = '';
$user_id='';
$my_rates_data= [];
session_start();

if (isset($_SESSION['user'])){
    $u = $_SESSION['user'];
    $is_auth = 1;
    $user_name = $u['name'];
    $user_id = $u['id'];
}

$categories = [];
$name_page='';
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

  $sql= "SELECT MAX(r.id) id, MAX(r.summ) summ, u.contacts contacts, u.name author_name, MAX(r.date_add) date_add, c.name category, l.user_victor_id victor, l.name lot_name, l.img_url url, l.id lot_id, l.date_end date_end   FROM rates r
  JOIN lots l ON r.lot_id=l.id
  JOIN categories c ON l.category_id=c.id
  JOIN users u ON l.user_author_id = u.id
  WHERE r.user_id='$user_id'
  GROUP BY r.lot_id
  ORDER BY date_add DESC
  LIMIT 100";

  $result = mysqli_query($con, $sql);
  if(!$result) {
      $error= mysqli_error($con);
      $page_content = include_template('error.php', ['error' => $error]);
  } else {
      $my_rates_data= mysqli_fetch_all($result, MYSQLI_ASSOC);
  }
}

/* Если нет никаких ошибок, то показываем страницу */
if ($error == '') {
  $page_content = include_template('my-lots.php', [
    'categories' => $categories,
    'rates' => $my_rates_data,
    'is_auth' => $is_auth,
    'user_id' => $user_id
    ]);

    $name_page="Мои ставки";
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

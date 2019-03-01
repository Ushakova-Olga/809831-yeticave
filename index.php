<?php
require_once('functions.php');
require_once('victory.php');
require_once('init.php');

$is_auth = 0;
$user_name = '';

session_start();

if (isset($_SESSION['user'])){
    $is_auth = 1;
    $user_name = $_SESSION['user']['name'];
    $user_id = $_SESSION['user']['id'];
}

$categories = [];
$lots_list = [];
$error = '';
$link = init();

if(!$link) {
    $error="Ошибка подключения: " . mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
} else {
    $sql= "SELECT l.id id, l.name name, l.date_end, c.name category, COUNT(r.summ) amount, GREATEST(IFNULL(MAX(r.summ),0),l.start_price) price, l.img_url url   FROM lots l
    JOIN users u ON u.id=l.user_author_id
    JOIN categories c ON c.id=l.category_id
    LEFT JOIN rates r ON r.lot_id=l.id
    WHERE CURRENT_TIMESTAMP < l.date_end
    GROUP BY l.id
    ORDER BY l.date_add DESC
    LIMIT 9";
    $result = mysqli_query($link, $sql);

    if(!$result) {
        $error= mysqli_error($link);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $lots_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    $sql= "SELECT c.id, c.name FROM categories c";
    $result = mysqli_query($link, $sql);
    if(!$result) {
        $error= mysqli_error($link);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $categories= mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

/* Если нет никаких ошибок, то показываем обычную страницу */
if ($error === '') {
    $page_content = include_template('index.php', [
        'categories' => $categories,
        'lots_list' => $lots_list
    ]);
}

$layout_content = include_template('layout.php', [
	'content' => $page_content,
	'categories' => $categories,
	'name_page' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

//вызов ф-ии определения победителей
victory();
print($layout_content);

?>

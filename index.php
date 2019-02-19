<?php
require_once('functions.php');
$is_auth = rand(0, 1);

$user_name = 'Ольга'; // укажите здесь ваше имя
$categories = [];
$lots_list = [];

function format_rub($arg)
{
    $result = ceil($arg);
    if ($result > 999) {
        $result = number_format($result,0,'',' ');
    }
    $result = $result . " ₽";
    return $result;
}

$error = '';
$con = mysqli_connect("localhost", "root", "", "yeticave");
mysqli_set_charset($con, "utf8");

if(!$con) {
    $error="Ошибка подключения: " . mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
} else {
    $sql= "SELECT l.id id, l.name name, c.name category, GREATEST(IFNULL(MAX(r.summ),0),l.start_price) price, l.img_url url   FROM lots l
    JOIN users u ON u.id=l.user_author_id
    JOIN categories c ON c.id=l.category_id
    LEFT JOIN rates r ON r.lot_id=l.id
    WHERE l.user_victor_id IS NULL
    GROUP BY l.id
    ORDER BY l.date_add DESC
    LIMIT 9";
    $result = mysqli_query($con, $sql);

    if(!$result) {
        $error= mysqli_error($con);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $lots_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    $sql= "SELECT c.name FROM categories c";
    $result = mysqli_query($con, $sql);
    if(!$result) {
        $error= mysqli_error($con);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $categories= mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

/* Если нет никаких ошибок, то показываем обычную страницу */
if ($error == '') {
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

print($layout_content);
?>

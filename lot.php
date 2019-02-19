<?php
require_once('functions.php');
$is_auth = rand(0, 1);

$user_name = 'Ольга'; // укажите здесь ваше имя
$categories = [];

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
        $sql= "SELECT c.name FROM categories c";
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
        'categories' => $categories
    ]);
}

/* Если нет никаких ошибок, то показываем обычную страницу */
if (($error == '')&&($var_404 == 0)) {
    $page_content = include_template('lot.php', [
        'categories' => $categories,
        'lot' => $lot_data,
        'rates' => $rates_data
    ]);
}

$layout_content = include_template('layout.php', [
	'content' => $page_content,
	'categories' => $categories,
	'name_page' => 'DC Ply Mens 2016/2017 Snowboard',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
?>

<?php
require_once('functions.php');

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
$category = [];
$current_page = 1;
$var_404 = 0;
$cat = 0;

$error = '';
$link = mysqli_connect("localhost", "root", "", "yeticave");
mysqli_set_charset($link, "utf8");

if (!ISSET($_GET['category'])){
    // В запросе отсутствует category
    $var_404 = 1;
} else {
    $cat = intval($_GET['category']);
};

if (ISSET($_GET['page'])) {
    $current_page = intval($_GET['page']);
    if ($current_page <= 0 ) {
        $current_page = 1;
    }
};

if(!$link) {
    $error="Ошибка подключения: " . mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
} else {
    $sql= "SELECT c.id, c.name FROM categories c";
    $result = mysqli_query($link, $sql);
    if(!$result) {
        $error= mysqli_error($link);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $categories= mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    $sql= "SELECT c.id, c.name FROM categories c WHERE c.id='$cat'";
    $result = mysqli_query($link, $sql);
    if(!$result) {
        $error= mysqli_error($link);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $category= mysqli_fetch_array($result, MYSQLI_ASSOC);
        if (count($category) == 0) {
            //category указанная в запросе отсутствует в базе данных
            $var_404 = 1;
        }
    }
}

if ($var_404 === 1) {
    http_response_code(404);
    $page_content = include_template('404.php', [
        'categories' => $categories, 'error' => '404'
    ]);
}

if (($error === '')&&($var_404 == 0)) {
    $page_items = 9;

    $sql= "SELECT COUNT(*) as cnt FROM lots l
    WHERE l.category_id ='$cat' AND CURRENT_TIMESTAMP < l.date_end";

    $result = mysqli_query($link, $sql);
    $items_count = mysqli_fetch_assoc($result)['cnt'];

    $pages_count = ceil($items_count / $page_items);
    $offset = ($current_page - 1) * $page_items;

    $pages = range(1, $pages_count);

    $sql= 'SELECT l.id id, l.name name, c.name category, l.date_end, COUNT(r.summ) amount, GREATEST(IFNULL(MAX(r.summ),0),l.start_price) price, l.img_url url   FROM lots l
    JOIN users u ON u.id=l.user_author_id
    JOIN categories c ON c.id=l.category_id
    LEFT JOIN rates r ON r.lot_id=l.id
    WHERE l.category_id =' . $cat . ' AND CURRENT_TIMESTAMP < l.date_end
    GROUP BY l.id
    ORDER BY l.date_add DESC
    LIMIT ' . $page_items . ' OFFSET ' . $offset;
    $result = mysqli_query($link, $sql);

    if(!$result) {
        $error= mysqli_error($link);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $lots_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

if ($error) {
    $page_content = include_template('error.php', ['error' => $error]);
    $name_page = "Yeticave - Ошибка";
}
/* Если нет никаких ошибок, то показываем обычную страницу */
if (($error === '')&&($var_404 === 0)) {
    $page_content = include_template('all-lots.php', [
        'category' => $category,
        'categories' => $categories,
        'lots_list' => $lots_list,
        'pages' => $pages,
        'pages_count' => $pages_count,
        'current_page' => $current_page
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'name_page' => 'Все лоты',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
?>

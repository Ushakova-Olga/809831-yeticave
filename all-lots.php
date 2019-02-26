<?php
require_once('functions.php');
// Пагинацию сделала по свуему усмотрению, переделаю после след лекции когда расскажут про это
$is_auth = 0;
$user_name = '';

session_start();
if (isset($_SESSION['user'])){
    $u = $_SESSION['user'];
    $is_auth = 1;
    $user_name = $u['name'];
}

$categories = [];
$lots_list = [];
$category = [];
$p = '';

$error = '';
$con = mysqli_connect("localhost", "root", "", "yeticave");
mysqli_set_charset($con, "utf8");

if (!ISSET($_GET['category'])){
    // В запросе отсутствует category
    $var_404 = 1;
} else {
    $cat = $_GET['category'];
};

if (!ISSET($_GET['p'])){
    // В запросе отсутствует p
    $p=1;
} else {
    $p=intval($_GET['p']);
    if ($p <= 0 ) $p=1;
};


if(!$con) {
    $error="Ошибка подключения: " . mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
} else {
    $sql= "SELECT l.id id, l.name name, c.name category, GREATEST(IFNULL(MAX(r.summ),0),l.start_price) price, l.img_url url   FROM lots l
    JOIN users u ON u.id=l.user_author_id
    JOIN categories c ON c.id=l.category_id
    LEFT JOIN rates r ON r.lot_id=l.id
    WHERE l.category_id ='$cat' AND l.user_victor_id IS NULL
    GROUP BY l.id
    ORDER BY l.date_add DESC
    LIMIT 100";
    $result = mysqli_query($con, $sql);

    if(!$result) {
        $error= mysqli_error($con);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $lots_list = mysqli_fetch_all($result, MYSQLI_ASSOC);

    }

    $sql= "SELECT c.id, c.name FROM categories c";
    $result = mysqli_query($con, $sql);
    if(!$result) {
        $error= mysqli_error($con);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $categories= mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    $sql= "SELECT c.id, c.name FROM categories c WHERE c.id='$cat'";
    $result = mysqli_query($con, $sql);
    if(!$result) {
        $error= mysqli_error($con);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $category= mysqli_fetch_array($result, MYSQLI_ASSOC);
    }
}

/* Если нет никаких ошибок, то показываем обычную страницу */
if ($error == '') {
    $page_content = include_template('all-lots.php', [
        'category' => $category,
        'categories' => $categories,
        'lots_list' => $lots_list,
        'p' => $p
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

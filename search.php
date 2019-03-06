<?php
require_once('functions.php');
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
$current_page = 1;
$error = '';
$search = '';
$pages_count = 0;
$pages = 0;
$link = init();

if (ISSET($_GET['page'])){
    $current_page = intval($_GET['page']);
    if ($current_page <= 0 ) $current_page = 1;
};

if (ISSET($_GET['search'])){
    $search = convert_text($_GET['search']);
};

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

if ((($_SERVER['REQUEST_METHOD'] === 'POST')||($search !== ''))&&($error === '')) {
    //форма отправлена
    if ($search === '') {
        $search = trim(htmlspecialchars($_POST['search']));
    }

    if ($search !== '') {
        $lots_list = [];

        $page_items = 9;

        $sql= "SELECT COUNT(*) as cnt FROM lots l
        WHERE CURRENT_TIMESTAMP < l.date_end AND MATCH(l.name, description) AGAINST(?)";
        $stmt = db_get_prepare_stmt($link, $sql, [$search]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $items_count = mysqli_fetch_assoc($result)['cnt'];

        $pages_count = ceil($items_count / $page_items);
        $offset = ($current_page - 1) * $page_items;

        $pages = range(1, $pages_count);
        $sql= 'SELECT l.id id, l.name name, l.date_end, c.name category, COUNT(r.summ) amount, GREATEST(IFNULL(MAX(r.summ),0),l.start_price) price, l.img_url url   FROM lots l
        JOIN users u ON u.id=l.user_author_id
        JOIN categories c ON c.id=l.category_id
        LEFT JOIN rates r ON r.lot_id=l.id
        WHERE CURRENT_TIMESTAMP < l.date_end AND MATCH(l.name, description) AGAINST(?)
        GROUP BY l.id
        ORDER BY l.date_add DESC
        LIMIT ' . $page_items . ' OFFSET ' . $offset;

        $stmt = db_get_prepare_stmt($link, $sql, [$search]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $lots_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $page_content = include_template('search.php', [
                    'search' => $search,
                    'categories' => $categories,
                    'lots_list' => $lots_list,
                    'pages' => $pages,
                    'pages_count' => $pages_count,
                    'current_page' => $current_page]);
            $name_page="Результаты поиска по запросу: " . $search;
        } else {
            $page_content = include_template('error.php', ['error' => mysqli_error($link)]);
        }
    } else {
        // если пользователь отправил пустую строку, то выдаем пустые массивы и пустую страницу поиска в итоге
        $page_content = include_template('search.php', [
            'search' => $search,
            'categories' => $categories,
            'lots_list' => $lots_list,
            'pages' => $pages,
            'pages_count' => $pages_count,
            'current_page' => $current_page]);
        $name_page="Результаты поиска по запросу: " . $search;
    }
} else {
    $error="Неверные параметры запроса";
}

if ($error) {
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

<?php
require_once('functions.php');
require_once('init.php');

$is_auth = 0;
$user_name = '';
$user_id='';
session_start();
if (isset($_SESSION['user'])){
    $is_auth = 1;
    $user_name = $_SESSION['user']['name'];
    $user_id = $_SESSION['user']['id'];
}

$categories = [];
$name_page='';
$rate['cost']= '';
$errors = '';
$dict = '';
$rate_current_user = [];
$lot_data = [];
$rate_user = false;
$error = '';

$link = init();

$var_404 = 0;

if (!ISSET($_GET['id'])){
    // В запросе отсутствует наш id
    $var_404 = 1;
}
if (!$link) {
    $error="Ошибка подключения: " . mysqli_connect_error();
}

if (($var_404 === 0)&&($error === '')) {
    //Преобразуем к числу, чтобы избежать SQL- инъекций
    $id= intval($_GET['id']);
    $sql= "SELECT c.id, c.name FROM categories c";
    $result = mysqli_query($link, $sql);
    if(!$result) {
        $error= mysqli_error($link);
    } else {
        $categories= mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    $sql= "SELECT l.id id, l.name name, l.description description, l.date_end date_end, l.user_author_id, c.name category, GREATEST(IFNULL(MAX(r.summ),0),l.start_price) price, l.img_url url, l.step step  FROM lots l
    JOIN users u ON u.id=l.user_author_id
    JOIN categories c ON c.id=l.category_id
    LEFT JOIN rates r ON r.lot_id=l.id
    WHERE l.id='$id'
    GROUP BY l.id";

    $result = mysqli_query($link, $sql);
    if(!$result) {
        $error= mysqli_error($link);
    } else {
        $lot_data= mysqli_fetch_array($result, MYSQLI_ASSOC);
    }
}

if (!$lot_data) {
    $var_404 = 1;
    http_response_code(404);
    $page_content = include_template('404.php', [
        'categories' => $categories, 'error' => '404'
    ]);
}

if (($var_404 === 0)&&($error === '')) {
    $sql= "SELECT r.id id, r.summ summ, u.name name, r.date_add date_add, r.user_id  FROM rates r
    JOIN users u ON r.user_id=u.id
    WHERE r.lot_id='$id'
    ORDER BY r.date_add DESC
    LIMIT 10";

    $result = mysqli_query($link, $sql);
    if(!$result) {
        $error= mysqli_error($link);
    } else {
        $rates_data= mysqli_fetch_all($result, MYSQLI_ASSOC);

        //Проверяю есть ли ставка текущего пользователя
        $sql= "SELECT r.id id, r.summ summ, u.name name, r.date_add date_add, r.user_id  FROM rates r
        JOIN users u ON r.user_id=u.id
        WHERE r.lot_id='$id' AND r.user_id='$user_id'";
        $result = mysqli_query($link, $sql);

        if(!$result) {
            $error= mysqli_error($link);
        } else {
            $rate_current_user= mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
}

if (count($rate_current_user) > 0) {
    //ставка есть
    $rate_user = true;
}

/* Если нет никаких ошибок, то показываем страницу лота либо с формой по добавлению ставки, либо без нее */
// работа с данными формы по добавлению ставок, если она была отправлена
// и если выполнены условия:
// пользователь авторизован;
// срок размещения лота не истёк;
// лот создан не текущим пользователем;
// текущий пользователь еще не добавлял ставку для этого лота;
$conditions = 0;
if (($error === '')&&($var_404 === 0)) {
    //ошибок нет, лот существует, значит и $lot_data существует со всеми необходимыми полями
    if (($is_auth)&&(actually($lot_data['date_end']))&&($user_id != $lot_data['user_author_id'])&&(!$rate_user)) {
        $conditions = 1;
    }
};

if (($_SERVER['REQUEST_METHOD'] == 'POST')&&($conditions)) {
    $rate = $_POST;
    $required = ['cost'];
    $dict = ['cost' => 'Сумма ставки'];
    $errors = [];
    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if(isset($_POST['cost'])){
        if (!is_numeric($_POST['cost'])) {
            $errors['cost'] = 'Сумма ставки должна быть числом';
        } else if ($_POST['cost'] <= 0) {
            $errors['cost'] = 'Сумма ставки должна быть больше нуля';
        } else if ($_POST['cost'] < ($lot_data['price']+$lot_data['step'])) {
            $errors['cost'] = 'Сумма ставки должна быть больше текущей + шаг торгов';
        }
    } else {
        $errors['cost'] = 'Введите сумму ставки';
        $rate['cost'] = '';
    }

    if (count($errors)) {// если есть ошибки заполнения формы
        $page_content = include_template('lot.php', [
            'categories' => $categories,
            'lot' => $lot_data,
            'rates' => $rates_data,
            'cost' => $rate['cost'],
            'errors' => $errors,
            'dict' => $dict,
            'is_auth' => $is_auth,
            'user_id' => $user_id,
            'rate_user' => $rate_user
        ]);
    } else {
        //ошибок заполнения формы нет
        $sql = 'INSERT INTO rates (date_add, summ, user_id, lot_id) VALUES (NOW(), ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [$rate['cost'], $user_id, $id]);
        $res = mysqli_stmt_execute($stmt);

        // Добавили ставку и тепрерь нужно обновить страницу
        if ($res) {
            header("Location: lot.php?id=" . $id . "update=1");
            exit();
        } else {
            $error= mysqli_error($link);
        }
    }
} else if (($error === '')&&($var_404 === 0)){
    //форма не была отправлена, либо не выполнены перечисленные выше условия
    $page_content = include_template('lot.php', [
        'categories' => $categories,
        'lot' => $lot_data,
        'rates' => $rates_data,
        'cost' => [],
        'errors' => [],
        'dict' => [],
        'is_auth' => $is_auth,
        'user_id' => $user_id,
        'rate_user' => $rate_user
    ]);
    $name_page=$lot_data['name'];
}

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

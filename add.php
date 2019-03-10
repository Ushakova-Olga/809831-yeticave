<?php
// Добавление нового лота
require_once('functions.php');
require_once('init.php');

$is_auth = 0;
$user_name = '';
$user_id = '';

session_start();
if (isset($_SESSION['user'])){
    $is_auth = 1;
    $user_name = $_SESSION['user']['name'];
    $user_id = $_SESSION['user']['id'];
}

$categories = [];
$lots_list = [];
$id_category = 0;
$category = [];
$error = '';
$link = init();

// получаем категории для отрисовки на странице
if($link) {
    $sql= "SELECT c.id, c.name FROM categories c";
    $result = mysqli_query($link, $sql);
    if(!$result) {
        $error= mysqli_error($link);
        $page_content = include_template('error.php', ['error' => $error]);
    } else {
        $categories= mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

if ($is_auth === 0) {
    http_response_code(403);
    $page_content = include_template('404.php', [
        'categories' => $categories, 'error' => '403'
    ]);
}
    // работа с данными формы если она была отправлена
if (($_SERVER['REQUEST_METHOD'] === 'POST')&&($is_auth)&&($link)) {
    $lot = $_POST;
    $required = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
    $dict = ['lot-name' => 'Наименование лота', 'category' => 'Категория', 'message' => 'Описание',
        'lot-rate'=>'Начальная цена' , 'lot-step'=>'Шаг ставки' , 'lot-date'=>'Дата окончания торгов', 'file'=>'Изображение лота'];
    $errors = [];

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if(isset($_POST['category'])){
        if ((!is_numeric($_POST['category']))||($_POST['category'] <= 0)) {
            $errors['category'] = 'Выберите категорию';
        }
        $id_category = intval($_POST['category']);
    }

    // проверка есть ли категория в базе
    if(!isset($errors['category'])){
        $sql= "SELECT * FROM categories c WHERE c.id='$id_category'";
        $result = mysqli_query($link, $sql);

        if(!$result) {
            $error= mysqli_error($link);
        } else {
            $category= mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        if (!$category){
            $errors['category'] = 'Данной категории не существует';
        }
    }

    if(isset($_POST['lot-rate'])){
        if ((!is_numeric($_POST['lot-rate']))||($_POST['lot-rate'] <= 0)) {
            $errors['lot-rate'] = 'Введите число больше нуля';
        }
    }
    if(isset($_POST['lot-step'])){
        if ((!is_numeric($_POST['lot-step']))||($_POST['lot-step'] <= 0)) {
            $errors['lot-step'] = 'Введите число больше нуля';
        }
    }
    if(isset($_POST['lot-date'])){
        if (!delta_day($_POST['lot-date'])) {
            $errors['lot-date'] = 'Дата завершения должна быть хотя бы на день больше текущей';
        }
        if (!date_limit($_POST['lot-date'])){
            $errors['lot-date'] = 'Дата завершения должна быть меньше 2038 года';
        }
    }

    if(isset($_POST['name'])){
        if (strlen($_POST['name']) > 255) {
            $errors['name'] = 'Название лота должно быть короче 255 символов';
        }
    }

    $file_exist = 0;
    if (isset($_FILES['photo2']['name'])) {
        if (!empty($_FILES['photo2']['name'])) {
            $file_exist = 1;
        }
    }

    if ($file_exist) {
        $tmp_name = $_FILES['photo2']['tmp_name'];
        $path = $_FILES['photo2']['name'];
        $file_type = mime_content_type($tmp_name);

        switch ($file_type) {
            case "image/jpeg":
                $path = uniqid() . ".jpg";
                break;
            case "image/png":
                $path = uniqid() . ".png";
                break;
            default:
                break;
        }
        if (($file_type !== "image/jpeg")&&($file_type !== "image/png")) {
            $errors['file'] = 'Загрузите картинку в формате GPEG, либо PNG';
        }
    } else {
        $errors['file'] = 'Вы не загрузили файл';
    }

    if (count($errors)) {// если есть ошибки заполнения формы
        $page_content = include_template('add.php', ['lot' => $lot, 'categories' => $categories, 'errors' => $errors, 'dict' => $dict]);
    } else if ($link){
        //ошибок заполнения формы нет
        // сохраняем картинку в папку 'img/', длину не проверяю т.к. заранее известна длина кот возвращает uniqid() и заведомо < 255
        move_uploaded_file($tmp_name, 'img/' . $path);
        $lot['path'] = $path;

        $sql = 'INSERT INTO lots (date_add, name, description, img_url, start_price, date_end, step, user_author_id, category_id) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [$lot['lot-name'], $lot['message'], 'img/' . $lot['path'], $lot['lot-rate'], $lot['lot-date'], $lot['lot-step'], $user_id, $lot['category']]);
        $res = mysqli_stmt_execute($stmt);

        // получили id созданого лота и перешли на его страницу
        if ($res) {
            $lot_id = mysqli_insert_id($link);
            header("Location: lot.php?id=" . $lot_id);
            exit();
        } else {
            $page_content = include_template('error.php', ['error' => mysqli_error($link)]);
        }
    }
} else if ($is_auth){
    // форма не была отправлена, просто отображаем страницу
    $page_content = include_template('add.php', ['categories' => $categories]);
}

if (!$link){
    $error="Ошибка подключения: " . mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'name_page' => 'Добавление лота',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
?>

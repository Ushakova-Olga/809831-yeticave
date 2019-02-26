<?php
// Добавление нового лота
require_once('functions.php');

$is_auth = 0;
$user_name = '';
$user_id = '';

session_start();
if (isset($_SESSION['user'])){
    $u = $_SESSION['user'];
    $is_auth = 1;
    $user_name = $u['name'];
    $user_id = $u['id'];
}

$categories = [];
$lots_list = [];

$error = '';
$con = mysqli_connect("localhost", "root", "", "yeticave");
mysqli_set_charset($con, "utf8");

// получаем категории для отрисовки на странице
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

    if ($is_auth ==0) {
        http_response_code(403);
        $page_content = include_template('404.php', [
            'categories' => $categories, 'error' => '403'
        ]);
    } else {
    // работа с данными формы если она была отправлена
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

        if ((!is_numeric($_POST['category']))||($_POST['category'] <= 0)) {
            $errors['category'] = 'Выберите категорию';
        }

        if ((!is_numeric($_POST['lot-rate']))||($_POST['lot-rate'] <= 0)) {
            $errors['lot-rate'] = 'Введите число больше нуля';
        }

        if ((!is_numeric($_POST['lot-step']))||($_POST['lot-step'] <= 0)) {
            $errors['lot-step'] = 'Введите число больше нуля';
        }

        if (!delta_day($_POST['lot-date'])) {
            $errors['lot-date'] = 'Дата завершения должна быть хотя бы на день больше текущей';
        }

        if (isset($_FILES['photo2']['name'])) {
            if (!empty($_FILES['photo2']['name'])) {
                $tmp_name = $_FILES['photo2']['tmp_name'];
                $path = $_FILES['photo2']['name'];

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $file_type = finfo_file($finfo, $tmp_name);
                if (($file_type !== "image/jpeg")&&($file_type !== "image/png")) {
                    $errors['file'] = 'Загрузите картинку в формате GPEG, либо PNG';
                }
                else {
                    if ($file_type == "image/jpeg") {
                        $path = uniqid() . ".jpg";
                    }
                    if ($file_type == "image/png") {
                        $path = uniqid() . ".png";
                    }
                    move_uploaded_file($tmp_name, 'img/' . $path);
                    $lot['path'] = $path;
                }
            } else {
                $errors['file'] = 'Вы не загрузили файл';
            }
        } else {
            $errors['file'] = 'Вы не загрузили файл';
        }

        if (count($errors)) {// если есть ошибки заполнения формы
            $page_content = include_template('add.php', ['lot' => $lot, 'categories' => $categories, 'errors' => $errors, 'dict' => $dict]);
        } else {
            //ошибок заполнения формы нет
            $sql = 'INSERT INTO lots (date_add, name, description, img_url, start_price, date_end, step, user_author_id, category_id) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($con, $sql, [$lot['lot-name'], $lot['message'], 'img/' . $lot['path'], $lot['lot-rate'], $lot['lot-date'], $lot['lot-step'], $user_id, $lot['category']]);
            $res = mysqli_stmt_execute($stmt);

            // получили id созданого лота и перешли на его страницу
            if ($res) {
                $lot_id = mysqli_insert_id($con);
                header("Location: lot.php?id=" . $lot_id);
            } else {
                $page_content = include_template('error.php', ['error' => mysqli_error($con)]);
            }
        }
    } else {
        // форма не была отправлена, просто отображаем страницу
        $page_content = include_template('add.php', ['categories' => $categories]);
        }
    }
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

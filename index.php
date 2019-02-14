<?php
require_once('functions.php');
$is_auth = rand(0, 1);

$user_name = 'Ольга'; // укажите здесь ваше имя
$categories = [];
$lots_list = [];
/*$categories = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];
$lots_list = [
  [
    'name' => '2014 Rossignol District Snowboard',
    'category' => 'Доски и лыжи',
    'price' => 10999,
    'url' => 'img/lot-1.jpg'
  ],
  [
    'name' => 'DC Ply Mens 2016/2017 Snowboard',
    'category' => 'Доски и лыжи',
    'price' => 159999,
    'url' => 'img/lot-2.jpg'
  ],
  [
    'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
    'category' => 'Крепления',
    'price' => 8000,
    'url' => 'img/lot-3.jpg'
  ],
  [
    'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
    'category' => 'Ботинки',
    'price' => 10999,
    'url' => 'img/lot-4.jpg'
  ],
  [
    'name' => 'Куртка для сноуборда DC Mutiny Charocal',
    'category' => 'Одежда',
    'price' => 7500,
    'url' => 'img/lot-5.jpg'
  ],
  [
    'name' => 'Маска Oakley Canopy',
    'category' => 'Разное',
    'price' => 5400,
    'url' => 'img/lot-6.jpg'
  ]
];*/

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
    $sql= "SELECT l.name name, c.name category, l.start_price price, l.img_url url   FROM lots l
    JOIN users u ON u.id=l.user_author_id
    JOIN categories c ON c.id=l.category_id
    WHERE l.user_victor_id IS NULL
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

<?php
// Модуль для определения победителя.
require_once('init.php');

function victory() {
    $error = '';
    $link = init();

    $now = strtotime('now');
    $letter = '';
    if(!$link) {
        $error="Ошибка подключения: " . mysqli_connect_error();
    } else {
        $sql= "SELECT l.id id, l.name name, GREATEST(IFNULL(MAX(r.summ),0),l.start_price) price  FROM lots l
        JOIN users u ON u.id=l.user_author_id
        JOIN categories c ON c.id=l.category_id
        LEFT JOIN rates r ON r.lot_id=l.id
        WHERE l.user_victor_id IS NULL AND CURRENT_TIMESTAMP > l.date_end AND r.user_id IS NOT NULL
        GROUP BY l.id
        ORDER BY l.date_add DESC
        LIMIT 100";
        $result = mysqli_query($link, $sql);

        if(!$result) {
            $error= mysqli_error($link);
            $page_content = include_template('error.php', ['error' => $error]);
        } else {
            $lots_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        foreach ($lots_list as $item){
            $id = $item['id'];
            $name = $item['name'];
            $sql= "SELECT r.user_id, r.id FROM rates r WHERE r.lot_id = '$id'
            ORDER BY r.date_add DESC
            LIMIT 1";
            $result = mysqli_query($link, $sql);
            $vic= mysqli_fetch_array($result, MYSQLI_ASSOC);
            $victor = $vic['user_id'];

            $sql= "UPDATE lots l SET l.user_victor_id = '$victor'
            WHERE l.id='$id'";
            $result = mysqli_query($link, $sql);

            $sql= "SELECT u.email email, u.name, u.id FROM users u WHERE u.id = '$victor'";
            $result = mysqli_query($link, $sql);
            $v= mysqli_fetch_array($result, MYSQLI_ASSOC);
            $letter = 'Уважаемый(-ая) ' . $v['name'] .' Вы выиграли лот '. $name. ' Письмо отправлено на адрес: ' .$v['email'];
            $headers  = "Content-type: text/html; charset=windows-1251 \r\n";
            $headers .= "From: От кого письмо <mail@yeticave.ru>\r\n";

            if (mail($v['email'], "Вы выиграли лот", $letter, $headers)) {
                echo('Письмо отправлено: ' . $letter);
            } else {
                echo('Письмо не удалось отправить');
            };
        };
    }
};
?>

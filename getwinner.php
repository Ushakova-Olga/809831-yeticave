<?php
// Модуль для определения победителя.
require_once 'vendor/autoload.php';
require_once 'init.php';

// Конфигурация траспорта
$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

$mailer = new Swift_Mailer($transport);

$logger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

$error = '';
$link = init();
$letter = '';
$lots_list = [];

if($link) {
    $sql= "SELECT l.id id, l.name name, GREATEST(IFNULL(MAX(r.summ),0),l.start_price) price  FROM lots l
        JOIN users u ON u.id=l.user_author_id
        JOIN categories c ON c.id=l.category_id
        LEFT JOIN rates r ON r.lot_id=l.id
        WHERE l.user_winner_id IS NULL AND CURRENT_TIMESTAMP > l.date_end AND r.user_id IS NOT NULL
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

    foreach ($lots_list as $item) {
        $id = $item['id'];
        $name = $item['name'];
        $sql= "SELECT r.user_id, r.id FROM rates r WHERE r.lot_id = '$id'
            ORDER BY r.date_add DESC
            LIMIT 1";
        $result = mysqli_query($link, $sql);
        $rate_winner= mysqli_fetch_array($result, MYSQLI_ASSOC);
        $winner = $rate_winner['user_id'];

        $sql= "UPDATE lots l SET l.user_winner_id = '$winner'
        WHERE l.id='$id'";
        $result = mysqli_query($link, $sql);

        $sql= "SELECT u.email email, u.name, u.id FROM users u WHERE u.id = '$winner'";
        $result = mysqli_query($link, $sql);
        $user_winner= mysqli_fetch_array($result, MYSQLI_ASSOC);
        $path = '';
        if (isset($_SERVER['HTTP_HOST'])&& isset($_SERVER['REQUEST_URI'])){
            $path = 'http://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }

        $letter = include_template('email.php', [
        	'user_name' => $user_winner['name'],
        	'lot_name' => $name,
        	'lot_id' => $id,
            'path'=> $path
        ]);

        $message = new Swift_Message();
        $message->setSubject("Ваша ставка победила");
        $message->setFrom(['keks@phpdemo.ru' => 'Yeticave']);
        $message->setBcc([$user_winner['email'] => $user_winner['name']]);
        $message->setBody($letter, 'text/html');

        // Отправка сообщения
        $result = $mailer->send($message);

        if ($result) {
            echo("Письмо победителю успешно отправлено ");
        }
        else {
            echo("Не удалось отправить письмо: " . $logger->dump());
        }
    };
};
?>

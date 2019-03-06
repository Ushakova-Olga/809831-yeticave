<?
/**
* Инициализация подключения к БД
* @return Возвращает дескриптор соединения с MySQL в случае успеха, либо false в случае возникновения ошибки.
*/
function init() {
    $link = mysqli_connect("localhost", "root", "", "yeticave");
    mysqli_set_charset($link, "utf8");
    return $link;
}

?>

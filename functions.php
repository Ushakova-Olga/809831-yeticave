<?php

/**
* Формирует html - код на основании переданных параметров и шаблона
* @param string $name Название файла, в котором находится шаблон
* @param array $data Данные в виде массива вида ключ->значение для подстановки в шаблон
* @return string Сформированный из шаблона html-код
*/
function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
* Экранирует опасные символы в передаваемой строке
* @param string $str Строка, котороую нужно конверировать
* @return string Строка, в которые опасные символы заменены аналогами
*/
function convert_text($str) {
	$text = htmlspecialchars($str);
	return $text;
}

/**
* Форматирует вывод цены - отступы между разрядами и символ рубля
* @param integer Число, которое нужно конверировать
* @return string Результат в виде строки
*/
function format_rub($arg)
{
    $result = ceil($arg);
    if ($result > 999) {
        $result = number_format($result,0,'',' ');
    }
    $result = $result . " ₽";
    return $result;
}

/**
* Возвращает сколько часов, минут и секунд осталось до наступления времени, указанного в параметре
* @param string $str Дата до которой нужно считать оставшееся время
* @return string Результат в виде строки
*/
function seconds_free($str) {
    $ts_start = strtotime('now');
    $ts_end = strtotime($str);

    $secs_free = $ts_end - $ts_start;
    if ($secs_free > 0) {
        $hours = floor($secs_free / 3600);
        $minutes = floor(($secs_free % 3600)/60);
        $seconds = floor(($secs_free % 3600)%60);
        $result = $hours . ':' . $minutes . ':' . $seconds;

        return $result;
    }
    return '';
}

/**
* Возвращает количество ставок в виде строки, либо если число ставок равно нулю "Стартовая цена"
* @param integer $val количество ставок
* @return string Результат в виде строки
*/
function rates_string($val) {
    $rates = intval($val);
    $result = "Стартовая цена";

    if ($rates > 0) {
        $result = $rates . ' ставок';
        if (((($rates % 10) === 1 )&&($rates !== 11 ))) {
            $result = $rates . ' ставка';
        } else if ((($rates > 1 )&&($rates < 5))||(( $rates >= 22)&&( $rates <=24 ))){
            $result = $rates . ' ставки';
        }
    }
    return $result;
}

/**
* Возвращает булево значение истина если переданная дата больше текущей
* @param string $str Дата окончания лота в виде строки
* @return boolean Результат в виде значения истина, либо ложь
*/
function actually($str) {
    $ts_start = strtotime('now');
    $ts_end = strtotime($str);
    $secs_free = $ts_end - $ts_start;

    $result = ($secs_free > 0) ? true : false;
    return $result;
}

/**
* Возвращает булево значение истина если от текущего момента до указанной даты более 1 суток
* @param string $str Дата окончания лота в виде строки
* @return boolean Результат в виде значения истина, либо ложь
*/
function delta_day($str) {
    $ts_lot = strtotime($str);
    $secs_passed = $ts_lot - strtotime('now');

    $days = floor($secs_passed / 86400);
    if ($days > 0) {
        return true;
    }
    return false;
}

/**
* Возвращает булево значение истина если переданная дата не позже 2038 года
* @param string $str Дата окончания лота в виде строки
* @return boolean Результат в виде значения истина, либо ложь
*/
function date_limit($str) {
    $year = intval(substr($str, 0, 4));

    if ($year > 2038) {
        return false;
    }
    return true;
}

/**
* Возвращает в виде строки сообщение о том сколько времени назад была сделана ставка
* @param string $str Дата ставки в виде строки
* @return string Результат в виде строки
*/
function rate_time($str) {
    $ts_lot = strtotime($str);
    $secs_passed = strtotime('now') - $ts_lot;

    $days = intval(floor($secs_passed / 86400));
    $hours = intval(floor($secs_passed / 3600));
    if ($secs_passed < 0) {
        return ''; //false;
    }

    if (($days === 0)&&($hours > 0)){
        $result = $hours . ' часов назад';
        if (((($hours % 10) === 1 )&&($hours !== 11 ))||($hours === 21)) {
            $result = $hours . ' час назад';
        } else if ((($hours > 1 )&&($hours < 5))||(( $hours >= 22)&&( $hours <=23 ))){
            $result = $hours . ' часа назад';
        } else if (($hours >= 5)&&($hours < 21)){
            $result = $hours . ' часов назад';
        }
    }
    if (($days === 0)&&($hours <= 0)){
        $minutes = floor(($secs_passed % 3600)/60);
        if ((($minutes % 10) === 1)&&($minutes !== 11)) {
            $result = $minutes . ' минуту назад';
        } else {
            $result = $minutes . ' минут назад';
        }
    }

    if ($days > 0) {
        $result = date_format(date_create($str), "d.m.y в H:i");
    }

    return $result;
}

/**
* Функция для создания подготовленных выражений
* @param
* @param string
* @param array
* @return
*/
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = null;

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);
    }

    return $stmt;
}
?>

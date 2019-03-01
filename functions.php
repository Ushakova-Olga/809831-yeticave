<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

function convert_text($str) {
	$text = htmlspecialchars($str);
	return $text;
}

function format_rub($arg)
{
    $result = ceil($arg);
    if ($result > 999) {
        $result = number_format($result,0,'',' ');
    }
    $result = $result . " ₽";
    return $result;
}

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
    return false;
}

function rates_string($str) {
    $rates = intval($str);
    $result = "Стартовая цена";

    if ($rates > 0) {
        $result = $rates . ' ставок';
        if (((($rates % 10) == 1 )&&($rates != 11 ))) {
            $result = $rates . ' ставка';
        } else if ((($rates > 1 )&&($rates < 5))||(( $rates >= 22)&&( $rates <=24 ))){
            $result = $rates . ' ставки';
        }
    }
    return $result;
}

function actually($str) {
    $ts_start = strtotime('now');
    $ts_end = strtotime($str);
    $secs_free = $ts_end - $ts_start;

    $result = ($secs_free > 0) ? true : false;
    return $result;
}

function delta_day($str) {
    $ts_lot = strtotime($str);
    $secs_passed = $ts_lot - strtotime('now');

    $days = floor($secs_passed / 86400);
    if ($days > 0) {
        return true;
    }
    return false;
}

function date_limit($str) {
    $year = intval(substr($str, 0, 4));

    if ($year > 2038) {
        return false;
    }
    return true;
}

function rate_time($str) {
    $ts_lot = strtotime($str);
    $secs_passed = strtotime('now') - $ts_lot;

    $days = floor($secs_passed / 86400);
    $hours = floor($secs_passed / 3600);
    if ($secs_passed < 0) {
        return false;
    }

    if (($days == 0)&&($hours > 0)){
        $result = $hours . ' часов назад';
        if (((($hours % 10) == 1 )&&($hours != 11 ))||($hours == 21)) {
            $result = $hours . ' час назад';
        } else if ((($hours > 1 )&&($hours < 5))||(( $hours >= 22)&&( $hours <=23 ))){
            $result = $hours . ' часа назад';
        } else if (($hours >= 5)&&($hours < 21)){
            $result = $hours . ' часов назад';
        }
    }
    if (($days == 0)&&($hours <= 0)){
        $minutes = floor(($secs_passed % 3600)/60);
        if ((($minutes % 10) == 1)&&($minutes != 11)) {
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

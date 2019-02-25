<?php
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
	//$text = strip_tags($str);

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

function seconds_tomorrow() {
    $ts_midnight = strtotime('tomorrow midnight');
    $secs_to_midnight = $ts_midnight - strtotime('now');
    $hours = floor($secs_to_midnight / 3600);
    $minutes = floor(($secs_to_midnight % 3600)/60);
    $result = $hours . ':' . $minutes;

    return $result;
}

function delta_day($str) {
    $ts_lot = strtotime($str);
    $secs_passed = $ts_lot - strtotime('now');

    $days = floor($secs_passed / 86400);
    if ($days > 0) return true;
    return false;
}

function rate_time($str) {
    $ts_lot = strtotime($str);
    $secs_passed = strtotime('now') - $ts_lot;

    $days = floor($secs_passed / 86400);

    if ($days == 0) {
        $hours = floor($secs_passed / 3600);
        if ($hours > 0) {
            $result = $hours . ' часов назад';
            if (((($hours % 10) == 1 )&&($hours != 11 ))||($hours == 21)) {
                $result = $hours . ' час назад';
            } else if ((($hours > 1 )&&($hours < 5))||(( $hours >= 22)&&( $hours <=23 ))){
                $result = $hours . ' часа назад';
            } else if (($hours >= 5)&&($hours < 21)){
                $result = $hours . ' часов назад';
            }
        } else {
            $minutes = floor(($secs_passed % 3600)/60);
            if ((($minutes % 10) == 1)&&($minutes != 11)) {
                $result = $minutes . ' минуту назад';
            } else {
                $result = $minutes . ' минут назад';
            }
        }
    } else {
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

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

function seconds_tomorrow() {
    /*date_default_timezone_set("Asia/Krasnoyarsk");*/
    $ts_midnight = strtotime('tomorrow midnight');
    $secs_to_midnight = $ts_midnight - strtotime('now');
    $hours = floor($secs_to_midnight / 3600);
    $minutes = floor(($secs_to_midnight % 3600)/60);
    $result = $hours . ':' . $minutes;

    return $result;
}
?>

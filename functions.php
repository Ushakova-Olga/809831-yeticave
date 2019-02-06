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
?>
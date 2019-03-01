<?
function init() {
    $link = mysqli_connect("localhost", "root", "", "yeticave");
    mysqli_set_charset($link, "utf8");
    return $link;
}

?>

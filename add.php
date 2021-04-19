<?php
require('helpers.php');
require('utils.php');

/**
 * Получает тип контента из базы данных
 * @param object ресурс соединения с базой данных
 * @return array массив типов постов
 */
function getTypeContent($mysqli)
{
    $sql = "SELECT * FROM type_contents";
    $result = $mysqli->query($sql);
    $result = $result->fetch_all(MYSQLI_ASSOC);
    return $result;
}

$mysqli = connect();
$type_content = getTypeContent($mysqli);

$content = include_template('adding-post.php', ['types' => $type_content]);

$data = [
    'content' => $content,
    'user_name' => 'Alex',
    'is_auth' => 1,
    'page_name' => 'Добавить пост'
];

print_r(include_template('layout.php', $data));

?>

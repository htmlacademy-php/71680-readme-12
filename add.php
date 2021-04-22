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

$tabs = [
    'text' => 'text',
    'quote' => 'quote',
    'photo' => 'photo',
    'video' => 'video',
    'link' => 'link'
];

$active_tab = isset($_POST['type-post']) ? $tabs[$_POST['type-post']] : $tabs['text'];

$mysqli = connect();
$type_content = getTypeContent($mysqli);

$content = include_template('adding-post.php', ['types' => $type_content, 'active_tab' => $active_tab]);

$data = [
    'content' => $content,
    'is_auth' => 1,
    'user_name' => 'Alex',
    'page_name' => 'Добавить пост'
];

print_r(include_template('layout.php', $data));

?>

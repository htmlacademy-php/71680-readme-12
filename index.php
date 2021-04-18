<?php
require('helpers.php');
require('utils.php');
date_default_timezone_set('Europe/Moscow');
$is_auth = rand(0, 1);
$user_name = 'Александр';

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

/**
 * Получает первые 6 популярных постов
 * @param object ресурс соединения с базой данных
 * @return array массив популярных постов
 */
function getPopularPosts($mysqli)
{
    $sql= "
        SELECT
        p.id,
        date_create,
        title,
        text_content,
        quote_author,
        image_url,
        video_url,
        link,
        avatar_url,
        view_number,
        u.login,
        tc.name_icon as post_type
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN type_contents tc ON p.type_id = tc.id
        ORDER BY view_number DESC LIMIT 6
    ";
    $result = $mysqli->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getPostByType($mysqli)
{
    $type = $_GET['type'];
    $stmt = $mysqli->prepare("
        SELECT
        p.id,
        date_create,
        title,
        text_content,
        quote_author,
        image_url,
        video_url,
        link,
        avatar_url,
        view_number,
        u.login,
        tc.name_icon as post_type
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN type_contents tc ON p.type_id = tc.id
        WHERE tc.id = ?
        ORDER BY view_number DESC LIMIT 6
    ");
    $stmt->bind_param('i', $type);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Подключается к базе данных и запрашивает данные
 * @return array массив с данными
 */
function getData()
{
    $conn = connect();

    if (!$conn) {
        return;
    }

    if (isset($_GET['type'])) {
        $post = getPostByType($conn);
    } else {
        $post = getPopularPosts($conn);
    }
    $content_types = getTypeContent($conn);
    return [$content_types, $post];
}

[$content_types, $popular_posts] = getData();

$filter_type = $_GET['type'] ?? '';

$posts = getPostsWithDate($popular_posts);
$safe_data = prepearingPosts($posts);
$content = include_template('main.php',
    [
        'posts' => $safe_data,
        'content_types' => $content_types,
        'filter_type' => $filter_type,
    ]
);

$data = [
    'content' => $content,
    'user_name' => htmlspecialchars($user_name),
    'is_auth' => $is_auth,
    'page_name' => 'readme',
];
print(include_template('layout.php', $data));
?>

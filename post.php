<?php
require('helpers.php');
require('utils.php');

if (empty($_GET['id'])) {
    header("HTTP/1.0 404 Not Found");
    return;
}

$templates = [
    'video' => 'video.php',
    'text' => 'text.php',
    'link' => 'link.php',
    'quote' => 'quote.php',
    'photo' => 'image.php'
];

define("HOST", 'localhost');
define("USER", 'root');
define("PASSWORD", '');
define("DATABASE", 'readme');

/**
 * Функция для соединения с базой данных
 * @return object возвращает ресурс соединения, либо false если соединение неудалось
 */
function connect()
{
    $mysqli  = new mysqli(HOST, USER, PASSWORD, DATABASE);
    if ($mysqli->connect_errno) {
        print("Ошибка подключения: " . $mysqli->connect_errno);
        return;
    } else {
        $mysqli->set_charset("utf8");
        return $mysqli;
    }
}

function getContent($mysqli)
{
    $id = $_GET['id'];
    $stmt = $mysqli->prepare("
        SELECT p.id, date_create, title, text_content, quote_author, image_url,
        video_url, link, avatar_url, view_number, user_id, u.login, u.date_registration, tc.name_icon as post_type
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN type_contents tc ON p.type_id = tc.id
        WHERE p.id = ?
    ");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUserPublicationsCount($mysqli, $user_id)
{
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM `posts` WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_array()[0];
}

function getUserSubscribersCount($mysqli, $user_id)
{
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM `subscriptions` WHERE subscription_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_array()[0];
}

function getPostComments($mysqli)
{
    $post_id = $_GET['id'];
    $stmt = $mysqli->prepare("
        SELECT c.id, date_create, content, u.login, u.avatar_url FROM comments c
        JOIN users u ON user_id = u.id
        WHERE c.post_id = ?
        ORDER BY date_create DESC"
    );
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $result = $result->fetch_all(MYSQLI_ASSOC);
    $result = addRelativeTime($result);
    return $result;
}

function addRelativeTime($comments) {
    $result = Array();
    foreach ($comments as $comment) {
        if (is_array($comment)) {
            $comment['relative_time'] = getRelativeDate($comment['date_create'], 'назад');
            $result[] = $comment;
        }
    }
    return $result;
}

function getPostLikesCount($mysqli)
{
    $post_id = $_GET['id'];
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM `likes` WHERE post_id = ?");
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_array()[0];
}

function getCountViews($count)
{
    $frase = get_noun_plural_form($count, 'просмотр', 'просмотра', 'просмотров');
    return "{$count} {$frase}";
}

$mysqli = connect();
$data = getContent($mysqli);

if (empty($data)) {
    header("HTTP/1.0 404 Not Found");
    return;
}

$data = $data[0];

$type = $data['post_type'];
$template = $templates[$type];

$safe_post = prepearingPost($data);

$comments = getPostComments($mysqli);


$likes_count = getPostLikesCount($mysqli);
$comments_count = count($comments);

$safe_post['duration'] = getRelativeDate($safe_post['date_registration'], 'на сайте');
$safe_post['publications_count'] = getUserPublicationsCount($mysqli, $safe_post['user_id']);
$safe_post['subscribers_count'] = getUserSubscribersCount($mysqli, $safe_post['user_id']);
$safe_post['view_number'] = getCountViews($safe_post['view_number']);
$post = include_template($template, ['post' => $safe_post]);

$post_details = include_template('post-details.php',
    [
        'post' => $post,
        'info' => $safe_post,
        'comments' => $comments,
        'comments_count' => $comments_count,
        'likes_count' => $likes_count,
    ]
);

$data = [
    'content' => $post_details,
    'user_name' => 'Alex',
    'page_name' => $safe_post['title'],
    'is_auth' => 1,
];

print(include_template('layout.php', $data));
?>

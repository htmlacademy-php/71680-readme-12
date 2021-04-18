<?php
require('helpers.php');
require('utils.php');
require('models/post.php');

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

function getCountViews($count)
{
    $frase = get_noun_plural_form($count, 'просмотр', 'просмотра', 'просмотров');
    return "{$count} {$frase}";
}

$mysqli = connect();
$post = new Post($_GET['id'], $mysqli);
$data = $post->getPostContent();

if (empty($data)) {
    header("HTTP/1.0 404 Not Found");
    return;
}

$data = $data[0];

$type = $post->post_type;
$template = $templates[$type];

$safe_post = prepearingPost($data);

$comments = $post->getPostComments();
$comments = addRelativeTime($comments);


$likes_count = $post->getCountPostLikes();
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

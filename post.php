<?php
require('helpers.php');

$templates = [
    'video' => 'video.php',
    'text' => 'text.php',
    'link' => 'link.php',
    'quote' => 'quote.php',
    'photo' => 'image.php'
];

function cropText($text, $limit = 300)
{
    if (strlen(utf8_decode($text)) <= $limit) {
        return $text;
    }

    $words = explode(' ', $text);
    $crop_text= '';
    $space_after_word = 1;
    $length = 0;

    foreach ($words as $key => $value) {
        $length += strlen(utf8_decode($value)) + $space_after_word;
        if ($length > $limit) {
            $crop_text = implode(' ', array_slice($words, 0, $key));
            break;
        }
    }
    return $crop_text.'...';
}

/**
 * Формирует строку с относительной разницой между датами
 * @param int $diff Разница между датами в секундах
 * @return string Относительный формат даты
 */
function getRelativeDateString($diff, $ending)
{
    $ending = ' '.$ending;
    $minutes = ceil($diff/60);
    $hours = ceil($minutes/60);
    $days = ceil($minutes/1440);
    $weeks = ceil($minutes/10080);
    $mounth = floor($weeks/4);

    if ($minutes < 60) {
        return $minutes.' '.get_noun_plural_form($minutes, 'минута', 'минуты', 'минут')."{$ending}";
    }
    if ($minutes > 60 && $hours < 24) {
        return $hours.' '.get_noun_plural_form($hours, 'час', 'часа', 'часов')."{$ending}";
    }
    if ($hours >= 24 && $days < 7) {
        return $days.' '.get_noun_plural_form($days, 'день', 'дня', 'дней')."{$ending}";
    }
    if ($days >= 7 && $weeks < 5) {
        return $weeks.' '.get_noun_plural_form($weeks, 'неделя', 'недели', 'недель')."{$ending}";
    }
    if ($weeks >= 5) {
        return $mounth.' '.get_noun_plural_form($mounth, 'месяц', 'месяца', 'месяцев')."{$ending}";
    }
}

/**
 * Вычисляет разницу между датами и возвращает её в относительном формате
 * @param string $date Строковое представление даты
 * @return string Относительный формат даты
 */
function getRelativeDate($date, $ending)
{
    $pub_date = strtotime($date);
    $now = strtotime('now');
    $diff = $now - $pub_date;
    return getRelativeDateString($diff, $ending);
}

/**
 * Укорачивает текст в посте
 * @param array $post Массив с постом
 * @return array Массив поста с укороченным текстом
 */
function getShortenPostText($post)
{
    if ($post['type'] !== 'text') {
        return $post;
    }
    $post['short_text'] = cropText($post['text_content']);

    if ($post['short_text'] === $post['text_content']) {
        unset($post['short_text']);
    }
    return $post;
}

/**
 * Подготавливает один пост перед выводом в шаблон
 * @param array $post Ассоциативный массив
 * @return array Подготовленый массив
 */
function prepearingPost($post)
{
    $prepearing_post = Array();
    foreach ($post as $key => $value) {
        $prepearing_post[$key] = htmlspecialchars($value);
    }
    return getShortenPostText($prepearing_post);
}

/**
 * Подготавливает массив постов перед выводом в шаблон
 * @param array $data Двумерный массив
 * @return array Подготовленый массив
 */
function prepearingPosts($posts)
{
    $safe_posts = Array();
    foreach ($posts as $post) {
        if (is_array($post)) {
            $safe_posts[] = prepearingPost($post);
        }
    }
    return $safe_posts;
}

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
        video_url, link, avatar_url, view_number, user_id, u.login, u.date_registration, tc.name_icon as type
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN type_contents tc ON p.type_id = tc.id
        WHERE p.id = ?
    ");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC)[0];
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

$type = $data['type'];
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

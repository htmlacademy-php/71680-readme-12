<?php
date_default_timezone_set('Asia/Omsk');
require('helpers.php');
require('utils.php');

require('models/Post.php');
require('models/TypeContent.php');

$is_auth = rand(0, 1);
$user_name = 'Александр';

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

    $postModel = new Post($conn);
    $typeContentsModel = new TypeContent($conn);

    if (isset($_GET['type'])) {
        $post = $postModel->getPostByType($_GET['type']);
    } else {
        $post = $postModel->getPopularPosts($conn);
    }
    $content_types = $typeContentsModel->getAll();
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

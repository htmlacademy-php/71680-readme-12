<?php
require('helpers.php');
require('utils.php');

require('models/Post.php');
require('models/Hashtag.php');
require('models/Comment.php');
require('models/Like.php');

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

function addRelativeTime($comments) {
    $result = Array();
    foreach ($comments as $comment) {
        if (is_array($comment)) {
            $comment['relative_time'] = getRelativeDate($comment['date_create']);
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
$post = new Post($mysqli, $_GET['id']);
$data = $post->getPostContent();


if (empty($data)) {
    header("HTTP/1.0 404 Not Found");
    return;
}

$tags = (new Hashtag($mysqli))->getHashtags($post);
$comments = (new Comment($mysqli))->getComments($post);
$comments = addRelativeTime($comments);
$comments_count = count($comments);
$likes_count = (new Like($mysqli))->getCountLikes($post);

$template = $templates[$data->post_type];
$safe_data = prepearingPost($data);

$safe_data['duration_on_site'] = getRelativeDate($safe_data['date_registration'], 'на сайте');
$safe_data['view_number'] = getCountViews($safe_data['view_number']);
$post = include_template($template, ['post' => $safe_data]);

$post_details = include_template('post-details.php',
    [
        'post' => $post,
        'tags' => $tags,
        'info' => $safe_data,
        'comments' => $comments,
        'comments_count' => $comments_count,
        'likes_count' => $likes_count,
    ]
);

$data = [
    'content' => $post_details,
    'user_name' => 'Alex',
    'page_name' => $safe_data['title'],
    'is_auth' => 1,
];

print(include_template('layout.php', $data));
?>

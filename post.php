<?php
require('helpers.php');
$content = 'test';

$post = include_template('video.php', ['test' => $content]);
$test = include_template('post-details.php', ['post' => $post]);
$data = [
    'content' => $test,
    'user_name' => 'Alex',
    'page_name' => 'Post',
    'is_auth' => 1,
];
print(include_template('layout.php', $data));

?>

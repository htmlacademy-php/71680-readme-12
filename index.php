<?php
$is_auth = rand(0, 1);
$user_name = 'Александр';
$posts = [
    [
        'title' => 'Цитата',
        'type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'user_name' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Игра престолов',
        'type' => 'post-text',
        'content' => 'Действие «Игры престолов» происходит в вымышленном мире, напоминающем средневековую Европу. В сериале одновременно действует множество персонажей и развивается несколько сюжетных линий. Основных сюжетных арок три: первая посвящена борьбе нескольких влиятельных домов за Железный Трон Семи Королевств либо за независимость от него; вторая — потомку свергнутой династии правителей, принцессе-изгнаннице, планирующей вернуть престол; третья — древнему братству, охраняющему государство от угроз с севера.',
        'user_name' => 'Владик',
        'avatar' => 'userpic.jpg'
    ],
    [
        'title' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'content' => 'rock-medium.jpg',
        'user_name' => 'Виктор',
        'avatar' => 'userpic-mark.jpg'
    ],
    [
        'title' => 'Моя мечта',
        'type' => 'post-photo',
        'content' => 'coast-medium.jpg',
        'user_name' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Лучшие курсы',
        'type' => 'post-link',
        'content' => 'www.htmlacademy.ru',
        'user_name' => 'Владик',
        'avatar' => 'userpic.jpg'
    ],
];

/**
 * Получает текст и ограничивает его по количеству символов
 * @param string $text Текст поста, который нужно ограничить
 * @param integer $limit Максимальное количество символов
 * @return string Итоговый текст
 */
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
 * Укорачивает текст в посте
 * @param array $post Массив с постом
 * @return array Массив с укороченным текстом
 */
function getShortenPostText($post) {
    if ($post['type'] !== 'post-text') {
        return $post;
    }
    $post['short_text'] = cropText($post['content']);

    if ($post['short_text'] === $post['content']) {
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

$safe_data = prepearingPosts($posts);

require('helpers.php');

$content = include_template('main.php', ['posts' => $safe_data]);
$data = [
    'content' => $content,
    'user_name' => htmlspecialchars($user_name),
    'is_auth' => $is_auth,
    'page_name' => 'readme',
];
print(include_template('layout.php', $data));

?>


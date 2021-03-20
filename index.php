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
function crop_text($text, $limit = 300)
{
    if (strlen(utf8_decode($text)) <= $limit) {
        return '<p>'.htmlspecialchars($text).'</p>';
    }

    $words = explode(' ', $text);
    $read_more_link = '<a class="post-text__more-link" href="#">Читать далее</a>';
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
    return '<p>'.htmlspecialchars($crop_text).'...'.'</p>'.$read_more_link;
}

require('helpers.php');

$content = include_template('main.php', ['posts' => $posts]);
$data = [
    'content' => $content,
    'user_name' => $user_name,
    'is_auth' => $is_auth,
    'page_name' => 'readme',
];
print(include_template('layout.php', $data));

?>

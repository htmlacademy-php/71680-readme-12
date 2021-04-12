<?php
require('helpers.php');
date_default_timezone_set('Europe/Moscow');

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
 * Формирует строку с относительной разницой между датами
 * @param int $diff Разница между датами в секундах
 * @return string Относительный формат даты
 */
function getRelativeDateString($diff)
{
    $minutes = ceil($diff/60);
    $hours = ceil($minutes/60);
    $days = ceil($minutes/1440);
    $weeks = ceil($minutes/10080);
    $mounth = floor($weeks/4);

    if ($minutes < 60) {
        return $minutes.' '.get_noun_plural_form($minutes, 'минута', 'минуты', 'минут').' назад';
    }
    if ($minutes > 60 && $hours < 24) {
        return $hours.' '.get_noun_plural_form($hours, 'час', 'часа', 'часов').' назад';
    }
    if ($hours >= 24 && $days < 7) {
        return $days.' '.get_noun_plural_form($days, 'день', 'дня', 'дней').' назад';
    }
    if ($days >= 7 && $weeks < 5) {
        return $weeks.' '.get_noun_plural_form($weeks, 'неделя', 'недели', 'недель').' назад';
    }
    if ($weeks >= 5) {
        return $mounth.' '.get_noun_plural_form($mounth, 'месяц', 'месяца', 'месяцев').' назад';
    }
}

/**
 * Вычисляет разницу между датами и возвращает её в относительном формате
 * @param string $date Строковое представление даты
 * @return string Относительный формат даты
 */
function getRelativeDate($date)
{
    $pub_date = strtotime($date);
    $now = strtotime('now');
    $diff = $now - $pub_date;
    return getRelativeDateString($diff);
}

/**
 * Возвращает дату в формате 'дд.мм.гггг чч:мм'
 * @param string $date Строковое представление даты
 * @return string Строковое представление даты в формате 'дд.мм.гггг чч:мм'
 */
function getDateForTitle($date)
{
    return date('d.m.Y H:i', strtotime($date));
}

/**
 * Устанавливает для поста дату в разных форматах
 * @param array $post Ассоциативный массив с информацией о посте
 * @param integer $index Индекс поста
 * @return array Ассоциативный массив
 */
function getPostWithDate($post, $index)
{
    $post['date_relative'] = getRelativeDate($post['date_create']);
    $post['date_format'] = getDateForTitle($post['date_create']);
    return $post;
}

/**
 * Получает массив постов и устанавливает дату каждому посту в разных форматах
 * @param array $posts Двумерный массив
 * @return array Массив постов с установленныи датами
 */
function getPostsWithDate($posts)
{
    $posts_with_date = Array();
    foreach ($posts as $key => $value) {
        $posts_with_date[] = getPostWithDate($value, $key);
    }
    return $posts_with_date;
}

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
 * @return array Массив поста с укороченным текстом
 */
function getShortenPostText($post)
{
    if ($post['post_type'] !== 'text') {
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

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'readme';

/**
 * Функция для соединения с базой данных
 * @param string $host имя хоста
 * @param string $user пользователь базы данных
 * @param string $password пароль от базы данных
 * @param string $database имя базы данных
 * @return object возвращает ресурс соединения, либо false если соединение неудалось
 */
function connect($host, $user, $password, $database)
{
    $connection = mysqli_connect($host, $user, $password, $database);
    if ($connection === false) {
        print("Ошибка подключения: " . mysqli_connect_error());
        return;
    } else {
        mysqli_set_charset($connection, "utf8");
        return $connection;
    }
}

/**
 * Получает тип контента из базы данных
 * @param object ресурс соединения с базой данных
 * @return array массив типов постов
 */
function getTypeContent($conection)
{
    $sql = "SELECT * FROM type_contents";
    $result = mysqli_query($conection, $sql);
    $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $result;
}

/**
 * Получает первые 6 популярных постов
 * @param object ресурс соединения с базой данных
 * @return array массив популярных постов
 */
function getPopularPosts($conection)
{
    $sql = "
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
        view_number, u.login, tc.name_ikon as post_type
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN type_contents tc ON p.type_id = tc.id
        ORDER BY view_number DESC LIMIT 6;";
    $result = mysqli_query($conection, $sql);
    $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $result;
}

$conn = connect($host, $user, $password, $database);

if ($conn) {
    $content_types = getTypeContent($conn);
    $popular_posts = getPopularPosts($conn);
}

$posts = getPostsWithDate($popular_posts);
$safe_data = prepearingPosts($posts);
$content = include_template('main.php', ['posts' => $safe_data, 'content_types' => $content_types]);
$data = [
    'content' => $content,
    'user_name' => htmlspecialchars($user_name),
    'is_auth' => $is_auth,
    'page_name' => 'readme',
];
print(include_template('layout.php', $data));
?>

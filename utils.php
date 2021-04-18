<?php

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

/**
 * Формирует строку с относительной разницой между датами
 * @param int $diff Разница между датами в секундах
 * @param string $ending Окончание формируемой строки
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
 * @param string $ending Окончание формируемой строки
 * @return string Относительный формат даты
 */
function getRelativeDate($date, $ending = 'назад')
{
    $pub_date = strtotime($date);
    $now = strtotime('now');
    $diff = $now - $pub_date;
    return getRelativeDateString($diff, $ending);
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
function getPostWithDate($post)
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
        $posts_with_date[] = getPostWithDate($value);
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
?>

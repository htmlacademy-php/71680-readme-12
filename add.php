<?php
require('helpers.php');
require('utils.php');

$rules = [
    'post-title' => function () {
        return validateFilled('post-title', 'Заголовок');
    },
    'post-text' => function () {
        return validateFilled('post-text', 'Текст публикации');
    },
    'quote-text' => function () {
        return validateFilled('quote-text', 'Текст цитаты');
    },
    'quote-author' => function () {
        return validateFilled('quote-author', 'Автор');
    },
    'post-link' => function () {
        return validateLink('post-link');
    },
    'video-url' => function () {
        return validateYotubeUrl('video-url');
    },
    'file-photo' => function () {
        return validateImageFile('file-photo');
    }
];

$errors = Array();

foreach ($_POST as $key => $value) {

    if (isset($rules[$key])) {
        $rule = $rules[$key];
        $errors[$key] = $rule();
        if (is_null($errors[$key])) {
            unset($errors[$key]);
        }
    }
}

foreach ($_FILES as $key => $value) {
    if (isset($rules[$key])) {
        $rule = $rules[$key];
        $errors[$key] = $rule();
        if (is_null($errors[$key])) {
            unset($errors[$key]);
        }
    }
}




function validateFilled($field_name, $name = '') {
    if (empty($_POST[$field_name])) {
        $result = empty($name) ? '' : $name.'. ';
        return $result.'Поля обязательно для заполнения';
    }
}

function validateLink($field_name) {
    if (!empty($_POST[$field_name])) {
        if (filter_var($_POST[$field_name], FILTER_VALIDATE_URL) === false) {
            return 'Некорректная ссылка';
        }
    } else {
        return 'Ссылка должна быть заполнена';
    }
}

function validateYotubeUrl($field_name) {
    if (!empty($_POST[$field_name])) {
        if (filter_var($_POST[$field_name], FILTER_VALIDATE_URL)) {
            $check_video = check_youtube_url($_POST[$field_name]);
            if ($check_video !== true) {
                return $check_video;
            }
        } else {
            return'Некорректная ссылка';
        }
    } else {
        return 'Ссылка youtube должна быть заполнена';
    }
}

function validateImageFile($field_name) {
    if (is_uploaded_file($_FILES[$field_name]['tmp_name'])) {
        return 'фотография загружена';
    }
}

function validateImgPost($data, $files, &$errors) {
    if (empty($data['link-heading'])) {
        $errors['link-heading'] = 'Заголовок не должен быть пустым';
    }

    if (is_uploaded_file($files['file-photo']['tmp_name'])) {
        var_dump($files);
    }
}

/**
 * Получает тип контента из базы данных
 * @param object ресурс соединения с базой данных
 * @return array массив типов постов
 */
function getTypeContent($mysqli)
{
    $sql = "SELECT * FROM type_contents";
    $result = $mysqli->query($sql);
    $result = $result->fetch_all(MYSQLI_ASSOC);
    return $result;
}

$tabs = [
    'text' => 'text',
    'quote' => 'quote',
    'photo' => 'photo',
    'video' => 'video',
    'link' => 'link'
];

$active_tab = isset($_POST['type-post']) ? $tabs[$_POST['type-post']] : $tabs['text'];

$mysqli = connect();
$type_content = getTypeContent($mysqli);

$content = include_template('adding-post.php',
    [
        'types' => $type_content,
        'active_tab' => $active_tab,
        'data' => $_POST,
        'errors' => $errors
    ]
);

$data = [
    'content' => $content,
    'is_auth' => 1,
    'user_name' => 'Alex',
    'page_name' => 'Добавить пост'
];

print_r(include_template('layout.php', $data));

?>

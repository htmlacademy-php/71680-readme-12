<?php
require('helpers.php');
require('utils.php');

require('models/Post.php');
require('models/Hashtag.php');
require('models/TypeContent.php');

define('VALID_FORMAT', ['png', 'jpeg', 'gif']);
define('UPLOAD_DIRECTORY',  __DIR__."/uploads/");

function validateFilled($field_name, $name = '') {
    if (empty($_POST[$field_name])) {
        $result = empty($name) ? '' : $name.'. ';
        return $result.'Поле обязательно для заполнения';
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
            return 'Некорректная ссылка';
        }
    } else {
        return 'Ссылка youtube должна быть заполнена';
    }
}

function validatePhotoLink($field_name) {
    if (isset($_POST['file-photo'])) {
        return null;
    }

    if (empty($_POST[$field_name])) {
        return 'Загрузите фото либо укажите ссылку на него.';
    }

    if (filter_var($_POST[$field_name], FILTER_VALIDATE_URL) === false) {
        return 'Некорректная ссылка';
    }

    set_error_handler(function(){/*Ignore warning*/});

    $file = file_get_contents($_POST[$field_name]);

    if ($file === false) {
        return 'Не удалось загрузить файл';
    }
    restore_error_handler();
    $file_info = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = explode('/', $file_info->buffer($file))[1];

    if (!in_array($mime_type, VALID_FORMAT)) {
        return 'Загрузите допустимый формат изображения ('.implode(', ', VALID_FORMAT).')';
    }

    $file_name = uniqid();
    if (file_put_contents(UPLOAD_DIRECTORY."{$file_name}.{$mime_type}", $file) === false) {
        return 'Не удалось загрузить файл';
    }
    $_POST['image_url'] = 'uploads/'."{$file_name}.{$mime_type}";
}

function validateImageFile($field_name) {

    $mime_type = explode('/', mime_content_type($_FILES[$field_name]['tmp_name']))[1];

    if (!in_array($mime_type, VALID_FORMAT)) {
        return 'Загрузите допустимый формат изображения ('.implode(', ', VALID_FORMAT).')';
    }

    $file_name = $_POST[$field_name]['name'];
    $from = $_POST[$field_name]['tmp_name'];
    $to = UPLOAD_DIRECTORY."{$file_name}";
    if (move_uploaded_file($from, $to)) {
        $_POST['image_url'] = "uploads/{$file_name}";
    } else {
        return 'Не удалось загрузить файл';
    }
}

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
    'photo-link' => function () {
        return validatePhotoLink('photo-link');
    },
    'file-photo' => function () {
        return validateImageFile('file-photo');
    }
];

$mysqli = connect();
$type_content = (new TypeContent($mysqli))->getAll();

if (isset($_FILES['file-photo']) && is_uploaded_file($_FILES['file-photo']['tmp_name'])) {
    $_POST['file-photo'] = $_FILES['file-photo'];
}

if (isset($_POST['submit'])) {

    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
            if (is_null($errors[$key])) {
                unset($errors[$key]);
            }
        }
    }

    if (empty($errors)) {
        $post = new Post($mysqli);
        $hashtag = new Hashtag($mysqli);
        $post->addPost($_POST, $hashtag);
    }
}

$tabs = [
    'text' => 'text',
    'quote' => 'quote',
    'photo' => 'photo',
    'video' => 'video',
    'link' => 'link'
];

$active_tab = isset($_POST['type-post']) ? $tabs[$_POST['type-post']] : $tabs['text'];

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

<?php
require('helpers.php');
require('utils.php');

$mysqli = connect();
$type_content = getTypeContent($mysqli);

if (isset($_FILES['file-photo']) && is_uploaded_file($_FILES['file-photo']['tmp_name'])) {
    $_POST['file-photo'] = $_FILES['file-photo'];
}

define('VALID_FORMAT', ['png', 'jpeg', 'gif']);

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

function addTextPost($mysqli, $data)
{
    $stmt = $mysqli->prepare("INSERT INTO posts (title, text_content, user_id, type_id) VALUES (?,?,?,1);");
    $title = $data['post-title'];
    $content = $data['post-text'];
    $user = 1;
    $stmt->bind_param('ssi', $title, $content, $user);
    $stmt->execute();
    $id = $mysqli->insert_id;
    header("Location: post.php?id={$id}");
}

function addQuotePost($mysqli, $data)
{
    $stmt = $mysqli->prepare("INSERT INTO posts (title, text_content, quote_author, user_id, type_id) VALUES (?,?,?,?,2);");
    $title = $data['post-title'];
    $content = $data['quote-text'];
    $author = $data['quote-author'];
    $user = mt_rand(1, 3);
    $stmt->bind_param('sssi', $title, $content, $author, $user);
    $stmt->execute();
    $id = $mysqli->insert_id;
    header("Location: post.php?id={$id}");
}

function addPhotoPost ($mysqli, $data) {}

function addVideoPost ($mysqli, $data)
{
    $stmt = $mysqli->prepare("INSERT INTO posts (title, video_url, user_id, type_id) VALUES (?,?,?,4);");
    $title = $data['post-title'];
    $video = $data['video-url'];
    $user = mt_rand(1, 3);
    $stmt->bind_param('ssi', $title, $video, $user);
    $stmt->execute();
    $id = $mysqli->insert_id;
    header("Location: post.php?id={$id}");
}

function addLinkPost ($mysqli, $data)
{
    $stmt = $mysqli->prepare("INSERT INTO posts (title, link, user_id, type_id) VALUES (?,?,?,5);");
    $title = $data['post-title'];
    $link = str_replace( parse_url( $data['post-link'], PHP_URL_SCHEME ) . '://', '', $data['post-link']);
    $user = mt_rand(1, 3);
    $stmt->bind_param('ssi', $title, $link, $user);
    $stmt->execute();
    $id = $mysqli->insert_id;
    header("Location: post.php?id={$id}");
}

$errors = Array();
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
        switch ($_POST['type-post']) {
            case 'text':
                addTextPost($mysqli, $_POST);
                break;
            case 'quote':
                addQuotePost($mysqli, $_POST);
                break;
            case 'photo':
                addPhotoPost($mysqli, $_POST);
                break;
            case 'video':
                addVideoPost($mysqli, $_POST);
                break;
            case 'link':
                addLinkPost($mysqli, $_POST);
                break;
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

    $file = file_get_contents($_POST[$field_name]);

    if ($file === false) {
        return 'Файл загрузить не получилось';
    }
}

function validateImageFile($field_name) {

    $mime_type = explode('/', mime_content_type($_FILES[$field_name]['tmp_name']))[1];

    if (!in_array($mime_type, VALID_FORMAT)) {
        return 'Загрузите допустимый формат изображения ('.implode(', ', VALID_FORMAT).')';
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

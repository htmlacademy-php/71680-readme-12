<?php
require_once('Model.php');

class Post extends Model {
    private $id;
    private $author_id;
    protected $table = 'posts';

    function __construct($mysqli, $id = null) {
        parent::__construct($mysqli);
        $this->id = $id;
    }

    function getPopularPosts()
    {
        $sql= "
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
            view_number,
            u.login,
            tc.name_icon as post_type
            FROM {$this->table} p
            JOIN users u ON p.user_id = u.id
            JOIN type_contents tc ON p.type_id = tc.id
            ORDER BY view_number DESC LIMIT 6
        ";
        $result = $this->mysqli->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function getPostByType($type)
    {
        $stmt = $this->mysqli->prepare("
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
            view_number,
            u.login,
            tc.name_icon as post_type
            FROM {$this->table} p
            JOIN users u ON p.user_id = u.id
            JOIN type_contents tc ON p.type_id = tc.id
            WHERE tc.id = ?
            ORDER BY view_number DESC LIMIT 6
        ");
        $stmt->bind_param('i', $type);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getPostContent()
    {
        $stmt = $this->mysqli->prepare("
            SELECT p.id, date_create, title, text_content, quote_author, image_url,
            video_url, link, avatar_url, view_number, user_id, u.login, u.date_registration, tc.name_icon as post_type
            FROM {$this->table} p
            JOIN users u ON p.user_id = u.id
            JOIN type_contents tc ON p.type_id = tc.id
            WHERE p.id = ?
        ");
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_object();
        if ($result) {
            $this->setAuthorId($result->user_id);
            $result->publications_count = $this->getAuthorPublicationsCount();
            $result->subscribers_count = $this->getAuthorSubscribersCount();
        }
        return $result;
    }

    function addPost(array $data, Hashtag $hashtag)
    {
        $stmt = $this->mysqli->prepare("
            INSERT INTO {$this->table} (title, text_content, quote_author, image_url, video_url, link, user_id, type_id)
            VALUES (?,?,?,?,?,?,?,?)
        ");
        $title = $data['post-title'] ?? null;
        $text_content = $data['post-text'] ?? null;
        $quote_author = $data['quote-author'] ?? null;
        $image_url = $data['image_url'] ?? null;
        $video_url = $data['video-url'] ?? null;
        $link = isset($data['post-link']) ? str_replace( parse_url( $data['post-link'], PHP_URL_SCHEME ) . '://', '', $data['post-link']) : null;
        $user = mt_rand(1, 3);
        $type_id = $this->getTypeIdPost($data['type-post']);
        $stmt->bind_param('ssssssii', $title, $text_content, $quote_author, $image_url, $video_url, $link, $user, $type_id);
        $stmt->execute();
        $id = $this->mysqli->insert_id;
        $this->setId($id);
        $hashtag->addHashTags($this, $data['tags']);
        header("Location: post.php?id={$id}");
    }

    public function getId() {
        return $this->id;
    }

    private function setAuthorId($id) {
        $this->author_id = $id;
    }

    private function getTypeIdPost($type)
    {
        switch($type) {
            case 'text':
                return 1;
            case 'quote':
                return 2;
            case 'photo':
                return 3;
            case 'video':
                return 4;
            case 'link':
                return 5;
        }
    }

    private function setId($id)
    {
        if (!isset($this->id)) {
            $this->id = $id;
        }
    }

    private function getAuthorPublicationsCount()
    {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) FROM {$this->table} WHERE user_id = ?");
        $stmt->bind_param('i', $this->author_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array()[0];
    }

    private function getAuthorSubscribersCount()
    {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) FROM subscriptions WHERE subscription_id = ?");
        $stmt->bind_param('i', $this->author_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array()[0];
    }
}

?>

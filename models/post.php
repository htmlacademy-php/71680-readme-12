<?php

class Post {

    private $mysqli;
    public $id;
    public $author_id;

    public function __construct($id, $mysqli) {
        $this->id = $id;
        $this->mysqli = $mysqli;
    }

    public function getPostContent()
    {
        $stmt = $this->mysqli->prepare("
            SELECT p.id, date_create, title, text_content, quote_author, image_url,
            video_url, link, avatar_url, view_number, user_id, u.login, u.date_registration, tc.name_icon as post_type
            FROM posts p
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

    private function setAuthorId($id) {
        $this->author_id = $id;
    }

    public function getPostComments()
    {
        $stmt = $this->mysqli->prepare("
            SELECT c.id, date_create, content, u.login, u.avatar_url FROM comments c
            JOIN users u ON user_id = u.id
            WHERE c.post_id = ?
            ORDER BY date_create DESC"
        );
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_all(MYSQLI_ASSOC);
        $result = $this->secureComments($result);
        return $result;
    }

    private function secureComments($comments) {
        $safe_content = Array();
        foreach ($comments as $comment) {
            $comment['content'] = htmlspecialchars($comment['content']);
            $safe_content[] = $comment;
        }
        return $safe_content;
    }

    public function getCountPostLikes()
    {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array()[0];
    }

    private function getAuthorPublicationsCount()
    {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
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

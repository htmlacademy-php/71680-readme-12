<?php
require_once('Model.php');

class Comment extends Model {

    protected $table = 'comments';

    public function getComments(Post $post)
    {
        $id = $post->getId();
        $stmt = $this->mysqli->prepare("
            SELECT c.id, date_create, content, u.login, u.avatar_url FROM {$this->table} c
            JOIN users u ON user_id = u.id
            WHERE c.post_id = ?
            ORDER BY date_create DESC"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_all(MYSQLI_ASSOC);
        return $this->secureComments($result);
    }

    private function secureComments($comments) {
        $safe_content = Array();
        foreach ($comments as $comment) {
            $comment['content'] = htmlspecialchars($comment['content']);
            $safe_content[] = $comment;
        }
        return $safe_content;
    }
}
?>

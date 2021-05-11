<?php
require_once('Model.php');

class Hashtag extends Model {

    protected $table = 'hashtags';

    public function getHashtags(Post $post)
    {
        $id = $post->getId();

        $stmt = $this->mysqli->prepare("
            SELECT h.hashtag, ph.hashtag_id
            FROM {$this->table} h
            JOIN posts_hashtags ph ON ph.hashtag_id = h.id
            WHERE ph.post_id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_all(MYSQLI_ASSOC);
        return $result;
    }
}

?>

<?php
require_once('Model.php');

class Like extends Model {

    protected $table = 'likes';

    public function getCountLikes(Post $post)
    {
        $id = $post->getId();
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) FROM {$this->table} WHERE post_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array()[0];
    }

}
?>

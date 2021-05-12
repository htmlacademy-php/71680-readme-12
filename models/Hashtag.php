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

    public function addHashTags(Post $post, $tags)
    {
        if (empty($tags)) {
            return;
        }
        $id = $post->getId();
        $tags = str_word_count($tags, 1, 'АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя');
        foreach ($tags as $tag) {
            $stmt = $this->mysqli->prepare("INSERT INTO {$this->table} (hashtag) VALUES (?);");
            $stmt->bind_param('s', $tag);
            $stmt->execute();
            $hashtag_id = $this->mysqli->insert_id;
            $stmt = $this->mysqli->prepare("INSERT INTO posts_hashtags (post_id, hashtag_id) VALUES (?, ?);");
            $stmt->bind_param('is', $id, $hashtag_id);
            $stmt->execute();
        }
    }
}

?>

<div class="post__main">
    <?php if (isset($post['short_text'])) :?>
        <p><?=$post['short_text']; ?></p>
        <a class="post-text__more-link" href="#">Читать далее</a>
    <?php else : ?>
        <p><?=$post['text_content']; ?></p>
    <?php endif?>
</div>

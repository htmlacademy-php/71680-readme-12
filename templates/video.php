<div class="post__main">
    <div class="post-video__block">
        <div class="post-video__preview">
            <!--<img src="img/coast.jpg" alt="Превью к видео" width="760" height="396">-->
            <?=embed_youtube_video($post['video_url']);?>
        </div>
        <!--<div class="post-video__control">
            <button class="post-video__play post-video__play--paused button button--video" type="button"><span class="visually-hidden">Запустить видео</span></button>
            <div class="post-video__scale-wrapper">
            <div class="post-video__scale">
                <div class="post-video__bar">
                <div class="post-video__toggle"></div>
                </div>
            </div>
            </div>
            <button class="post-video__fullscreen post-video__fullscreen--inactive button button--video" type="button"><span class="visually-hidden">Полноэкранный режим</span></button>
        </div>
        <button class="post-video__play-big button" type="button">
            <svg class="post-video__play-big-icon" width="27" height="28">
            <use xlink:href="#icon-video-play-big"></use>
            </svg>
            <span class="visually-hidden">Запустить проигрыватель</span>
        </button>-->
    </div>
</div>

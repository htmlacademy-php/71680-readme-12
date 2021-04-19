<section class="page__main page__main--popular">
    <div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <li class="sorting__item sorting__item--popular">
                        <a class="sorting__link sorting__link--active" href="#">
                            <span>Популярность</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link" href="#">
                            <span>Лайки</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link" href="#">
                            <span>Дата</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип контента:</b>
                <ul class="popular__filters-list filters__list">
                    <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                        <a class="filters__button filters__button--ellipse filters__button--all
                            <?=$filter_type === '' ? 'filters__button--active' : '';?>"
                            href="./">
                            <span>Все</span>
                        </a>
                    </li>
                    <?php foreach ($content_types as $type) :?>
                        <li class="popular__filters-item filters__item">
                            <a
                                class="button filters__button filters__button--<?=$type['name_icon']?>
                                <?=$filter_type === $type['id'] ? 'filters__button--active' : '';?>"
                                href="?type=<?=$type['id']?>"
                            >
                                <span class="visually-hidden"><?=$type['name']?></span>
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-<?=$type['name_icon']?>"></use>
                                </svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="popular__posts">
            <?php foreach ($posts as $post): ?>
            <article class="popular__post post post-<?=$post['post_type']; ?>">
                <header class="post__header">
                    <h2><a href="./post.php?id=<?=$post['id'];?>"><?=$post['title'];?></a></h2>
                </header>
                <div class="post__main">
                    <?php if ($post['post_type'] === 'link') :?>
                        <div class="post-link__wrapper">
                            <a class="post-link__external" href="http://<?=$post['link']; ?>" title="Перейти по ссылке">
                                <div class="post-link__info-wrapper">
                                    <div class="post-link__icon-wrapper">
                                        <img src="https://www.google.com/s2/favicons?domain=vitadental.ru" alt="Иконка">
                                    </div>
                                    <div class="post-link__info">
                                        <h3><?=$post['title']; ?></h3>
                                    </div>
                                </div>
                                <span><?=$post['link']; ?></span>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($post['post_type'] === 'quote') :?>
                        <blockquote>
                            <p>
                                <?=$post['text_content']; ?>
                            </p>
                            <cite>Неизвестный Автор</cite>
                        </blockquote>
                    <?php endif; ?>

                    <?php if ($post['post_type'] === 'photo') :?>
                        <div class="post-photo__image-wrapper">
                            <img src="img/<?=$post['image_url']; ?>" alt="Фото от пользователя" width="360" height="240">
                        </div>
                    <?php endif; ?>

                    <?php if ($post['post_type'] === 'text') :?>
                        <?php if (isset($post['short_text'])) :?>
                            <p><?=$post['short_text']; ?></p>
                            <a class="post-text__more-link" href="#">Читать далее</a>
                        <?php else: ?>
                            <p><?=$post['text_content']; ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <footer class="post__footer">
                    <div class="post__author">
                        <a class="post__author-link" href="#" title="<?=$post['date_format']; ?>">
                            <div class="post__avatar-wrapper">
                                <?php if ($post['avatar_url']) :?>
                                    <!--укажите путь к файлу аватара-->
                                    <img class="post__author-avatar" src="img/<?=$post['avatar']; ?>" alt="Аватар пользователя">
                                <?php endif; ?>
                            </div>
                            <div class="post__info">
                                <b class="post__author-name"><?=$post['login']; ?></b>
                                <time class="post__time" datetime="<?=$post['date_create']; ?>"><?=$post['date_relative']; ?></time>
                            </div>
                        </a>
                    </div>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span>0</span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span>0</span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                        </div>
                    </div>
                </footer>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

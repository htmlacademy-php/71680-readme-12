<main class="page__main page__main--adding-post">
    <div class="page__main-section">
        <div class="container">
            <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
            <div class="adding-post__tabs-wrapper tabs">
                <div class="adding-post__tabs filters">
                    <ul class="adding-post__tabs-list filters__list tabs__list">
                        <?php foreach ($types as $type) :?>
                            <li class="adding-post__tabs-item filters__item">
                                <a class="adding-post__tabs-link filters__button filters__button--<?=$type['name_icon'];?> tabs__item  tabs__item--active button <?=$active_tab === $type['name_icon'] ? 'filters__button--active' : '';?>">
                                    <svg class="filters__icon" width="22" height="18">
                                        <use xlink:href="#icon-filter-<?=$type['name_icon'];?>"></use>
                                    </svg>
                                    <span><?=$type['name'];?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="adding-post__tab-content">
                <section class="adding-post__text tabs__content <?=$active_tab === 'text' ? 'tabs__content--active' : '';?>">
                        <h2 class="visually-hidden">Форма добавления текста</h2>
                        <form class="adding-post__form form" action="add.php" method="post">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <?php include('title-input.php');?>
                                    <div class="adding-post__textarea-wrapper form__textarea-wrapper <?=isset($errors['post-text']) ? 'form__input-section--error' : '';?>">
                                        <label class="adding-post__label form__label" for="post-text">Текст поста <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <textarea class="adding-post__textarea form__textarea form__input" id="post-text" placeholder="Введите текст публикации" name="post-text"><?=$data['post-text'] ?? '';?></textarea>
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Ошибка</h3>
                                                <p class="form__error-desc"><?=$errors['post-text'] ?? '';?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php include('tags-input.php');?>
                                </div>
                                <?php if (!empty($errors) && $data['type-post'] === 'text') :?>
                                <div class="form__invalid-block">
                                    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                    <ul class="form__invalid-list">
                                        <?php foreach($errors as $error) :?>
                                            <li class="form__invalid-item"><?=$error;?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php include('submit.php');?>
                            <input type="hidden" name="type-post" value="text"/>
                        </form>
                    </section>

                    <section class="adding-post__quote tabs__content <?=$active_tab === 'quote' ? 'tabs__content--active' : '';?>">
                        <h2 class="visually-hidden">Форма добавления цитаты</h2>
                        <form class="adding-post__form form" action="add.php" method="post">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <?php include('title-input.php');?>
                                    <div class="adding-post__input-wrapper form__textarea-wrapper">
                                        <label class="adding-post__label form__label" for="cite-text">Текст цитаты <span class="form__input-required">*</span></label>
                                        <div class="form__input-section <?=isset($errors['post-text']) ? 'form__input-section--error' : '';?>">
                                            <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input" id="cite-text" placeholder="Текст цитаты" name="post-text"><?=$data['post-text'] ?? '';?></textarea>
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Ошибка</h3>
                                                <p class="form__error-desc"><?=$errors['post-text'] ?? '';?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="adding-post__textarea-wrapper form__input-wrapper <?=isset($errors['quote-author']) ? 'form__input-section--error' : '';?>">
                                        <label class="adding-post__label form__label" for="quote-author">Автор <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="quote-author" type="text" name="quote-author" value="<?=$data['quote-author'] ?? '';?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Ошибка</h3>
                                                <p class="form__error-desc"><?=$errors['quote-author'] ?? '';?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php include('tags-input.php');?>
                                </div>
                                <?php if (!empty($errors) && $data['type-post'] === 'quote') :?>
                                <div class="form__invalid-block">
                                    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                    <ul class="form__invalid-list">
                                        <?php foreach($errors as $error) :?>
                                            <li class="form__invalid-item"><?=$error;?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php include('submit.php');?>
                            <input type="hidden" name="type-post" value="quote"/>
                        </form>
                    </section>

                    <section class="adding-post__photo tabs__content <?=$active_tab === 'photo' ? 'tabs__content--active' : '';?>">
                        <h2 class="visually-hidden">Форма добавления фото</h2>
                        <form class="adding-post__form form" action="add.php" method="post" enctype="multipart/form-data">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <?php include('title-input.php');?>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета</label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="photo-url" type="text" name="photo-link" placeholder="Введите ссылку" value="<?=$data['photo-link'] ?? '';?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообщения</h3>
                                                <p class="form__error-desc">Текст сообщения об ошибке, подробно объясняющий, что не так.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php include('tags-input.php');?>
                                </div>
                                <?php if (!empty($errors) && $data['type-post'] === 'photo') :?>
                                <div class="form__invalid-block">
                                    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                    <ul class="form__invalid-list">
                                        <?php foreach($errors as $error) :?>
                                            <li class="form__invalid-item"><?=$error;?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="adding-post__input-file-container form__input-container form__input-container--file">
                                <div class="adding-post__input-file-wrapper form__input-file-wrapper">
                                    <div class="adding-post__file-zone adding-post__file-zone--photo form__file-zone dropzone">
                                        <input class="adding-post__input-file form__input-file" id="userpic-file-photo" type="file" name="file-photo" title=" ">
                                        <div class="form__file-zone-text">
                                            <span>Перетащите фото сюда</span>
                                        </div>
                                    </div>
                                    <button class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button" type="button">
                                        <span>Выбрать фото</span>
                                        <svg class="adding-post__attach-icon form__attach-icon" width="10" height="20">
                                            <use xlink:href="#icon-attach"></use>
                                        </svg>
                                    </button>
                                </div>
                                <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">

                                </div>
                            </div>
                            <?php include('submit.php');?>
                            <input type="hidden" name="type-post" value="photo"/>
                        </form>
                    </section>

                    <section class="adding-post__video tabs__content <?=$active_tab === 'video' ? 'tabs__content--active' : '';?>">
                        <h2 class="visually-hidden">Форма добавления видео</h2>
                        <form class="adding-post__form form" action="add.php" method="POST" enctype="multipart/form-data">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                <?php include('title-input.php');?>
                                    <div class="adding-post__input-wrapper form__input-wrapper <?=isset($errors['video-url']) ? 'form__input-section--error' : '';?>">
                                        <label class="adding-post__label form__label" for="video-url">Ссылка youtube <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="video-url" type="text" name="video-url" placeholder="Введите ссылку" value="<?=$data['video-url'] ?? '' ;?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Ошибка</h3>
                                                <p class="form__error-desc"><?=$errors['video-url'] ?? '';?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php include('tags-input.php');?>
                                </div>
                                <?php if (!empty($errors) && $data['type-post'] === 'video') :?>
                                <div class="form__invalid-block">
                                    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                    <ul class="form__invalid-list">
                                        <?php foreach($errors as $error) :?>
                                            <li class="form__invalid-item"><?=$error;?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php include('submit.php');?>
                            <input type="hidden" name="type-post" value="video"/>
                        </form>
                    </section>

                    <section class="adding-post__link tabs__content <?=$active_tab === 'link' ? 'tabs__content--active' : '';?>">
                        <h2 class="visually-hidden">Форма добавления ссылки</h2>
                        <form class="adding-post__form form" action="add.php" method="post">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <?php include('title-input.php');?>
                                    <div class="adding-post__textarea-wrapper form__input-wrapper <?=isset($errors['post-link']) ? 'form__input-section--error' : '';?>">
                                        <label class="adding-post__label form__label" for="post-link">Ссылка <span class="form__input-required">*</span></label>
                                        <div class="form__input-section">
                                            <input class="adding-post__input form__input" id="post-link" type="text" name="post-link" value="<?=$data['post-link'] ?? '';?>">
                                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок сообщения</h3>
                                                <p class="form__error-desc"><?=$errors['post-link'] ?? '';?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php include('tags-input.php');?>
                                </div>
                                <?php if (!empty($errors) && $data['type-post'] === 'link') :?>
                                <div class="form__invalid-block">
                                    <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                    <ul class="form__invalid-list">
                                        <?php foreach($errors as $error) :?>
                                            <li class="form__invalid-item"><?=$error;?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php include('submit.php');?>
                            <input type="hidden" name="type-post" value="link"/>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</main>

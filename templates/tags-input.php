<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="post-tags">Теги</label>
    <div class="form__input-section">
        <input class="adding-post__input form__input" id="post-tags" type="text" name="tags" placeholder="Введите теги" value="<?=$data['tags'] ?? '';?>">
        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
        <div class="form__error-text">
            <h3 class="form__error-title">Ошибка</h3>
            <p class="form__error-desc">Теги должны быть разделены пробелами</p>
        </div>
    </div>
</div>

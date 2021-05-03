<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for="link-heading">Заголовок <span class="form__input-required">*</span></label>
    <div class="form__input-section <?=isset($errors['post-title']) ? 'form__input-section--error' : '';?>">
        <input class="adding-post__input form__input" id="link-heading" type="text" name="post-title" placeholder="Введите заголовок" value="<?=$data['post-title'] ?? '';?>">
        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
        <div class="form__error-text">
            <h3 class="form__error-title">Обязательное поле</h3>
            <p class="form__error-desc"><?=$errors['post-title'] ?? '';?></p>
        </div>
    </div>
</div>

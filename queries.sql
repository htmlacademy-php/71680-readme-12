/*Добавления типов контента для поста*/
INSERT INTO type_contents (`name`, `name_ikon`) VALUES
('Текст', 'text'),
('Цитата', 'quote'),
('Картинка', 'photo'),
('Видео', 'video'),
('Ссылка', 'link');

/*Добавления юзеров*/
INSERT INTO users (`date_registration`, `email`, `login`, `password`, `avatar_url`) VALUES
('2020-11-12 10:00:00', 'lar@mail.ru', 'Larisa', 'pass', NULL),
('2021-01-01 10:00:00', 'alextest@mail.ru', 'Alex', 'pass', NULL),
('2021-02-02 12:01:00', 'harrytest@mail.ru', 'Harry', 'pass', NULL);

/*Добавляем посты*/
INSERT INTO posts (`date_create`, `title`, `text_content`, `quote_author`, `image_url`,
`video_url`, `link`, `view_number`, `user_id`, `type_id`)
VALUES
('2020-12-02 15:32:03', 'Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный автор', NULL,
NULL, NULL, 23, 1, 2),

('2021-01-05 23:02:03', 'Игра престолов', 'Действие «Игры престолов» происходит в вымышленном мире, напоминающем средневековую Европу. В сериале одновременно действует множество персонажей и развивается несколько сюжетных линий. Основных сюжетных арок три: первая посвящена борьбе нескольких влиятельных домов за Железный Трон Семи Королевств либо за независимость от него; вторая — потомку свергнутой династии правителей, принцессе-изгнаннице, планирующей вернуть престол; третья — древнему братству, охраняющему государство от угроз с севера',
NULL, NULL,NULL, NULL, 16, 2, 1),

('2021-01-15 20:16:13', 'Наконец, обработал фотки!', NULL, NULL, 'rock-medium.jpg',
NULL, NULL, 36, 3, 3),

('2021-01-17 12:33:26', 'Моя мечта', NULL, NULL, 'coast-medium.jpg',
NULL, NULL, 12, 1, 3),

('2021-01-19 19:13:14', 'Лучшие курсы', NULL, NULL, NULL,
NULL, 'www.htmlacademy.ru', 12, 1, 5);

/*добавляем комментарии*/
INSERT INTO comments (`date_create`, `content`, `user_id`, `post_id`) VALUES
('2021-01-15 20:26:28', 'Круто получилось!))', 2, 3),
('2021-01-15 20:28:38', 'Мастер!', 1, 3),
('2021-01-17 12:38:06', 'Мечтать не вредно))', 2, 4);

/*получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента*/
SELECT
p.id,
date_create,
title,
text_content,
quote_author,
image_url,
video_url,
link,
view_number, u.login, tc.name_ikon
FROM posts p
JOIN users u ON p.user_id = u.id
JOIN type_contents tc ON p.type_id = tc.id
ORDER BY view_number DESC;

/*получить список постов для конкретного пользователя*/
SELECT * FROM posts WHERE user_id = 1;

/*получить список комментариев для одного поста, в комментариях должен быть логин пользователя*/
SELECT c.id, date_create, content, u.login FROM comments c JOIN users u ON c.user_id = u.id WHERE post_id = 3;

/*добавить лайк к посту*/
INSERT INTO likes ('user_id', 'post_id') VALUES (2, 3);

/*подписаться на пользователя*/
INSERT INTO subscriptions ('author_id', 'subscription_id') VALUES (2, 3);

CREATE DATABASE readme
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;
USE readme;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_registration DATETIME NOT NULL,
  email CHAR(128) NOT NULL UNIQUE,
  login CHAR(128) NOT NULL UNIQUE,
  password CHAR(128) NOT NULL,
  avatar_url TINYTEXT
);

CREATE TABLE hashtags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hashtag TINYTEXT NOT NULL UNIQUE
);

CREATE TABLE type_contents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name TINYTEXT NOT NULL UNIQUE,
  name_ikon TINYTEXT NOT NULL UNIQUE
);

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_create DATETIME NOT NULL,
  title VARCHAR(128) NOT NULL,
  text_content TEXT,
  quote_author TINYTEXT,
  image_url TINYTEXT,
  video_url TINYTEXT,
  link TINYTEXT,
  view_number INT,
  user_id INT,
  type_id INT,
  FOREIGN KEY (user_id) REFERENCES users (id),
  FOREIGN KEY (type_id) REFERENCES type_contents (id)
);

CREATE INDEX title ON posts (title);
CREATE INDEX text_content ON posts (text_content);

CREATE TABLE posts_hashtags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT,
  hashtag_id INT,
  FOREIGN KEY (post_id) REFERENCES posts (id),
  FOREIGN KEY (hashtag_id) REFERENCES hashtags (id)
);

CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_create DATETIME NOT NULL,
  content VARCHAR(128) NOT NULL,
  user_id INT,
  post_id INT,
  FOREIGN KEY (user_id) REFERENCES users (id),
  FOREIGN KEY (post_id) REFERENCES posts (id)
);

CREATE TABLE likes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  post_id INT,
  FOREIGN KEY (user_id) REFERENCES users (id),
  FOREIGN KEY (post_id) REFERENCES posts (id)
);

CREATE TABLE subscriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  author INT,
  subscription INT,
  FOREIGN KEY (author) REFERENCES users (id),
  FOREIGN KEY (subscription) REFERENCES users (id)
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_create DATETIME NOT NULL,
  content TEXT NOT NULL,
  sender INT,
  recipient INT,
  FOREIGN KEY (sender) REFERENCES users (id),
  FOREIGN KEY (recipient) REFERENCES users (id)
);

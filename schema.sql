CREATE DATABASE yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

/* Созданы таблицы для всех сущностей, свойства UNIQUE и NOT NULL добавила
по своему усмотрению, т.к. в ТЗ не нашла четкого указания, возможно где-то ошиблась*/

/* Категории */
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name CHAR(255) NOT NULL UNIQUE
);

/* Лоты */
CREATE TABLE lots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  name char(255)  NOT NULL,
  description TEXT,
  img_url CHAR(255),
  start_price INT NOT NULL,
  date_end TIMESTAMP,
  step INT NOT NULL,
  user_author_id INT NOT NULL,
  user_victor_id INT,
  category_id INT NOT NULL
);

/* Ставки */
CREATE TABLE rates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  summ INT NOT NULL,
  user_id INT NOT NULL,
  lot_id INT NOT NULL
);

/* Пользователи */
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email CHAR(255) NOT NULL UNIQUE,
  name CHAR(255) NOT NULL UNIQUE,
  password CHAR(64) NOT NULL,
  img_url CHAR(255),
  contacts CHAR(255)
);

/* Для тренировки создала данные в таблицах похожие на те что были в массивах*/
INSERT INTO categories(name) VALUES ('Доски и лыжи'), ('Крепления'), ('Ботинки'), ('Одежда'), ('Инструменты'), ('Разное');

INSERT INTO lots SET  name='2014 Rossignol District Snowboard', img_url='img/lot-1.jpg', start_price=10999, step='100', user_author_id=3, category_id=1;
INSERT INTO lots SET  name='DC Ply Mens 2016/2017 Snowboard', img_url='img/lot-2.jpg', start_price=159999, step='100', user_author_id=2, category_id=1;
INSERT INTO lots SET  name='Крепления Union Contact Pro 2015 года размер L/XL', img_url='img/lot-3.jpg', start_price=8000, step='100', user_author_id=3, category_id=2;
INSERT INTO lots SET  name='Ботинки для сноуборда DC Mutiny Charocal', img_url='img/lot-4.jpg', start_price=10999, step='100', user_author_id=1, category_id=3;
INSERT INTO lots SET  name='Куртка для сноуборда DC Mutiny Charocal', img_url='img/lot-5.jpg', start_price=7500, step='100', user_author_id=1, category_id=4;
INSERT INTO lots SET  name='Маска Oakley Canopy', img_url='img/lot-6.jpg', start_price=5400, step='100', user_author_id=1, category_id=6;

/* Таблицу с пользователями заполнила произвольными значениями для тренировки */
INSERT INTO users SET email='test1@ya.ru', name='Геннадий', password='123', contacts='2-222-222';
INSERT INTO users SET email='test2@ya.ru', name='Николай', password='543', contacts='3-333-333';
INSERT INTO users SET email='test3@ya.ru', name='Анна', password='096', contacts='4-444-444';

/* Созданы индексы для поиска - уникальные и обычные*/
CREATE UNIQUE INDEX category_name ON categories(name);
CREATE UNIQUE INDEX user_email ON users(email);
CREATE UNIQUE INDEX user_name ON users(name);
CREATE INDEX lot_name ON lots(name);
CREATE INDEX lot_start_price ON lots(start_price);

/* Потренировалась выбирать по связанным значениям*/
/* Лоты пользователя 3- Анна*/
/*SELECT l.name, l.start_price FROM lots l
JOIN users u
ON l.user_author_id=u.id
WHERE u.id=3;*/

/* Лоты в категории 1 - Доски и лыжи*/
/*SELECT l.name, l.start_price FROM lots l
JOIN categories c
ON l.category_id=c.id
WHERE c.id=1;*/

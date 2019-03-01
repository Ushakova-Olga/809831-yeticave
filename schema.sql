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
  date_end TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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
  name CHAR(255) NOT NULL,
  password CHAR(64) NOT NULL,
  img_url CHAR(255),
  contacts CHAR(255)
);

/* Созданы индексы для поиска - уникальные и обычные*/
CREATE UNIQUE INDEX category_name ON categories(name);
CREATE UNIQUE INDEX user_email ON users(email);
CREATE UNIQUE INDEX user_name ON users(name);
CREATE INDEX lot_name ON lots(name);
CREATE INDEX lot_start_price ON lots(start_price);

CREATE FULLTEXT INDEX lot_ft_search ON lots(name, description);

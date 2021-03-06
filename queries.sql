/* Вносим данные в таблицу категорий*/
INSERT INTO categories(name) VALUES ('Доски и лыжи'), ('Крепления'), ('Ботинки'), ('Одежда'), ('Инструменты'), ('Разное');

/* Вносим данные в таблицу лотов */
INSERT INTO lots SET  date_add='2019-03-08 10:00:00', date_end='2019-03-11 18:00:00', name='2014 Rossignol District Snowboard', img_url='img/lot-1.jpg', start_price=10999, step='100', user_author_id=2, category_id=1;
INSERT INTO lots SET  date_add='2019-03-09 03:31:00', date_end='2019-03-11 18:00:00', name='DC Ply Mens 2016/2017 Snowboard', img_url='img/lot-2.jpg', start_price=159999, step='100', user_author_id=2, category_id=1;
INSERT INTO lots SET  date_add='2019-03-09 15:10:00', date_end='2019-03-11 18:00:00', name='Крепления Union Contact Pro 2015 года размер L/XL', img_url='img/lot-3.jpg', start_price=8000, step='100', user_author_id=3, category_id=2;
INSERT INTO lots SET  date_add='2019-03-10 05:05:00', date_end='2019-03-11 18:00:00', name='Ботинки для сноуборда DC Mutiny Charocal', img_url='img/lot-4.jpg', start_price=10999, step='100', user_author_id=1, category_id=3;
INSERT INTO lots SET  date_add='2019-03-10 06:00:00', date_end='2019-03-11 18:00:00', name='Куртка для сноуборда DC Mutiny Charocal', img_url='img/lot-5.jpg', start_price=7500, step='100', user_author_id=1, category_id=4;
INSERT INTO lots SET  date_add='2019-03-10 07:35:00', date_end='2019-03-11 18:00:00', name='Маска Oakley Canopy', img_url='img/lot-6.jpg', start_price=5400, step='100', user_author_id=1, category_id=6;

/* Создана ставка для лота 1 */
INSERT INTO rates SET date_add='2019-03-09 16:40:00', summ=11999, user_id=3, lot_id=1;

/* Созданы 3 ставки для лота 4 */
INSERT INTO rates SET date_add='2019-03-10 05:08:00', summ=11499, user_id=3, lot_id=4;
INSERT INTO rates SET date_add='2019-03-10 06:00:00', summ=12499, user_id=3, lot_id=4;
INSERT INTO rates SET date_add='2019-03-10 07:06:00', summ=13499, user_id=3, lot_id=4;

/* Таблицу с пользователями заполнила произвольными значениями */
INSERT INTO users SET email='test1@ya.ru', name='Геннадий', password='123', contacts='2-222-222';
INSERT INTO users SET email='test2@ya.ru', name='Николай', password='543', contacts='3-333-333';
INSERT INTO users SET email='ewelina-2016@yandex.ru', name='Анна', password='096', contacts='4-444-444';

/* получить все категории */
SELECT * FROM categories;

/* получить 10 самых новых, открытых лотов.
Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории;*/
/* в объявлении должна быть связь с пользователем и категорией */
SELECT l.name, l.start_price, l.img_url, c.name, u.name, l.date_add  FROM lots l
JOIN users u ON u.id=l.user_author_id
JOIN categories c ON c.id=l.category_id
WHERE l.user_winner_id IS NULL
ORDER BY l.date_add DESC
LIMIT 10;

/* показать лот по его id. Получите также название категории, к которой принадлежит лот */
SELECT l.name, c.name FROM lots l
JOIN categories c
ON l.category_id=c.id
WHERE l.id=1;

/* обновить название лота по его идентификатору;*/
UPDATE lots SET name='Крепления Union Contact Pro 2015 года' WHERE lots.id=3;

/* получить список из 10 самых свежих ставок для лота по его идентификатору;*/
SELECT l.name, l.start_price, r.summ, r.date_add FROM lots l
JOIN rates r ON r.lot_id=l.id
WHERE l.id=4
ORDER BY r.date_add DESC
LIMIT 10;

-- Создание таблицы users, если она еще не существует
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,      -- Уникальный идентификатор пользователя
    username VARCHAR(50) NOT NULL UNIQUE,   -- Имя пользователя, уникальное
    password VARCHAR(255) NOT NULL,         -- Хеш пароля пользователя
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Время создания записи пользователя
);

-- Вставка данных в таблицу users
INSERT INTO users (username, password) VALUES
('user1', 'password_hash1'),
('user2', 'password_hash2');

-- Создание таблицы todos, если она еще не существует
CREATE TABLE IF NOT EXISTS todos (
    id INT AUTO_INCREMENT PRIMARY KEY,         -- Уникальный идентификатор задачи
    title VARCHAR(255) NOT NULL,               -- Название задачи
    completed TINYINT DEFAULT 0,               -- Статус завершения задачи (0 - не завершена, 1 - завершена)
    user_id INT NOT NULL,                      -- Идентификатор пользователя, которому принадлежит задача
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Время создания задачи
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  -- Время последнего обновления
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE  -- Внешний ключ, ссылающийся на таблицу users
);

-- Вставка данных в таблицу todos
INSERT INTO todos (title, completed, user_id) VALUES
('Task 1', 0, 1),
('Task 2', 1, 1),
('Task 3', 0, 2),
('Task 4', 0, 2);


-- CREATE TABLE todos (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     title VARCHAR(255) NOT NULL,
--     completed BOOLEAN NOT NULL DEFAULT 0,
--     user_id INT NOT NULL,
--     FOREIGN KEY (user_id) REFERENCES users(id)
-- );

-- CREATE TABLE users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     username VARCHAR(255) NOT NULL,
--     password VARCHAR(255) NOT NULL,
--     email VARCHAR(255) NOT NULL
-- );

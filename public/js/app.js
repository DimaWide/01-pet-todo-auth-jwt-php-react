// URL for interacting with the REST API
const apiUrl = 'http://learn.list/dev_02.todo-php-rest-crud-1/api/todos';
const apiUrlAuth = 'http://learn.list/dev_02.todo-php-rest-crud-1/api/auth';

function showLoginForm() {
    document.getElementById('login-section').style.display = 'block';
    document.getElementById('register-section').style.display = 'none';
    document.getElementById('task-manager').style.display = 'none';
}

function showRegisterForm() {
    document.getElementById('login-section').style.display = 'none';
    document.getElementById('register-section').style.display = 'block';
    document.getElementById('task-manager').style.display = 'none';
}

function showTaskManager() {
    document.getElementById('login-section').style.display = 'none';
    document.getElementById('register-section').style.display = 'none';
    document.getElementById('task-manager').style.display = 'block';
}


function logout() {
    deleteCookie('token');
    deleteCookie('refresh_token');
    showLoginForm();
}


// Helper functions for managing cookies
function setCookie(name, value, days = 7) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000)); // Default to 7 days
    document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
}

function getCookie(name) {
    const nameEQ = `${name}=`;
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function deleteCookie(name) {
    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/`;
}


async function login(event) {
    event.preventDefault();

    const username = document.getElementById('login-username').value;
    const password = document.getElementById('login-password').value;

    try {
        const response = await fetch(apiUrlAuth + '/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();

        if (response.ok) {
            // Успешный вход
            alert('Login successful!');
            setCookie('token', data.token);  // Сохраняем токен в localStorage
            window.location.href = 'http://learn.list/dev_02.todo-php-rest-crud-1/public/'; // Перенаправляем на страницу панели
        } else {
            // Ошибка на сервере
            throw new Error(data.message || 'Login failed');
        }
    } catch (error) {
        console.error('Error:', error);

        // Показать сообщение об ошибке
        const errorMessage = error.message || 'An unexpected error occurred';
        document.getElementById('login-error-text').innerText = errorMessage;
        document.getElementById('login-error-message').style.display = 'block';
    }
}


// Обработчик для закрытия ошибки
document.getElementById('login-error-message').querySelector('.close').addEventListener('click', () => {
    document.getElementById('login-error-message').style.display = 'none';
});

async function register(event) {
    event.preventDefault();

    const username = document.getElementById('register-username').value;
    const password = document.getElementById('register-password').value;

    try {
        const response = await fetch(apiUrlAuth + '/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();

        if (response.ok) {
            if (data.message === 'User registered successfully') {
                alert('Registration successful! You can now log in.');
                showLoginForm();
            }
        } else {
            throw new Error(data.message || 'Registration failed');
        }
    } catch (error) {
        console.error('Error:', error);

        // Показать сообщение об ошибке
        const errorMessage = error.message || 'An unexpected error occurred';
        document.getElementById('register-error-text').innerText = errorMessage;
        document.getElementById('register-error-message').style.display = 'block';
    }
}

// Обработчик для закрытия ошибки


document.getElementById('register-error-message').querySelector('.close').addEventListener('click', () => {
    document.getElementById('register-error-message').style.display = 'none';
});



// API URL (измените на свой URL)

// Получаем токен из localStorage (или другого места)
const token = getCookie('token');
// Функция для получения списка задач
async function fetchTasks() {
    console.log(token)
    try {
        const response = await fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`  // Токен в заголовке
            }
        });

        if (!response.ok) {
            throw new Error('Error fetching tasks');
        }

        const tasks = await response.json();
        console.log(tasks)
        displayTasks(tasks);
    } catch (error) {
        console.error('Error:', error);
        //alert('Failed to load tasks.');
    }
}

// Функция для отображения списка задач
function displayTasks(tasks) {
    const tasksContainer = document.querySelector('.data-list');
    tasksContainer.innerHTML = '';  // Очистить текущий список задач


    tasks.forEach(task => {
        const taskElement = document.createElement('div');
        taskElement.classList.add('data-item', 'item', 'task');
        if (Number(task.completed)) {
            taskElement.classList.add('completed');
        }

        taskElement.innerHTML = `
            <div class="data-item-content">
                <div class="data-item-b1">
                    <div class="ui checkbox">
                        <input type="checkbox" 
                               id="task-completed-${task.id}" 
                               ${Number(task.completed) ? "checked" : ""} 
                               onclick="toggleTaskCompletion(${task.id}, ${Number(task.completed)})">
                        <label for="task-completed-${task.id}">
                            ${Number(task.completed) ? '' : ''}
                        </label>
                    </div>
                    <div class="header">${task.title}</div>
                </div>
                <div class="extra">
                    <button onclick="editTask(${task.id})" class="data-edit   button tiny">
                        <i class="edit icon"></i> 
                    </button>
                    <button onclick="deleteTask(${task.id})" class="data-delete   button tiny">
                        <i class="trash icon"></i> 
                    </button>
                </div>
            </div>
        `;

        tasksContainer.appendChild(taskElement);
    });
}

// Функция для добавления новой задачи
async function addTask(event) {
    const taskTitle = document.getElementById('task-title').value.trim();
    if (!taskTitle) {
        alert('Task title cannot be empty');
        return;
    }

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ title: taskTitle, completed: 0 })
        });

        if (!response.ok) throw new Error('Error adding task');

        document.getElementById('task-title').value = '';  // Очистка поля
        fetchTasks();
    } catch (error) {
        console.error('Error:', error);
    }
}

// Функция для удаления задачи
async function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
        try {
            const response = await fetch(`${apiUrl}/${taskId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`  // Токен в заголовке
                }
            });

            if (!response.ok) {
                throw new Error('Error deleting task');
            }

            fetchTasks();  // Перезагружаем список задач
        } catch (error) {
            console.error('Error:', error);
            //alert('Failed to delete task.');
        }
    }
}

// Функция для редактирования задачи
async function editTask(taskId) {
    const newTitle = prompt('Enter the new task title');
    if (newTitle) {
        try {
            const response = await fetch(`${apiUrl}/${taskId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`  // Токен в заголовке
                },
                body: JSON.stringify({ title: newTitle })
            });

            if (!response.ok) {
                throw new Error('Error updating task');
            }

            fetchTasks();  // Перезагружаем список задач
        } catch (error) {
            console.error('Error:', error);
            //alert('Failed to update task.');
        }
    }
}

// Функция для изменения статуса выполнения задачи
async function toggleTaskCompletion(taskId, currentStatus) {
    const newStatus = currentStatus ? 0 : 1;  // Переключаем между 0 (не выполнено) и 1 (выполнено)
    try {
        const response = await fetch(`${apiUrl}/${taskId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`  // Токен в заголовке
            },
            body: JSON.stringify({ completed: newStatus })  // Передаем новый статус
        });

        if (!response.ok) {
            throw new Error('Error changing task status');
        }

        fetchTasks();  // Перезагружаем список задач
    } catch (error) {
        console.error('Error:', error);
        //alert('Failed to change task status.');
    }
}

// Инициализация страницы: загрузка задач
document.addEventListener('DOMContentLoaded', () => {
    //  fetchTasks();
    // Обработчик для добавления новой задачи через форму
    const taskForm = document.getElementById('task-form');
    if (taskForm) {
        taskForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const taskTitle = taskForm.querySelector('input[name="task-title"]').value;
            if (taskTitle) {
                addTask(taskTitle);
                taskForm.reset();  // Сбросить форму после отправки
            } else {
                //alert('Enter a task title');
            }
        });
    }

});

// document.addEventListener('DOMContentLoaded', () => {
//     const token = getCookie('token');
//     isTokenValid(token)
//     if (token) {
//         showTaskManager();  // Показать интерфейс задач
//         fetchTasks();       // Загрузить задачи с сервера
//     } else {
//         showLoginForm();    // Показать форму входа
//     }
// });


// Функция для проверки валидности JWT
function isTokenValid(token) {
    return true;
    // Разбираем токен (JWT состоит из 3 частей, разделённых точками)
    const [header, payload, signature] = token.split('.');

    // Декодируем базовый64url payload (без проверки подписи для простоты)
    const decodedPayload = JSON.parse(atob(payload.replace(/_/g, '/').replace(/-/g, '+')));
    console.log(decodedPayload)

    // Проверяем срок действия (exp) токена
    const currentTime = Math.floor(Date.now() / 1000);
    return decodedPayload.exp > currentTime;
}



document.addEventListener('DOMContentLoaded', () => {
    const token = getCookie('token');
    const refreshToken = getCookie('refresh_token');

    // Проверка валидности access token
    if (token && isTokenValid(token)) {
        showTaskManager();  // Показать интерфейс задач
        fetchTasks();       // Загрузить задачи с сервера
    } else if (refreshToken) {
        // Если access token не валиден, пытаемся обновить его с помощью refresh token
        refreshAccessToken(refreshToken)
            .then(newToken => {
                if (newToken) {
                    // Сохраняем новый access token в cookies
                    setCookie('token', newToken, 15); // Срок действия 15 минут
                    showTaskManager();  // Показать интерфейс задач
                    fetchTasks();       // Загрузить задачи с сервера
                } else {
                    showLoginForm();  // Показать форму входа
                }
            })
            .catch(error => {
                console.error('Error refreshing token:', error);
                showLoginForm();  // Показать форму входа, если не удалось обновить токен
            });
    } else {
        showLoginForm();    // Показать форму входа, если нет токена или refresh token
    }
});



// Функция для обновления access token с использованием refresh token
async function refreshAccessToken(refreshToken) {
    console.log('reffresh')
  return
    const response = await fetch(apiUrlAuth + '/refresh', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ refresh_token: refreshToken }),
    });

    if (response.ok) {
        const data = await response.json();
        window.location.href = 'http://learn.list/dev_02.todo-php-rest-crud/public/'; // Перенаправляем на страницу панели
        return data.access_token;  // Возвращаем новый access token
    } else {
        throw new Error('Failed to refresh token');
    }
}
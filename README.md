## Warden install
Для розгортання в окремому середовищі використовується [warden](https://docs.warden.dev/installing.html)

## Запуск контейнерів за допомогою Warden
- warden svc up
- warden env up

## Конфігурація Laravel .env файла
- Необхідно добавити значення для **JWT_SECRET**
- Підключитися до контейнера php **warden shell**
  - В контейнері php конати команду **php artisan storage:link**
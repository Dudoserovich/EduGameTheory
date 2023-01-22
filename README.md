# EduGameTheory
Для разработки используется docker версии 20.10.19
- [установка docker для Linux](https://docs.docker.com/engine/install/ubuntu/#set-up-the-repository) 
- [установка Docker desktop для Windows/Mac](https://www.docker.com/)

> Для Linux, чтобы не запускать команды группы докер из под sudo, 
> нужно прописать ```sudo usermod -a -G docker <username>``` и перегагрузиться

Front пока засран говном 😢

## Организационные вопросы
### Общие моменты
- Все документашки по api находятся по адресу: ```localhost/api/doc```
- Все текущие задачки лежат на kanban доске на гитхабе
### Git
- Не забываем делать git pull перед началом работы!
- Перед началом работы над новой задачкой создаём отдельную ветку вида #issue_id, 
  где issue_id - id задачки и сразу же переходим на свою ветку 
  для работы на ней (```git checkout -b "#issue_id"```)
- Чтобы залить свою ветку на гит добавляем все файлы и коммитим, 
  если ещё не сделали этого:
  
  ```
  git add /files
  git commit
  ```
- После чего пушим: ```git push --set-upstream origin new-branch```
- Отправляем pull request на самом гитхабе и вместе смотрим чего там такого сделали
- Если всё ок и конфликтов при merge веток быть не должно, мерджим в main

Не забываем делать git pull перед началом работы!!!

## Начальные настройки frontend

```bash
docker compose run --rm --no-deps php-fpm composer install
docker compose run --rm --no-deps frontend npm install
```

## Начальные настройки backend

### Установка зависимостей
```bash
docker compose run --rm --no-deps php-fpm composer install
```
#### Генерация jwt ключа
```bash
php bin/console lexik:jwt:generate-keypair
```
#### Установка последней миграции (структуры бд)
```bash
php bin/console doctrine:migrations:migrate
```
## Работа с Docker контейнерами
#### Запуск dev-сервера для разработки
```bash
docker compose up -d --force-recreate
```
#### Запуск контейнера с php-fpm
```bash
docker compose exec php-fpm sh
```
#### Запуск контейнера с бд
```bash
docker compose exec mysql mysql -uuser -ppassword dromupgrade
```
#### Пересобрать для обновления зависимостей и Dockerfile
```bash
docker compose up -d --force-recreate --build
```

## Работа с бд и Doctrine
### Пересоздание бд
Дроп бд:
```bash
php bin/console doctrine:database:drop --force
```
Восстановление бд:
```bash
php bin/console doctrine:database:create
```

### Миграции
#### Создать новую миграцию
```bash
php bin/console make:migration
```
#### Установить последнюю миграцию
```bash
php bin/console doctrine:migrations:migrate
```
#### Создать пустую миграцию
```bash
php bin/console doctrine:migrations:generate
```
#### Загрузить существующую в бд миграцию
```bash
php bin/console doctrine:migrations:execute --up DoctrineMigrations\\Version20221010123446_add_aliasCategory
```

### Установка фикстур
Фикстуры пока не используются, это на будущее
#### Стереть данные из бд и записать фикстуры
```bash
php bin/console doctrine:fixtures:load
```
#### Дописать фикстуры в бд без стирания
```bash
php bin/console doctrine:fixtures:load --append
```

## Отправка запросов
Отправлять запросы можно с помощью:
- используя схему api в Swagger
- Postman
- curl

> Если используем api в Swagger, не забываем про кнопку Authorize.
> В остальных случаях нужно явно передавать jwt ключ пользователя для отправки запросов, 
> в которых подразумеваются запросы от авторизированного пользователя

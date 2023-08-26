# EduGameTheory

Для сборки проекта используется **docker** версии 20.10.21
- [установка docker для Linux](https://docs.docker.com/engine/install/ubuntu/#set-up-the-repository) 
- [установка Docker desktop для Windows/Mac](https://www.docker.com/)

> Для Linux, чтобы не запускать команды группы докер из под sudo, 
> нужно прописать ```sudo usermod -a -G docker <username>``` и перезагрузиться

## Организационные вопросы

### Данные для входа
| Логин | Пароль | Роль |
|-------|--------|------|
| `admin` | `admin`  | админ |
| `login0` | `password0` | Ученик |
| `login1` | `password1` | Преподаватель |
| ... | ... | ... |
| `login9` | `password9` | Преподаватель |

### Общие моменты
- **url Swager'а:** `dudoser.msur.team/api/doc`
- SSE сервер (mercure)
> На удалённом сервере может не работать, 
> если не разрешить в браузере "Небезопасный контент".

## Build образов
### Backend
```shell
cd server
docker build -t the_dudoser/egt_backend .
```

### Frontend
```shell
cd client
docker build -t the_dudoser/egt_frontend .
```

## Начальные настройки backend

### Начальные настройки backend через make файл
```shell
docker compose up --force-recreate
docker compose exec backend bash
cd app
make set-backend
```
- **set-backend** - начальные настройки для backend.

<details><summary>Полезные команды</summary>

## Полезные команды

### Ручная первоначальная настройка backend
#### Генерация jwt ключа
```bash
php bin/console lexik:jwt:generate-keypair
```
#### Установка последней миграции (структуры бд)
```bash
php bin/console doctrine:migrations:migrate
```

### Работа с Docker контейнерами
#### Запуск dev-сервера для разработки
```bash
docker compose up -d --force-recreate
```
#### Запуск контейнера с backend
```bash
docker compose exec backend bash
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

#### Стереть данные из бд и записать фикстуры
```bash
php bin/console doctrine:fixtures:load
```
#### Дописать фикстуры в бд без стирания
```bash
php bin/console doctrine:fixtures:load --append
```
</details>

## Отправка запросов
Отправлять запросы можно с помощью:
- Swagger
- Postman
- curl

> Если используете Swagger, **не забывайте** про кнопку _Authorize_.
>
> В остальных случаях нужно явно передавать jwt ключ пользователя для отправки запросов, 
> в которых подразумеваются запросы от авторизированного пользователя

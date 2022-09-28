# Authentication service

- [Схема базы данных](https://drawsql.app/teams/chemodan-tours/diagrams/gpe-authentication-service)
- [Документация API](https://documenter.getpostman.com/view/11988073/VVBUzT4C)

---

_**Чтобы начать работу:**_

- Перейди в папку docker: 
```
    cd docker
```
- Для начала работы необходимо настроить .env файл: 
```
    make env
```
- Необходимо запустить проект.

---
Для запуска использовать:
```
    make up
```

Для остановки контейнеров использовать:
```
    make down
```

Для выполнения команды php:
```
    make shell
```
---
.env файл имеет следующие переменные:

1. APP_ENV - окружение проекта. Для разработки используется dev, для продакшна prod.
2. DATABASE_USER - имя пользователя для базы данных.
3. DATABASE_PASSWORD - пароль для подключения пользователя к БД.

---

Установка зависимостей:
```
    composer create-project symfony/skeleton:"6.1.*" app
    cd app
    composer require sensio/framework-extra-bundle
    composer require symfony/orm-pack
    composer require --dev symfony/maker-bundle
    composer require symfony/validator
    composer require symfony/form
    composer require symfony/serializer-pack
    composer require symfony/security-bundle
```

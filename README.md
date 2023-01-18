# Lime Meeting Api V1
## Table of Contents

- [Up And Running](#up-and-running)
- [Documentation](#documentation)

---
## Up And Running

Please make sure you have docker in your system.

```sh
$ cp .env.example .env
```

Please update the below values in .env file:
- CACHE_DRIVER=redis
- QUEUE_CONNECTION=redis

```sh
$ docker-compose up -d
```

```sh
$ docker exec meeting_app bash -c "php artisan key:generate"
```

```sh
$ docker exec meeting_app bash -c "php artisan storage:link"
```

```sh
$ docker exec meeting_app bash -c "php artisan migrate"
```
---
The below commands are optional
```sh
$ docker exec meeting_app bash -c "php artisan db:seed"
```
Api documentation will be generated in root of the project
- documentation/docsV1 folder will be added
- open index.html to see the API docs page

```sh
$ docker exec meeting_app bash -c "composer docs"
```

```sh
$ docker exec meeting_app bash -c "composer cs"
```

```sh
$ docker exec meeting_app bash -c "php artisan test"
```
---
Links:

- phpmyadmin is available: http://localhost:8080/
- Api: http://localhost:8000

### Documentation
Please open the link below the find out a bit more about structure.
[Notion](https://first-collard-80e.notion.site/Lime-Meeting-API-V1-849390a472e74c7089aa478dab598ede)


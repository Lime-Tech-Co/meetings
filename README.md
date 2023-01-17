# Lime Meeting Api V1
## Table of Contents

- [Up And Running](#up-and-running)
- [Documentation](#documentation)

## Up And Running

There two ways to run the project:

First method:

```sh
$ cp .env.example .env
```

```sh
$ docker-compose up -d
```

```sh
$ docker exec meetings-app-1 bash -c "composer install"
```

```sh
$ docker exec meetings-app-1 bash -c "php artisan key:generate"
```

```sh
$ docker exec meetings-app-1 bash -c "php artisan migrate --seed"
```

```sh
$ docker exec meetings-app-1 bash -c "composer docs"
```

```sh
$ docker exec meetings-app-1 bash -c "php artisan test"
```

alternatively you can simply run:

```sh
$ make it
```

### Documentation
Please open the link below the find out a bit more about structure.
[ClickUP](https://publicdoc.clickup.com/9004023643/d/h/8cawzuv-21/da894f6659cb97c)


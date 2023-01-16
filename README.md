# Lime Meeting Api V1
## Table of Contents

- [Up And Running](#up-and-running)
- [Example Readmes](#example-readmes)
- [Related Efforts](#related-efforts)
- [Related Efforts](#related-efforts)

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

Second method:

simply run 
```sh
$ make it
```

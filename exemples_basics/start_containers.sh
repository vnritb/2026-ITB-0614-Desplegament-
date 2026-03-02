#!/bin/bash
# Script per aixecar els contenidors manualment amb docker run
# Les comandes corresponen a les que es mostren al README.md

# 1. Aixecar MariaDB (Base de dades)
docker run -d \
  --name c_bd3 \
  --rm \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=db_dawe \
  -e MYSQL_USER=user_dawe \
  -e MYSQL_PASSWORD=pwd \
  mariadb:10.6

# 2. Aixecar PHPMyAdmin (Gestor web) connectat a la BD
docker run -d \
  --name c_phpmyadmin3 \
  --rm \
  -p 8082:80 \
  -e PMA_HOST=c_bd3 \
  -e MYSQL_ROOT_PASSWORD=root \
  phpmyadmin:latest

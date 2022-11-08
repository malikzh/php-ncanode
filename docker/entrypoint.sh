#!/bin/bash
set -e

# change user
if [ -n "$USER_ID" ]; then
  usermod -u "${USER_ID}" fpm-user
fi

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
  set -- php-fpm "$@"
fi

# TODO Убрать выполнение команд из под root
exec "$@"

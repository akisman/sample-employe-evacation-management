#!/bin/bash

if [ ! -f .env ]; then
  echo "Error: .env file not found. Please create a .env file before running this script."
  exit 1
fi

export MY_UID=$(id -u)
export MY_GID=$(id -g)

function build() {
  echo "Building and starting containers..."
  MY_UID=$MY_UID MY_GID=$MY_GID docker-compose up -d --build
}

function up() {
  echo "Starting containers..."
  MY_UID=$MY_UID MY_GID=$MY_GID docker-compose up -d
}

function down() {
  echo "Stopping containers..."
  MY_UID=$MY_UID MY_GID=$MY_GID docker-compose down
}

function install() {
  echo "Running composer install and frontend build inside container..."
  docker compose exec --user $MY_UID:$MY_GID app composer install
  docker compose exec --user $MY_UID:$MY_GID app bash -c "cd frontend && npm install && npm run build"
}

function migrate() {
  echo "Running migrations and seeds inside container..."
  docker compose exec --user $MY_UID:$MY_GID app php migrate.php
  docker compose exec --user $MY_UID:$MY_GID app php seed.php
}

function test() {
  echo "Running Pest with Coverage build inside container..."
  docker compose exec --user $MY_UID:$MY_GID app bash -c "./vendor/bin/pest --coverage-html=coverage"
}

case "$1" in
  build)
    build
    ;;
  up)
    up
    ;;
  down)
      down
      ;;
  install)
    install
    ;;
  migrate)
    migrate
    ;;
  test)
    test
    ;;
  *)
    echo "Usage: $0 {build|up|install|migrate|test}"
    exit 1
    ;;
esac

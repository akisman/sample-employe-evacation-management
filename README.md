# Employee Vacation Management System

A sample PHP backend and Vue.js frontend for managing employee vacation requests. The system supports submission, approval, and tracking of vacation days with role-based workflows, ensuring managers can easily review and approve requests. 

---

## Project Structure

```
├── _config Docker config files
├── _data Docker data volumes
├── app
│   ├── Controllers
│   ├── Core
│   ├── Exceptions
│   ├── Migrations
│   ├── Models
│   └── Traits
├── bootstrap
├── composer.json
├── composer.lock
├── frontend # Vue.js frontend app
├── manage.sh # Helper script for setup and running
├── migrate.php # CLI migration runner script
├── seed.php # CLI seeder runner script
├── phpunit.xml
├── public
├── routes
└── tests
````

---

## Requirements

- Docker & Docker Compose

All application components (PHP backend, database, frontend build environment) run inside Docker containers.
You only need Docker and Docker Compose installed on your host machine to build, run, and manage the application. No additional local environment setup is required.

---

## Setup and Run with Docker Compose

This project includes a `docker-compose.yml` to run the application and a helper shell script `manage.sh` to simplify setup, starting, and stopping. 

### Environment Configuration

Before running any commands, you need to create your `.env` configuration file. This file holds important environment variables needed for the app and Docker containers to run correctly.

Create your `.env` file by copying the example template:

```bash
cp .env.example .env
```

Then open the `.env` file and fill in the required fields.

### Helper Script for Common Tasks (`manage.sh`)

To make managing the Docker containers and app setup easier, a helper script `manage.sh` is included.

### Usage

Make sure `manage.sh` is executable:

```bash
chmod +x manage.sh
```

Run commands via the script like this:

```bash
./manage.sh build      # Build and start containers with proper UID/GID passed to Docker build args
./manage.sh up         # Start containers (without rebuild)
./manage.sh down       # Stop containers
./manage.sh install    # Run composer install + frontend npm install/build inside container with correct user permissions
./manage.sh migrate    # Run PHP migrations and seeders inside container
./manage.sh test       # Run Pest tests with HTML Coverage reporting inside the container
```

---

### Installation

```bash
./manage.sh build
./manage.sh install
./manage.sh migrate
```

This sequence will build and start your app, install PHP and frontend dependencies, run migrations and seeders, and keep the app running.

The application will be served on `http://app.localhost`

---

### Running Tests

Run PHP unit tests with coverage inside the container:

```bash
./manage.sh test
```

A `coverage` folder will be created with the generated HTML report.

---

## Notes

* Employee codes must be unique 7-digit integers.
* Manager role is required to create or update users.

### Sample Users  (as defined in `seed.php`)

Manager
```
Email: alice.manager@example.com
Password: password
```

Employee

```
Email: bob.employee@example.com
Password: password
```



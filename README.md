
# Installation
## Using Docker
To get started, the following steps needs to be taken:
+ Clone the repo.
+ `cd icp-be` to the project directory.
+ `cp .env.example .env` to use env config file
+ Run `docker run --rm --interactive --tty -v $(pwd):/app composer install` to install composer using docker
+ Run `docker-compose up -d` to start the containers.
+ Application will run on http://localhost:8000.
+ Run `docker exec -it icp-be-ticketing-1 /bin/sh` to access application file in docker
+ Run `php artisan migrate` to migrate all database (may need using `--seed` to seed first data
+ Now application ready to use

## Not Using Docker
+ Clone the repo.
+ `cd icp-be` to the project directory.
+ `cp .env.example .env` to use env config file
+ Edit file `.env` to match your database (For now, it is recommended to use MySQL for the database. [see issue.](https://github.com/mnafiudrr/icp-be/issues/2))
+ Run `composer install` to install composer.
+ Run `php artisan migrate` to migrate all database (may need using `--seed` to seed first data)
+ Run `php artisan serve` to run the application on your device.
+ Application will run on your localhost.

# API Collection
- See all collection on [postman](https://www.postman.com/restless-zodiac-64588/workspace/ticketing/collection/14455202-d2c30265-171f-448b-9351-1dbff2792c8b?action=share&creator=14455202)
- For example/online testing, you can access http://103.163.161.18:8765/ 

# Features
## Authentication
User need register to access the application, (https://github.com/mnafiudrr/icp-be/pull/3#issue-2229973198)
## Manage Ticket and Project
See https://github.com/mnafiudrr/icp-be/pull/4#issue-2230008259 and https://github.com/mnafiudrr/icp-be/pull/5#issue-2230154019
## Ticket Priority
- API for using drag n drop to change Label from `To Do` to `Doing`, or the other way around
- Drag n drop to sort up and down
- For details, see https://github.com/mnafiudrr/icp-be/pull/6#issue-2230512171

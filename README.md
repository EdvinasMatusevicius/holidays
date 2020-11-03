# Holiday info api

Api that returns list of holidays for selected country, region and year. Holidays are grouped by a month. List also includes number of holidays for that year, maximum amount of free days in a row and current day status. <br>

Currently deployed and can be reached at [link to back-end](http://holiday-api.serverpi.ddns.me)<br>
Front-end vue.js app to help interact with api deployed at [link to front-end](http://holiday-front.serverpi.ddns.me)

## Required
- PHP >= **7.3**
- MySQL


## Install

- Run `composer install` command.
- Create `Mysql` database,
- Run `copy .env.example .env` command and  `update database` credentials with your database info,
- Run `php artisan key:generate` command,
- Run `php artisan migrate` command,
- Run `php artisan db:seed` command,
- If you don't have virtualization run `php artisan serve` command
- Go to your domain on web browser

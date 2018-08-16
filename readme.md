# Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

# laraCRM
** used mailgun for mailing

Created by following http://laraadmin.com/docs/1.0/installation

- composer create-project laravel/laravel=5.2.31 CRM
- cd CRM
- sudo chmod -R 777 storage/ bootstrap/ database/migrations/
- composer require "dwij/laraadmin:1.0.40"
- Add LaraAdmin Service provider Dwij\Laraadmin\LAProvider::class in config/app.php :
    'providers' => [
            ...
            Dwij\Laraadmin\LAProvider::class
    ],
- php artisan la:install

- https://confluence.atlassian.com/bitbucket/set-up-an-ssh-key-728138079.html#SetupanSSHkey-ssh1 refer link for generating bitbucket account key

- add 
            $table_name = ($json != 'la_menus') ? strtolower(str_plural($json)) : 'la_menus';
            and change strtolower(str_plural($json)) with $table_name
 in process_values function of C:\xampp\htdocs\Ganit\CRM\vendor\dwij\laraadmin\src\LAFormMaker.php 
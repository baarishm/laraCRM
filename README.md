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
 
 -->> For project file access on server add rule in web.config
 <directoryBrowse enabled="false" />
        <security>
            <authorization>
                <remove users="*" roles="" verbs="" />
                <add accessType="Allow" users="?" />
            </authorization>
        </security>
        
-->> For Cron jobs : 
- https://www.sitepoint.com/managing-cronjobs-with-laravel/
- https://quantizd.com/how-to-use-laravel-task-scheduler-on-windows-10/

# Steel Project - Backend

PHP driven backend for Steel Project; a project management system

This is a personal project, made just for learning and personal development. The code has been provided on **AS IS** basis.
Feel free to share your insights.

# How can you use it?

Easy steps:

- Clone this repo or download a tarball
- Run `composer install`
- Open `var/keys`
- Create a private key using `openssl genrsa -aes256 -out private.pem 2048`
- Use that key to generate a public copy for it using `openssl rsa -pubout -in private.pem -out public.pem`
- Deploy on any server or `composer serve`
- There you go, ready to create something beautiful!
 

# Try, Then Try Harder

> Repeat after me; I CAN.


## Tech

This project uses a number of open source projects to work properly:

* [Monolog] - Yes, please!
* [Selective] - Just for an easy life.
* [JWT] - For API auth.
* [Slim PHP] - Any skeleton needs bones, right?
* [Faker PHP] - Used with seeders,to fake data.
* [Eloquent] - Laravel's ORM.




### Development

Want to contribute? Great!
Make a change in your files and submit a pull request!

   [Eloquent]: <https://laravel.com/docs/9.x/eloquent/>
   [Monolog]: <https://github.com/Seldaek/monolog>
   [Selective]: <https://github.com/selective-php>
   [JWT]: <https://jwt.io/>
   [Faker PHP]: <https://fakerphp.github.io/>
   [Slim PHP]: <https://www.slimframework.com/>



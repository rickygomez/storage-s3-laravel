# storage s3 in Laravel

Author: Ricardo Gomez
E-mail: ricardo@assys.com.br

This repository was created with the aim of being a study on storage S3 in laravel

- PHP 8.1
- Laravel 10
- S3 AWS

---

## Resource used for the development

- **GIT**: for versioning by Github
- **Layer Service**: to manage Business Rules and manipulate Models
- **[Intervention/image](https://github.com/Intervention/image)**: to resize images

---

## References

Laravel 9 e PHP 8.1 - Armazenando arquivos no AWS S3 Storage [https://www.youtube.com/watch?v=yP0MMHmzdX0]
AWS S3 - TUDO sobre o Storage da AWS | Curso AWS - Aula 16 - #68 [https://www.youtube.com/watch?v=ayu9xlQXCYs]

---

## Install new environment

1- install composer dependency

~~~shell
composer install
~~~

2- Create and configure the file _/.env_ using example file

~~~shell
cp .env.example .env
~~~

3- Generate application key

~~~shell
php artisan key:generate
~~~

4- Generate symbolic link to the storage folder inside the public folder

~~~shell
php artisan storage:link
~~~

---

## Clear config caches

~~~shell
php artisan optimize:clear
~~~


## RUN

### Terminal using command

~~~shell
php artisan dev:playground {url}
~~~

### API Endpoint

the endpoint accepted the body paramenters 
- "url" a string with the URL of the image 
  or
- "file" an imagem file

~~~
URI:  '/api/make-image'
method: 'POST',
body: {
    url
    file
}
~~~

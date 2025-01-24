# I have image configured for the PHP 8.3
# https://github.com/palamar/mx-php-fpm/tree/8.3
# it's uploaded to the Docker Hub
# https://hub.docker.com/layers/palamar/mx-php-fpm/8.3/images/sha256-26342a20fd1342ce4ed28255510eff48eafc9432cd1ee86070696d16f5a859fd
# so I just use it
FROM palamar/mx-php-fpm:8.3

COPY code/ /var/www/html/

EXPOSE 8000

ENTRYPOINT php artisan serve --host 0.0.0.0 --port 8080

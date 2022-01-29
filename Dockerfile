FROM phpswoole/swoole:4.8-php7.4-alpine

COPY . /usr/src/myapp
WORKDIR /usr/src/myapp

RUN composer update --ignore-platform-reqs --optimize-autoloader --no-plugins --no-scripts --prefer-dist

CMD [ "php", "./src/index.php" ]
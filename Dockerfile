FROM phpswoole/swoole:4.8-php7.4-alpine

COPY . /usr/src/myapp
WORKDIR /usr/src/myapp
CMD [ "php", "./src/index.php" ]
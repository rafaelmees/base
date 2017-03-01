FROM jaschweder/php:tools

MAINTAINER jonathan.schweder@bludata.com.br

# install mongodb extension
RUN git clone -b v1.2 --depth 1 https://github.com/mongodb/mongo-php-driver.git /usr/src/mongodb-ext \
    && cd /usr/src/mongodb-ext \
    && git submodule sync \
    && git submodule update --init \
    && phpize \
    && ./configure \
    && make all -j3 \
    && make install \
    && echo "extension=mongodb.so" >> /usr/local/lib/php.ini \
    && rm -rf /usr/src/mongodb-ext

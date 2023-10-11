PRIVATE MESSAGE

Demo : <a href="https://privatemessage.ovh">Private Message</a>

<p align="center">
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/lgrdev/privatemessage/releases"><img src="https://img.shields.io/github/release/lgrdev/privatemessage.svg?style=flat-square" alt="Latest Version"></img></a>
</p>

Private Message is created to reduce the quantity of plain text passwords that transit through email and remain accessible in an email inbox or mail archives. By generating a short-lived link that can only be accessed once, this message ceases to exist once viewed or expires.

Encrypted messages have a fixed lifespan (currently 1 hour, 1 day, 4 days or 7 days) and are automatically deleted upon expiration.

Encrypted messages can only be viewed once, reducing the risk of disclosure.

## requirements

* Apache or Nginx
* Php 8.1 or earlier
* Redis , Mysql , MariaDB , Postgresql or Memcached
* Composer

## Setup

```bash
$ git clone https://github.com/lgrdev/privatemessage.git <YOUR_DIRECTORY>
$ cd <YOUR_DIRECTORY>
$ mkdir cache
$ composer install
```

## Setting

1. Create your .env with a copy of env.sample and change parameters

```bash
$ cd <YOUR_DIRECTORY>
$ cp .env.sample .env
$ nano .env
```

2. If you use an other database than Redis, update by comment and uncomment the file config/config.php



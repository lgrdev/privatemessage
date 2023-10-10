Private Message
===============


<p align="center">
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/lgrdev/privatemessage/releases"><img src="https://img.shields.io/github/release/lgrdev/privatemessage.svg?style=flat-square" alt="Latest Version"></img></a>
</p>

## requirments

** Apache or Nginx
** Php 8.1 or earlier
** Redis , Mysql , MariaDB or Postgresql
** Composer


## Installation

```bash
$ git clone https://github.com/lgrdev/privatemessage.git <YOUR_DIRECTORY>
$ cd <YOUR_DIRECTORY>
$ mkdir cache
$ composer install
```

## Setup

1. Create your config.prod.php with a copy of config.env.php

```bash
$ cd config
$ cp config.env.php config.prod.php
```

2. Edit config/config.prod.php


3. If you use an other database than Redis, update by comment and uncomment the file config/config.php



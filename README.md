
## xiaoshouyi crm

	
## 配置文件格式参考xsy-crm.conf
- xsy-crm.conf.dev ->xsy-crm.conf


## 技术栈
- php + guzzle + memcached + monolog + phpunit + redis


## 说明
- memcached/redis主要用来缓存token
- cache_driver 支持memcached和redis

	
## phpunit test cover 100%
 	- run test
	- make test
	
## composer
	- composer require yd/php-xsy-crm v1.0.0 	

## todo
- 完善异常处理

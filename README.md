# yaftest
用于测试yaf框架的安装与部署；测试多模块，测试项目位于根目录及二级目录nginx配置的写法。

    安装yaf
    在GitHub找yaf项目下载最新版 yaf项目("yaf主分支源码")
    在PECL官网找了最新版[yaf-3.0.7.tgz](http://pecl.php.net/package/yaf)，如果不会，请参看官网的安装[yaf官网](http://www.laruence.com/manual/ "yaf官网")
```shell
##phpize 与 php-config需要根据自己的机器来确定路径
##make完后可以执行 make test 用于测试是否存在bug导致不能改正常使用，但基本上很少会执行 make test 这里我也省略了 make test
##加上 sudo 避免因为权限不够导致安装失败
cd yaf-master
/usr/bin/phpize
./configure --with-php-config=/usr/bin/php-config
make
sudo make install
```
    在php.ini里面增加yaf模块的配置
```ini
extension=yaf.so

;yaf config
yaf.environ = product
;yaf.library =
yaf.cache_config = 0
yaf.name_suffix = 1
;yaf.name_separator =
yaf.forward_limit = 5
yaf.use_namespace = 1
yaf.use_spl_autoload = 0
```

    nginx的安装目录及安装信息
```shell
localhost:1.14.0 blueprinted$ pwd
/usr/local/Cellar/nginx-full/1.14.0
localhost:1.14.0 blueprinted$ ll 
total 600
-rw-r--r--  1 blueprinted  admin  286953  4 17 23:22 CHANGES
-rw-r--r--  1 blueprinted  admin    2851  9 11 02:28 INSTALL_RECEIPT.json
-rw-r--r--  1 blueprinted  admin    1397  4 17 23:22 LICENSE
-rw-r--r--  1 blueprinted  admin      49  4 17 23:22 README
drwxr-xr-x  3 blueprinted  admin      96  9 11 02:28 bin
-rw-r--r--  1 blueprinted  admin     581  9 11 02:28 homebrew.mxcl.nginx-full.plist
lrwxr-xr-x  1 blueprinted  admin      33  9 13 23:48 html -> /Users/blueprinted/Public/webtest/public
lrwxr-xr-x  1 blueprinted  admin      14  9 13 23:45 logs -> /var/log/nginx
lrwxr-xr-x  1 blueprinted  admin      14  9 13 23:46 run -> /var/run/nginx
drwxr-xr-x  3 blueprinted  admin      96  9 11 02:28 share
localhost:1.14.0 blueprinted$ 
```
```shell
localhost:webtest blueprinted$ nginx -V
nginx version: nginx/1.14.0
built by clang 9.1.0 (clang-902.0.39.2)
built with OpenSSL 1.0.2p  14 Aug 2018
TLS SNI support enabled
configure arguments: --prefix=/usr/local/Cellar/nginx-full/1.14.0 --with-http_ssl_module --with-pcre --with-ipv6 --sbin-path=/usr/local/Cellar/nginx-full/1.14.0/bin/nginx --with-cc-opt='-I/usr/local/include -I/usr/local/opt/pcre/include -I/usr/local/opt/openssl/include' --with-ld-opt='-L/usr/local/lib -L/usr/local/opt/pcre/lib -L/usr/local/opt/openssl/lib' --conf-path=/usr/local/etc/nginx/nginx.conf --pid-path=/usr/local/var/run/nginx.pid --lock-path=/usr/local/var/run/nginx.lock --http-client-body-temp-path=/usr/local/var/run/nginx/client_body_temp --http-proxy-temp-path=/usr/local/var/run/nginx/proxy_temp --http-fastcgi-temp-path=/usr/local/var/run/nginx/fastcgi_temp --http-uwsgi-temp-path=/usr/local/var/run/nginx/uwsgi_temp --http-scgi-temp-path=/usr/local/var/run/nginx/scgi_temp --http-log-path=/usr/local/var/log/nginx/access.log --error-log-path=/usr/local/var/log/nginx/error.log --add-module=/usr/local/share/echo-nginx-module
localhost:webtest blueprinted$ 
```
项目文件所在目录
```
localhost:webtest blueprinted$ 
localhost:webtest blueprinted$ pwd
/Users/blueprinted/Public/webtest
localhost:webtest blueprinted$ tree
.
├── application
│   ├── Bootstrap.php
│   ├── controllers
│   │   ├── Error.php
│   │   └── Index.php
│   ├── models
│   │   └── Sample.php
│   ├── modules
│   │   └── guahao
│   │       ├── controllers
│   │       │   ├── Error.php
│   │       │   └── Index.php
│   │       └── views
│   │           ├── error
│   │           │   └── error.phtml
│   │           └── index
│   │               └── index.phtml
│   ├── plugins
│   │   └── Sample.php
│   └── views
│       ├── error
│       │   └── error.phtml
│       └── index
│           └── index.phtml
├── conf
│   └── application.ini
└── public
    └── index.php

15 directories, 13 files
localhost:webtest blueprinted$  
```
    nginx主配置文件(/usr/local/etc/nginx/nginx.conf)内容
```shell
user  nobody;
worker_processes  4;

error_log  logs/error.log;
error_log  logs/error.log  notice;
error_log  logs/error.log  info;

pid        run/nginx.pid;


events {
    worker_connections  1024;
}


http {
    include       mime.types;
    default_type  application/octet-stream;

    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';
    log_format srv.android '$remote_addr - $remote_user [$time_local] $request '
                 '"$status" $body_bytes_sent $request_time "$http_referer" '
                  '"$http_user_agent" "$http_x_forwarded_for"';
    log_format srv-android '$remote_addr - $remote_user [$time_local] $request '
                 '"$status" $body_bytes_sent $request_time "$http_referer" '
                  '"$http_user_agent" "$http_x_forwarded_for"';

    #access_log  logs/access.log  main;

    sendfile        on;
    #tcp_nopush     on;

    #keepalive_timeout  0;
    keepalive_timeout  65;

    #gzip  on;

    #server {
        #listen       8080;
        #server_name  localhost;

        #charset koi8-r;

        #access_log  logs/host.access.log  main;

        #location / {
        #    root   html;
        #    index  index.html index.htm;
        #}

        #error_page  404              /404.html;

        # redirect server error pages to the static page /50x.html
        #
        #error_page   500 502 503 504  /50x.html;
        #location = /50x.html {
        #    root   html;
        #}

        # proxy the PHP scripts to Apache listening on 127.0.0.1:80
        #
        #location ~ \.php$ {
        #    proxy_pass   http://127.0.0.1;
        #}

        # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
        #
        #location ~ \.php$ {
        #    root           html;
        #    fastcgi_pass   127.0.0.1:9000;
        #    fastcgi_index  index.php;
        #    fastcgi_param  SCRIPT_FILENAME  /scripts$fastcgi_script_name;
        #    include        fastcgi_params;
        #}

        # deny access to .htaccess files, if Apache's document root
        # concurs with nginx's one
        #
        #location ~ /\.ht {
        #    deny  all;
        #}
    #}


    # another virtual host using mix of IP-, name-, and port-based configuration
    #
    #server {
    #    listen       8000;
    #    listen       somename:8080;
    #    server_name  somename  alias  another.alias;

    #    location / {
    #        root   html;
    #        index  index.html index.htm;
    #    }
    #}


    # HTTPS server
    #
    #server {
    #    listen       443 ssl;
    #    server_name  localhost;

    #    ssl_certificate      cert.pem;
    #    ssl_certificate_key  cert.key;

    #    ssl_session_cache    shared:SSL:1m;
    #    ssl_session_timeout  5m;

    #    ssl_ciphers  HIGH:!aNULL:!MD5;
    #    ssl_prefer_server_ciphers  on;

    #    location / {
    #        root   html;
    #        index  index.html index.htm;
    #    }
    #}
    include servers/*.conf;
}

```

nginx虚拟主机配置文件（/usr/local/etc/nginx/servers/default.conf）内容
```shell
server {
        listen       80;
        server_name  127.0.0.1 127.1 localhost;

        charset utf-8;
        default_type text/plain;
        
        error_log   logs/error.log error;
        access_log  logs/default.access.log  main;

        location / {
            root   html;
            index  index.html index.htm index.php;
            include mime.types;
            autoindex on;
            autoindex_exact_size off;
            #if (!-e $request_filename) {
            #    rewrite ^/(.*)  /index.php last;
            #}
            try_files $uri $uri/ /index.php?$query_string;
        }

        error_page  404              /404.html;
        location = /404.html {
            root html;
        }
        # redirect server error pages to the static page /50x.html
        #
        error_page   500 502 503 504  /50x.html;
        location = /50x.html {
            root   html;
        }

        # proxy the PHP scripts to Apache listening on 127.0.0.1:80
        #
        #location ~ \.php$ {
        #    proxy_pass   http://127.0.0.1;
        #}

        # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
        #
        location ~ \.php$ {
            root           html;
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            #fastcgi_param  SCRIPT_FILENAME  /scripts$fastcgi_script_name;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }

        # deny access to .htaccess files, if Apache's document root
        # concurs with nginx's one
        #
        #location ~ /\.ht {
        #    deny  all;
        #}
}

```

测试结果

浏览器访问：http://localhost/
页面输出
```html
$get=default value
Hello World! I am Stranger
```

浏览器访问：http://localhost/index/index/index/name/phper?get=iamget
页面输出
```html
$get=iamget
Hello World! I am phper
```


浏览器访问：http://localhost/guahao/index/index
页面输出
```html
$get=default value
Hello World! I am Stranger
```


浏览器访问：http://localhost/guahao/index/index/name/phper?get=iamget
页面输出
```html
$get=iamget
Hello World! I am phper
```

将虚拟主机配置文件 /usr/local/etc/nginx/servers/default.conf 的 
```shell
        location / {
            root   html;
            index  index.html index.htm index.php;
            include mime.types;
            autoindex on;
            autoindex_exact_size off;
            #if (!-e $request_filename) {
            #    rewrite ^/(.*)  /index.php last;
            #}
            try_files $uri $uri/ /index.php?$query_string;
        }
```
改为
```shell
        location / {
            root   html;
            index  index.html index.htm index.php;
            include mime.types;
            autoindex on;
            autoindex_exact_size off;
            if (!-e $request_filename) {
                rewrite ^/(.*)  /index.php last;
            }
            #try_files $uri $uri/ /index.php?$query_string;
        }
```
做跟之前一样的URL访问，输出也是正常且一样的。
    
    以上实现了nginx下yaf框架部署在根目录的多模块功能。
    
    如果将yaf框架部署在二级目录，因为根目录的业务可能是用其他框架来写的或者根本没有用框架，nginx应该怎样配置才能实现？
    
    比如将nginx的根目录改成这样
```shell
localhost:1.14.0 blueprinted$ pwd
/usr/local/Cellar/nginx-full/1.14.0
localhost:1.14.0 blueprinted$ ll 
total 600
-rw-r--r--  1 blueprinted  admin  286953  4 17 23:22 CHANGES
-rw-r--r--  1 blueprinted  admin    2851  9 11 02:28 INSTALL_RECEIPT.json
-rw-r--r--  1 blueprinted  admin    1397  4 17 23:22 LICENSE
-rw-r--r--  1 blueprinted  admin      49  4 17 23:22 README
drwxr-xr-x  3 blueprinted  admin      96  9 11 02:28 bin
-rw-r--r--  1 blueprinted  admin     581  9 11 02:28 homebrew.mxcl.nginx-full.plist
lrwxr-xr-x  1 blueprinted  admin      33  9 14 01:40 html -> /Users/blueprinted/Public/webtest
lrwxr-xr-x  1 blueprinted  admin      14  9 13 23:45 logs -> /var/log/nginx
lrwxr-xr-x  1 blueprinted  admin      14  9 13 23:46 run -> /var/run/nginx
drwxr-xr-x  3 blueprinted  admin      96  9 11 02:28 share
localhost:1.14.0 blueprinted$ 
```
    yaf项目的目录结构保持不变，也不改动nginx的配置文件。
/usr/local/etc/nginx/servers/default.conf 的 location / {} 段内容
```shell
location / {
            root   html;
            index  index.html index.htm index.php;
            include mime.types;
            autoindex on;
            autoindex_exact_size off;
            if (!-e $request_filename) {
                rewrite ^/(.*)  /index.php last;
            }
            #try_files $uri $uri/ /index.php?$query_string;
        }
```

浏览器访问：http://localhost/
页面输出
```html
Index of /
../
application/                                       13-Sep-2018 16:55       -
conf/                                              12-Sep-2018 06:01       -
public/                                            13-Sep-2018 17:39       -
```

浏览器访问：http://localhost/public/
页面输出
```html
$get=default value
Hello World! I am Stranger
```

浏览器访问：http://localhost/public/?get=iamget
页面输出
```html
$get=iamget
Hello World! I am Stranger
```

浏览器访问：http://localhost/public/index
页面输出(状态码是404)
```html
File not found.
```
查看nginx的报错信息：
```shell
2018/09/14 02:08:24 [error] 33574#0: *1 FastCGI sent in stderr: "Primary script unknown" while reading response header from upstream, client: 127.0.0.1, server: 127.0.0.1, request: "GET /public/index HTTP/1.1", upstream: "fastcgi://127.0.0.1:9000", host: "localhost"
```
从nginx的报错信息来看，http://localhost/public/index 这个请求最终命中到了 location ~ \.php$ {...} 这一段，并且nginx已经将这个请求反向代理（或称转发）到127.0.0.1:9000端口了，也就是php-fpm监听的端口，但是php-fpm没有找到对应的php文件，报出了404。

分析一下 location 的流程走向：
URI: /public/index
如果有正则location且能命中到则会进入正则的location{}段，很显然没有命中到 location ~ \.php$ {...} 这一段；
首先会进入 location / {...} 段，命中到 if (!-e $request_filename) {rewrite ^/(.*)  /index.php last;} 这一段，nginx内会做请求重定向，重定向到的文件是 /index.php 然后重新进入 server{} 段，但是浏览器地址栏的地址不会变。
重新进入 server{}段之后，正则优先原则命中到了 location ~ \.php$ {...} 这一段，于是nginx将请求转发给 127.0.0.1:9000，这正好是php-fpm监听的端口，php-fpm去找这个文件，发现找不到，于是报出了404

将/usr/local/etc/nginx/servers/default.conf location / {} 段的配置改成这样
```shell
        location / {
            root   html;
            index  index.html index.htm index.php;
            include mime.types;
            autoindex on;
            autoindex_exact_size off;
            #if (!-e $request_filename) {
            #    rewrite ^/(.*)  /index.php last;
            #}
            try_files $uri $uri/ /index.php?$query_string;
        }
```
做跟之前一样的访问

浏览器访问：http://localhost/
页面输出
```html
Index of /
../
application/                                       13-Sep-2018 16:55       -
conf/                                              12-Sep-2018 06:01       -
public/                                            13-Sep-2018 17:39       -
```

浏览器访问：http://localhost/public/
页面输出
```html
$get=default value
Hello World! I am Stranger
```

浏览器访问：http://localhost/public/?get=iamget
页面输出
```html
$get=iamget
Hello World! I am Stranger
```

浏览器访问：http://localhost/public/index
页面输出(状态码是404)
```html
File not found.
```
查看nginx的报错信息：
```shell
127.0.0.1 - - [15/Sep/2018:03:08:08 +0800] "GET /public/index HTTP/1.1" 404 27 "-" "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36" "-"
```
注意，虽然都是报的404，但这个404的nginx报错信息，与上一个404的nginx报错信息是不一样的。上一个是请求rewrite后匹配到了 location ~ \.php$ {...} 这一段，从而是 php-fpm 报出来的404，而这个404报错，是nginx报出来的，并没有命中到 location ~ \.php$ {...} 段。
从这可以看出 rewrite 与 try_files 的区别：
```shell
rewrite ^/(.*)  /index.php last;                      不会先检查 /index.php 是否存在，直接重定向
try_files $uri $uri/ /index.php?$query_string;        fallback到 /index.php 时，会检查一遍 index.php 是否存在，不存在则直接由nginx报404，存在则会去重定向。
```

可以验证一下这个结论：
在 /Users/blueprinted/Public/webtest 目录下增加一个 index.php 文件：
```php
<?php
echo "this is /Users/blueprinted/Public/webtest/index.php";

```
显然这个文件与 public 目录平级
然后，浏览器访问：http://localhost/public/index
页面输出(状态码是200)
```html
this is /Users/blueprinted/Public/webtest/index.php
```
因此，结论无误。

从以上来看，想要把yaf框架入口文件（index.php）部署到二级目录，增加一段 location /public {...} 或许可以解决问题，下面开始尝试

更改配置文件 /usr/local/etc/nginx/servers/default.conf 改动 location / {} 增加一段 location /public {...}
```shell
      location / {
            root   html;
            index  index.html index.htm index.php;
            include mime.types;
            autoindex on;
            autoindex_exact_size off;
            #if (!-e $request_filename) {
            #    rewrite ^/(.*)  /index.php last;
            #}
            #try_files $uri $uri/ /index.php?$query_string;
        }

        location /public {
            root   html;
            index  index.html index.htm index.php;
            include mime.types;
            try_files $uri $uri/ /public/index.php?$query_string;
        }
```
重启nginx 重新访问

浏览器访问：http://localhost/public/index/index/index/name/phper?get=iamget
页面输出
```html
$get=iamget
Hello World! I am phper
```

浏览器访问：http://localhost/public/guahao/index/index/name/phper?get=iamget
页面输出
```html
$get=iamget
Hello World! I am phper
```

再更改配置文件 /usr/local/etc/nginx/servers/default.conf 不动 location / {} 更改 location /public {...}
```shell
        location / {
            root   html;
            index  index.html index.htm index.php;
            include mime.types;
            autoindex on;
            autoindex_exact_size off;
            #if (!-e $request_filename) {
            #    rewrite ^/(.*)  /index.php last;
            #}
            #try_files $uri $uri/ /index.php?$query_string;
        }

        location /public {
            root   html;
            index  index.html index.htm index.php;
            include mime.types;
            if (!-e $request_filename) {
                rewrite ^/(.*)  /public/index.php last;
            }
        }
```
重启nginx 重新访问(正常，与之前一样)

浏览器访问：http://localhost/public/index/index/index/name/phper?get=iamget
页面输出
```html
$get=iamget
Hello World! I am phper
```

浏览器访问：http://localhost/public/guahao/index/index/name/phper?get=iamget
页面输出
```html
$get=iamget
Hello World! I am phper
```
至此，yaf框架放在二级目录也可以实现，目录结构为：
```
localhost:webtest blueprinted$ pwd
/Users/blueprinted/Public/webtest     ##这个是nginx的根目录
localhost:webtest blueprinted$ tree -L 2
.
├── application
│   ├── Bootstrap.php
│   ├── controllers
│   ├── models
│   ├── modules
│   ├── plugins
│   └── views
├── conf
│   └── application.ini
└── public
    └── index.php

8 directories, 3 files
localhost:webtest blueprinted$ 
```
但实际上这种目录结构还存在问题，多数框架文件或目录位于了web根目录，占用了根目录的资源。下面将目录结构进行调整：
```
localhost:webtest blueprinted$ tree -L 2
.
└── public
    ├── application
    ├── conf
    └── index.php

3 directories, 1 file
localhost:webtest blueprinted$  
```

调整一下index.php的内容，由
```php
<?php
  
define('APP_PATH', dirname(__DIR__));

$application = new Yaf\Application(APP_PATH . "/conf/application.ini");

$application->bootstrap()->run();
```
更改为
```php
<?php
  
define('APP_PATH', __DIR__);

$application = new Yaf\Application(APP_PATH . "/conf/application.ini");

$application->bootstrap()->run();
```

不需要更改 nginx 配置，所以不需要重启nginx，然后进行访问测试

浏览器访问：http://localhost/public/index/index/index/name/phper?get=iamget
页面输出
```html
$get=iamget
Hello World! I am phper
```

浏览器访问：http://localhost/public/guahao/index/index/name/phper?get=iamget
页面输出
```html
$get=iamget
Hello World! I am phper
```
再调整一下 /usr/local/etc/nginx/servers/default.conf ，由
```shell
        location /public {
            root   html;
            index  index.html index.htm index.php;
            include mime.types;
            if (!-e $request_filename) {
                rewrite ^/(.*)  /public/index.php last;
            }
        }
```
更改为
```shell
        location /public {
            root   html;
            index  index.html index.htm index.php;
            include mime.types;
            try_files $uri $uri/ /public/index.php?$query_string;
        }
```

重启nginx，重新访问

浏览器访问：http://localhost/public/index/index/index/name/phper?get=iamget
页面输出
```html
$get=iamget
Hello World! I am phper
```

浏览器访问：http://localhost/public/guahao/index/index/name/phper?get=iamget
页面输出
```html
$get=iamget
Hello World! I am phper
```

一切正常，至此将yaf框架部署到二级目录也可以实现。

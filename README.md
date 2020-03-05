# FuZZil Simple fuzz
[![Version](https://img.shields.io/badge/Fuzzil-0.1-brightgreen.svg?maxAge=259200)]()
[![PHP 7](https://img.shields.io/badge/PHP-7-yellow.svg)](https://www.php.net/)
[![Build](https://img.shields.io/badge/Supported_OS-Linux-orange.svg)]()
![GitHub](https://img.shields.io/github/license/MrCl0wnLab/Fuzzil?color=blue)

### DESCRIPTION
```
The Fuzzil is a program which injects automatically semi-random
data into a program and detect potential bugs.
```

```
 + Autor: MrCl0wn
 + Blog: http://blog.mrcl0wn.com
 + GitHub: https://github.com/MrCl0wnLab
 + Twitter: https://twitter.com/MrCl0wnLab
 + Email: mrcl0wnlab\@\gmail.com
```
## WARNING
```
+------------------------------------------------------------------------------+
|  [!] Legal disclaimer: Usage of afdWordpress for attacking                   |
|  targets without prior mutual consent is illegal.                            |
|  It is the end user's responsibility to obey all applicable                  | 
|  local, state and federal laws.                                              |
|  Developers assume no liability and are not responsible for any misuse or    |
|  damage caused by this program                                               |
+------------------------------------------------------------------------------+
```
### HELP
```
    ███████╗██╗   ██╗███████╗███████╗██╗██╗     
    ██╔════╝██║   ██║╚══███╔╝╚══███╔╝██║██║     
    █████╗  ██║   ██║  ███╔╝   ███╔╝ ██║██║     
    ██╔══╝  ██║   ██║ ███╔╝   ███╔╝  ██║██║     
    ██║     ╚██████╔╝███████╗███████╗██║███████╗
    ╚═╝      ╚═════╝ ╚══════╝╚══════╝╚═╝╚══════╝
[!] [HELP]

     Defina o comando 
     TARGET                  -t target.com.br/FUZZ  
     ARQUIVO SOURCE FUZZ     -f strings.txt 
     THREADS                 --thr 15
     RANGE                   --range 1-200
     GREP                    --grep 
     HELP                    --help / -h

[!] [OUTPUT]
     CODE                      200.log,301.log,
                               302.log,401.log,
                               402.log,403.log,
                               grep.log
```

### USE
```
php fuzzil.php  -t http://www3.ILUSTRATIVO.gov.br/FUZZ -f 0day.txt --grep 'Admin - Autentica'
php fuzzil.php  -t http://FUZZ.ILUSTRATIVO.gov.br/Sistema/Login.aspx -f sub.txt --grep 'Admin - Autentica'
php fuzzil.php  -t 'http://FUZZ.ILUSTRATIVO.gov.br/Sistema/Login.aspx?login=1%27' -f sub.txt --grep 'SQL syntax;'
php fuzzil.php  -t 'http://FUZZ.ILUSTRATIVO.gov.br/admin.FUZZ' -f ext.txt'
php fuzzil.php  -t http://wwwFUZZ.ILUSTRATIVO.gov.br  --range 1-100
php fuzzil.php  -f hots_check_status_code.txt
```
### TUNNING ( GAMBIARRA )
```
MODIFICAR O ARQUIVO  PHP-FPM:
       apt-get install php7.2-fpm
       sudo vim /etc/php-fpm.conf

ADD OS VALORES:
    pm = dynamic
    pm.max_children = 40
    pm.start_servers = 15
    pm.min_spare_servers = 15
    pm.max_spare_servers = 25
    pm.max_requests = 1000

        
RESTART SERVICE:
    sudo service php-fdm restart 

REF:
    https://php.net/manual/en/install.fpm.configuration.php
    https://serversforhackers.com/c/php-fpm-process-manageme
```

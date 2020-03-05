<?php
$error_display = 1;
error_reporting($error_display);
ini_set('display_errors', $error_display);
ini_set('display_startup_errors', $error_display);
ini_set('memory_limit', -1);
ini_set('max_execution_time', 0);
ini_set('allow_url_fopen', 1); 
ini_set('max_file_size', -1);
ini_set('implicit_flush', 1);
session_start();
set_time_limit(0);
ob_implicit_flush(1);


/*

By: MrCl0wnLab
My blog: http://blog.mrcl0wn.com/
GitHub: https://github.com/MrCl0wnLab
Twitter: https://twitter.com/MrCl0wnLab


    MODIFICAR O ARQUIVO  PHP-FPM ]
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

    https://php.net/manual/en/install.fpm.configuration.php
    https://serversforhackers.com/c/php-fpm-process-management 

    Script By: mr.Cl0wn  - v1.0  ( DEMO )
    http://blog.mrcl0wn.com
    https://github.com/MrCl0wnLab           
*/


$op = getopt('t:f:h::c:d::v:',['thr:','help::','grep:','range:']);

$banner = "\033[01;37m
    ███████╗██╗   ██╗███████╗███████╗██╗██╗     
    ██╔════╝██║   ██║╚══███╔╝╚══███╔╝██║██║     
    █████╗  ██║   ██║  ███╔╝   ███╔╝ ██║██║     
    ██╔══╝  ██║   ██║ ███╔╝   ███╔╝  ██║██║     
    ██║     ╚██████╔╝███████╗███████╗██║███████╗
    ╚═╝      ╚═════╝ ╚══════╝╚══════╝╚═╝╚══════╝\033[0m
    \033[0;31mscript by: Mr.Cl0wn  - v1.0  ( DEMO )\033[0m
    \033[01;37m* http://blog.mrcl0wn.com\033[0m
    \033[01;37m* https://github.com/MrCl0wnLab\033[0m        

";
$msg_err = $banner."
                                    
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
[!] [EXAMPLE]
     php fuzzil.php  -t http://www3.ILUSTRATIVO.gov.br/FUZZ -f 0day.txt --grep 'Admin - Autentica'
     php fuzzil.php  -t http://FUZZ.ILUSTRATIVO.gov.br/Sistema/Login.aspx -f sub.txt --grep 'Admin - Autentica'
     php fuzzil.php  -t 'http://FUZZ.ILUSTRATIVO.gov.br/Sistema/Login.aspx?login=1%27' -f sub.txt --grep 'SQL syntax;'
     php fuzzil.php  -t 'http://FUZZ.ILUSTRATIVO.gov.br/admin.FUZZ' -f ext.txt'
     php fuzzil.php  -t http://wwwFUZZ.ILUSTRATIVO.gov.br  --range 1-100
     php fuzzil.php  -f hots_check_status_code.txt
                                    
\n\n";


isset($op['help']) || isset($op['h'])  ?
        exit($msg_err) : NULL;

$target_op = isset($op['t']) ? 
        $op['t'] : NULL;

$_SESSION['grep_op'] = isset($op['grep']) ? 
        $op['grep'] : NULL;        

$thread_op = isset($op['thr']) ? 
        $op['thr'] : 1;

if(isset($op['f'])){
    $file_op =  $op['f'];
    $arrytarget = (array)__open_targets($file_op); 
}

 if(isset($op['range'])){
    $_ = explode('-', $op['range']);
    $arrytarget = range($_[0], $_[1]);
 }
 

$_SESSION['CODE_SAVE'] = [200,301,302,401,402,403,'grep'];
$_SESSION['tSTART'] = 0;
$_SESSION['tCOUNT_URL'] = 0;
$_SESSION['tEND'] = $thread_op;
$_SESSION['tTIME'] = ($_SESSION['tEND']/10);
$_SESSION['TMP'] = NULL;

echo $banner;

__exec($arrytarget,$target_op);

function __exec($arrytarget,$target_op){

    $i = $_SESSION['tSTART'];
    $sleep = 0;
    foreach ($arrytarget as $key => $value):
        if(!empty($value)):

            $target_mount = isset($target_op) ?
                 str_replace("FUZZ",$value,$target_op) : $value;

            if($i == $_SESSION['tEND']):
                __start_process('__request',$cmd);
                $i = 0;
                $cmd = null;
                $sleep++;
            endif;
            ($sleep==$_SESSION['tEND'] ) ? ($sleep=0).
            #(print("\n[!] TIME SLEEP: {$_SESSION['tTIME']}\n\n")).
            (sleep($_SESSION['tTIME'])) : NULL;

            $cmd[] = [
                $_SESSION['tCOUNT_URL']++ => $target_mount
            ];

            $i++;
        endif;
       
    endforeach;
    
}

function __start_process($func,$arrvalue){

    for ($i = $_SESSION['tSTART']; $i <= $_SESSION['tEND']; ++$i):
        if(!empty($arrvalue[$i])):   
            $pid = pcntl_fork();
            if ($pid == -1) {
                exit("Error forking...\n");
            }else if ($pid == 0) {
                $func([$arrvalue[$i]]);
                exit(0);
            }
        endif;
    endfor;

    while (pcntl_waitpid(0, $status) != -1):
            $status = pcntl_wexitstatus($status);     
    endwhile;
}

function __open_targets($file){
    return   array_filter(array_unique(explode("\n",file_get_contents($file))));
}

function __multiple_threads_request($nodes){

    $mh = curl_multi_init();
    $curl_array = [];
    foreach($nodes as $key => $value):
        foreach($value as $url):
            $id = key($value);
            $curl_array[$id] = curl_init($url);
            curl_setopt($curl_array[$id], CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl_array[$id], CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl_array[$id], CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($curl_array[$id], CURLOPT_HEADER, TRUE);
            curl_setopt($curl_array[$id], CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl_array[$id], CURLOPT_USERAGENT, __getUserAgentRandom());
            curl_setopt($curl_array[$id], CURLOPT_HEADER, TRUE);
            curl_setopt($curl_array[$id], CURLOPT_FRESH_CONNECT, TRUE);
            curl_setopt($curl_array[$id], CURLOPT_TCP_FASTOPEN, TRUE);
            curl_setopt($curl_array[$id], CURLOPT_TCP_NODELAY, FALSE);
            curl_setopt($curl_array[$id], CURLOPT_EXPECT_100_TIMEOUT_MS,   100);
            #curl_setopt($curl_array[$i], CURLOPT_VERBOSE, TRUE);
            curl_setopt($curl_array[$id], CURLOPT_TIMEOUT, 12); 
            curl_setopt($curl_array[$id], CURLOPT_CONNECTTIMEOUT, 12);
            curl_setopt($curl_array[$id], CURLOPT_MAXCONNECTS, 9000);
            curl_multi_add_handle($mh, $curl_array[$id]);
        endforeach;
    endforeach;

    $running = NULL;
    do {
        usleep(10000);
        curl_multi_exec($mh,$running);
    } while($running > 0);

    $res =[];
    foreach($nodes as $key => $value):

        foreach($value as $url):
            //RETORNO DE INFORMAÇÕES DO REQUEST
            $id = key($value);
            $res[$url] =  [
                $id=>curl_getinfo($curl_array[$id]),
                'content'=>curl_multi_getcontent($curl_array[$id]),
                ];
            __plus();
        endforeach;
    endforeach;
   
    foreach($nodes as $key => $value):
        foreach($value as $url):
            $id = key($value);
            curl_multi_remove_handle($mh, $curl_array[$id]);
        endforeach;
    endforeach;

    curl_multi_close($mh);       
    return $res;
} 

function __request($url){

    $all_info = (__multiple_threads_request($url));

    foreach ($all_info as $key => $value):
            foreach ($value as $url):

                if(isset($url['url'])):

                    $id     = key($value);
                    $cor    = __colorCode($url['http_code']);
                    $title  = __page_title($value['content']);
                    $grep   = __grep($_SESSION['grep_op'],$value['content'])[0];

                    __saveValue($url['url'],$url['http_code']);

                    if(isset($grep))
                        __saveValue($url['url'],'grep');
                    
                    __plus();
                    echo "{$cor}[+][{$id}][ ".date("d-m-Y H:i:s")." ] URL: {$url['url']} : {$url['http_code']} :  {$title}\n\033[0m";
                    __plus();

                endif;

        endforeach;
    endforeach;

}

function __saveValue($url,$code){
    if (in_array($code,$_SESSION['CODE_SAVE'] , true)):
        file_put_contents("{$code}-".date("d-m-Y").".log", "{$url}\n", FILE_APPEND);
    endif;
}

function __colorCode($code){
		   
    $c = __sysColor();

    switch ($code):
        case 200:
            return $c['5'];
        break;
        case 302:
            return $c['6'];
        break;
        case 301:
            return $c['7'];
        break;
        case 404:
            return $c['9'];
        break;
        case 400:
            return $c['2'];
        break;
        case 403:
            return $c['10'];
        break;
        case 500:
            return $c['9'];
        break;
        case 0:
            return $c['16'];
        break;
        case 429:
            return $c['14'];
        break;
        case 203:
            return $c['3'];
        break;
        case 503:
            return $c['8'];
        break;
        case 401:
            return $c['8'];
        break;
    endswitch;
}

 function __sysColor() {

        // DEFINING COLORS //
        $color["0"] = "\033[0m";      // END OF COLOR
        $color["1"] = "\033[1;37m";   // WHITE
        $color["2"] = "\033[01;33m";  // YELLOW
        $color["3"] = "\033[1;31m";   // RED LIGHT
        $color["4"] = "\033[32m";     // GREEN 
        $color["5"] = "\033[1;32m";   // GREEN LIGHT
        $color["6"] = "\033[0;35m";   // PURPLE
        $color["7"] = "\033[1;30m";   // DARK GREY
        $color["8"] = "\033[0;34m";   // BLUE
        $color["9"] = "\033[0;37m";   // LIGHT GREY
        $color["10"] = "\033[0;33m";  // BROWN
        $color["11"] = "\033[1;35m";  // LIGHT PURPLE
        $color["12"] = "\033[0;31m";  // RED
        $color["13"] = "\033[1;36m";  // LIGHT CYAN
        $color["14"] = "\033[0;36m";  // CIANO
        $color["15"] = "\033[1;34m";  // LIGHT BLUE
        $color["16"] = "\033[02;31m"; // DARK RED
        $color["17"] = "\033[01;37m"; // WHITE
    
    return $color;
}

function __page_title($source) {

    if (!$source) 
        return null;

    $res = preg_match("/<title>(.*)<\/title>/siU", $source, $title_matches);

    if (!$res) 
        return null; 

    $title = preg_replace('/\s+/', ' ', $title_matches[1]);
    $title = trim($title);
    __plus();
    return $title;
}

function __getUserAgentRandom() {

        $agentBrowser = ['Firefox', 'Safari', 'Opera', 'Flock', 'Internet Explorer', 'Seamonkey', 'Tor Browser', 'GNU IceCat', 'CriOS', 'TenFourFox',
            'SeaMonkey', 'B-l-i-t-z-B-O-T', 'Konqueror', 'Mobile', 'Konqueror', 'Netscape', 'Chrome', 'Dragon', 'SeaMonkey', 'Maxthon', 'IBrowse',
            'K-Meleon', 'GoogleBot', 'Konqueror', 'Minimo', 'Googlebot', 'WeltweitimnetzBrowser', 'SuperBot', 'TerrawizBot', 'YodaoBot', 'Wyzo', 'Grail',
            'PycURL', 'Galaxy', 'EnigmaFox', '008', 'ABACHOBot', 'Bimbot', 'Covario IDS', 'iCab', 'KKman', 'Oregano', 'WorldWideWeb', 'Wyzo', 'GNU IceCat',
            'Vimprobable', 'uzbl', 'Slim Browser', 'Flock', 'OmniWeb', 'Rockmelt', 'Shiira', 'Swift', 'Pale Moon', 'Camino', 'Flock', 'Galeon', 'Sylera'];

        $agentSistema = ['Windows 3.1', 'Windows 95', 'Windows 98', 'Windows 2000', 'Windows NT', 'Linux 2.4.22-10mdk', 'FreeBSD',
            'Windows XP', 'Windows Vista', 'Redhat Linux', 'Ubuntu', 'Fedora', 'AmigaOS', 'BackTrack Linux', 'iPad', 'BlackBerry', 'Unix',
            'CentOS Linux', 'Debian Linux', 'Macintosh', 'Android', 'iPhone', 'Windows NT 6.1', 'BeOS', 'OS 10.5', 'Nokia', 'Arch Linux',
            'Ark Linux', 'BitLinux', 'Conectiva (Mandriva)', 'CRUX Linux', 'Damn Small Linux', 'DeLi Linux', 'Ubuntu', 'BigLinux', 'Edubuntu',
            'Fluxbuntu', 'Freespire', 'GNewSense', 'Gobuntu', 'gOS', 'Mint Linux', 'Kubuntu', 'Xubuntu', 'ZeVenOS', 'Zebuntu', 'DemoLinux',
            'Dreamlinux', 'DualOS', 'eLearnix', 'Feather Linux', 'Famelix', 'FeniX', 'Gentoo', 'GoboLinux', 'GNUstep', 'Insigne Linux',
            'Kalango', 'KateOS', 'Knoppix', 'Kurumin', 'Dizinha', 'TupiServer', 'Linspire', 'Litrix', 'Mandrake', 'Mandriva', 'MEPIS',
            'Musix GNU Linux', 'Musix-BR', 'OneBase Go', 'openSuSE', 'pQui Linux', 'PCLinuxOS', 'Plaszma OS', 'Puppy Linux', 'QiLinux',
            'Red Hat Linux', 'Red Hat Enterprise Linux', 'CentOS', 'Fedora', 'Resulinux', 'Rxart', 'Sabayon Linux', 'SAM Desktop', 'Satux',
            'Slackware', 'GoblinX', 'Slax', 'Zenwalk', 'SuSE', 'Caixa Mágica', 'HP-UX', 'IRIX', 'OSF/1', 'OS-9', 'POSYS', 'QNX', 'Solaris',
            'OpenSolaris', 'SunOS', 'SCO UNIX', 'Tropix', 'EROS', 'Tru64', 'Digital UNIX', 'Ultrix', 'UniCOS', 'UNIflex', 'Microsoft Xenix',
            'z/OS', 'Xinu', 'Research Unix', 'InfernoOS'];

        $locais = ['cs-CZ', 'en-US', 'sk-SK', 'pt-BR', 'sq_AL', 'sq', 'ar_DZ', 'ar_BH', 'ar_EG', 'ar_IQ', 'ar_JO',
            'ar_KW', 'ar_LB', 'ar_LY', 'ar_MA', 'ar_OM', 'ar_QA', 'ar_SA', 'ar_SD', 'ar_SY', 'ar_TN', 'ar_AE', 'ar_YE', 'ar',
            'be_BY', 'be', 'bg_BG', 'bg', 'ca_ES', 'ca', 'zh_CN', 'zh_HK', 'zh_SG', 'zh_TW', 'zh', 'hr_HR', 'hr', 'cs_CZ', 'cs',
            'da_DK', 'da', 'nl_BE', 'nl_NL', 'nl', 'en_AU', 'en_CA', 'en_IN', 'en_IE', 'en_MT', 'en_NZ', 'en_PH', 'en_SG', 'en_ZA',
            'en_GB', 'en_US', 'en', 'et_EE', 'et', 'fi_FI', 'fi', 'fr_BE', 'fr_CA', 'fr_FR', 'fr_LU', 'fr_CH', 'fr', 'de_AT', 'de_DE',
            'de_LU', 'de_CH', 'de', 'el_CY', 'el_GR', 'el', 'iw_IL', 'iw', 'hi_IN', 'hu_HU', 'hu', 'is_IS', 'is', 'in_ID', 'in', 'ga_IE',
            'ga', 'it_IT', 'it_CH', 'it', 'ja_JP', 'ja_JP_JP', 'ja', 'ko_KR', 'ko', 'lv_LV', 'lv', 'lt_LT', 'lt', 'mk_MK', 'mk', 'ms_MY',
            'ms', 'mt_MT', 'mt', 'no_NO', 'no_NO_NY', 'no', 'pl_PL', 'pl', 'pt_PT', 'pt', 'ro_RO', 'ro', 'ru_RU', 'ru', 'sr_BA', 'sr_ME',
            'sr_CS', 'sr_RS', 'sr', 'sk_SK', 'sk', 'sl_SI', 'sl', 'es_AR', 'es_BO', 'es_CL', 'es_CO', 'es_CR', 'es_DO', 'es_EC', 'es_SV',
            'es_GT', 'es_HN', 'es_MX', 'es_NI', 'es_PA', 'es_PY', 'es_PE', 'es_PR', 'es_ES', 'es_US', 'es_UY', 'es_VE', 'es', 'sv_SE',
            'sv', 'th_TH', 'th_TH_TH', 'th', 'tr_TR', 'tr', 'uk_UA', 'uk', 'vi_VN', 'vi'];

        shuffle($agentBrowser);
        shuffle($agentSistema);
        shuffle($locais);

        return $agentBrowser[0] . '/' . rand(1, 20) . '.' . rand(0, 20) . ' (' . $agentSistema[0] . ' ' . rand(1, 7) . '.' . rand(0, 9) . '; ' . $locais[0] . ';)';
}

function __grep($regex,$value){

    preg_match_all("/{$regex}/", $value,$ret);
	return $ret[0];

}
    
function __plus() {
    ob_flush();
    flush();
}





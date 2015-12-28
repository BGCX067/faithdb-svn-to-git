<?php
//release version
define('RELEASE_VERSION','v7.8.1');

//debug and log define
define('DEBUG',		false);
define('DEBUG_LOG',	false);

//error_handler,by system
define('ERROR_DISPLAY',	true);
define('ERROR_LOG',		true);

//exception_handler, by code,thow PFCException($msg,$code)
define('EXCEPTION_DISPLAY',	true);
define('EXCEPTION_LOG',		true);

define('DEBUG_LOGFILE',SERVER_ROOT.'/tmp/debug/{Date:Ymd}.log');
define('ERROR_LOGFILE',SERVER_ROOT.'/tmp/error/{Type}_{Date:Ymd}.log');

define('CACHE_PATH',SERVER_ROOT.'/tmp/cache');

//TPL_PATH:template src path, define it in your code, default 'tpl'
//define('TPL_PATH','tpl');

//template cache path define
define('TPL_CACHE_PATH',SERVER_ROOT.'/tmp/tpl_cache');

?>
<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/*
Coa
Basic Configuration
*/

# Default Page Template
if( !isset($pagetemplate) ) $pagetemplate = 'page.coa.txt';

# Theme Path
define('THEMEPATH', 'theme/'.$TEMPLATE.'/');

# Page Template Path
define('PAGETEMPLATE', THEMEPATH.'coa/'.$pagetemplate);

# Constant Template Path
define('CONSTANTTEMPLATE', THEMEPATH.'coa/constant.coa.txt');

# Translation Name for URL
define('TRANSLATIONURL', 'translated');

# Subdirectory Rewrite Path
define('SUBDIRPATH', '/');
?>
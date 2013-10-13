<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

# Theme Path
define('THEMEPATH', 'theme/'.$TEMPLATE.'/');

# Page Template Path
define('PAGETEMPLATE', THEMEPATH.'coa/page.coa.php');

# Constant Template Path
define('CONSTANTTEMPLATE', THEMEPATH.'coa/constant.coa.php');

# Translation Name for URL
define('TRANSLATIONURL', 'de');

# Subdirectory Rewrite Path
define('SUBDIRPATH', '/');

# Include Coa
include 'plugins/CoaPlugin/coa.php';
?>
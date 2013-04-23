<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/*
@File: template.php
@Package: GetSimple
@Action: Coa 1.8 for GetSimple 3.2
*/

/*
Coa, a GetSimple framework

Coa is a powerful framework to create lean and functional templates.
It provides CoaScript and some useful out of the box features for fast and flexible development.

Please read the manual for details.

Copyright (c) 2013 Benjamin Scherer 
http://coa.plue.me
*/

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
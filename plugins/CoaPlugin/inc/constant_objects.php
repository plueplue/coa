<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/*
Make Constant Objects
Echo HTML Header
*/


/* Get Config Object
 * since 1.0 */

function configObj($path) {
  global $curPid, $USR;
  
  $OA = array();
  $configOA = array();
  
  // get constant objects and lenght
  $OBJ = getObjects($path);
  $objLength = count($OBJ);

  // for each object
  for ($i=0; $i<$objLength; $i++) {

    // create $OA and get attributes
    $OA = $OBJ[$i];
    $OA = getAttributes($OA,$curPid);

    // make config object array
    if ($OA['allTrue'] == 1 && $OA['type'] == 'config') {
        
        // require Login
        if (isset($OA['loginReq']) && $OA['loginReq'] == 1 && !$USR) header('location:'.get_site_url(false).'admin/index.php?redirect='.$_SERVER['REQUEST_URI']);
        
        // all attributes to config array
        $configOA = $OA;
        
        // wrap page
        if(isset($OA['wrapPage'])) {
          $configOA['wrapPageB'] = wrapB($OA['wrapPage']);
          $configOA['wrapPageA'] = wrapA($OA['wrapPage']);
       } 
    }  
  }
  return $configOA;
}

// Make Config Object Array
$configOA = configObj(CONSTANTTEMPLATE);


/* Main Object
 * since 1.0 */

function mainObj($OA,$configOA) {
  global $TRANS;
  
  // vars
  $cB = '<!--[if ';
  $cE = '<![endif]-->';
  
  // get language
  if (!isset($OA['language'])) $languageA[0] = 'en';
  else $languageA = explComma($OA['language']);
  if (!isset($languageA[1])) $languageA[1] = $languageA[0];
  
  // doctype
  echo '<!doctype html>'."\n";
  
  // html tag
  if (isset($configOA['htmlTag'])) echo $configOA['htmlTag'];
  elseif (!$TRANS) echo '<html lang="'.$languageA[0].'">';
  else echo '<html lang="'.$languageA[1].'">';
      
  // <head> tag and charset
  echo "\n".'<head>'."\n";
  if (isset($OA['charset'])) echo '<meta charset="'. $OA['charset'] .'">'."\n";
  else echo '<meta charset="utf-8">'."\n";
  
  // force latest IE rendering engine and chrome frame
  echo '<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->'."\n";
        
  // page title
  if(isset($OA['title'])) echo '<title>'.$OA['title'].'</title>'."\n";
  else echo '<title>$title</title>'."\n";
      
  // attribute "base" 
  if(isset($OA['base']) && $OA['base']!=='none' || !isset($OA['base'])) echo '<base href="'.get_site_url(false).'">'."\n";
}


/* Meta Object
 * since 1.0 */

function metaObj($OA) {
  $metaB = '<meta name="';
    
  // attribute "viewport"
  if (isset($OA['viewport'])) echo $metaB.'viewport" content="'. $OA['viewport'] .'">'."\n";
      
  // attribute "robots"
  if (isset($OA['robots'])) echo $metaB.'robots" content="'. $OA['robots'] .'">'."\n";
      
  // attribute "description"
  if (isset($OA['description'])) echo $metaB.'description" content="'. $OA['description'] .'">'."\n";
      
  // attribute "kewords"
  if (isset($OA['keywords'])) echo $metaB.'keywords" content="'. $OA['keywords'] .'">'."\n";
  
  // GS header or canonical
  if(!isset($OA['headerGS'])) $OA['headerGS'] = 1;
  if($OA['headerGS'] == 1) get_header();
  else echo '<link rel="canonical" href="'.get_page_url(true).'">'."\n";

}


/* Make CSS
 * since 1.3 */

function makeCSS($value,$mediaTag,$OA) {
  if (substr($value,0,4) == 'http') $pathT = '';
  else $pathT = THEMEPATH;
  
  if(!isset($OA['wrap'])) {
    $OA['wrapB'] = '';
    $OA['wrapA'] = '';
  }
      
  echo $OA['wrapB']. '<link rel="stylesheet" href="'. $pathT . $value .'"'. $mediaTag .'>'.$OA['wrapA']."\n";
}


/* CSS Object
 * since 1.0 */

function cssObj($OA) {
  $cssB = '<link rel="stylesheet" href="';
  
  for ($k=0; $k<10; $k++) {
    if (isset($OA['all'][$k])) { $mediaTag = ''; makeCSS($OA['all'][$k], $mediaTag, $OA); }
    if (isset($OA['screen'][$k])) { $mediaTag = ' media="screen"'; makeCSS($OA['screen'][$k], $mediaTag, $OA); }
    if (isset($OA['handheld'][$k])) { $mediaTag = ' media="handheld"'; makeCSS($OA['handheld'][$k], $mediaTag, $OA); }
    if (isset($OA['print'][$k])) { $mediaTag = ' media="print"'; makeCSS($OA['print'][$k], $mediaTag, $OA); }
  }
}


/* Script Object
 * since 1.0 */

function scriptObj($OA) {
  for ($k=0; $k<10; $k++) {
    if (isset($OA['js'][$k])) {
      if (substr($OA['js'][$k],0,4) == 'http') $pathT = '';
      else $pathT = THEMEPATH;
      
      if(!isset($OA['wrap'])) {
        $OA['wrapB'] = '';
        $OA['wrapA'] = '';
      }
      
      echo $OA['wrapB']. '<script src="' . $pathT . $OA['js'][$k] .'"></script>'.$OA['wrapA']."\n";
    }
  }
}


/* Header End
 * since 1.0 */

function headEnd($configOA) {
  global $url;
  
  echo '</head>'."\n\n";
  
  if(!isset($configOA['bodyTag'])) $configOA['bodyTag'] = '';
  
  // basic body tag
  if ($configOA['bodyTag'] == 'basic') {
     echo '<!--[if lt IE 9]><body id="'.$url.'" class="ie no_js"><![endif]-->'."\n".
        '<!--[if (gt IE 8)|!(IE)]><!--><body id="'.$url.'" class="no_js"><!--<![endif]-->'."\n";
  }
  
  // extended body tag
  elseif ($configOA['bodyTag'] == 'extended') {
     echo '<!--[if lt IE 7]><body id="'.$url.'" class="ie ie6 no_js"><![endif]-->'."\n".
        '<!--[if IE 7]><body id="'.$url.'" class="ie ie7 no_js"><![endif]-->'."\n".
        '<!--[if IE 8]><body id="'.$url.'" class="ie ie8 no_js"><![endif]-->'."\n".
        '<!--[if IE 9]><body id="'.$url.'" class="ie ie9 no_js"><![endif]-->'."\n".
        '<!--[if (gt IE 9)|!(IE)]><!--><body id="'.$url.'" class="no_js"><!--<![endif]-->'."\n";
  }
  
  // custom body tag
  elseif ($configOA['bodyTag']) echo $configOA['bodyTag']."\n";
  
  // default body tag
  else echo '<body id="'.$url.'">'."\n";
  
  // wrapPage
  if (isset($configOA['wrapPageB'])) echo $configOA['wrapPageB']."\n";
}
?>
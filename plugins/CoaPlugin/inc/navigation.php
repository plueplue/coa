<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/*
Make Various Navigation Types
Arrange Menu Links for Page Translation
*/


/* Lang Rewrite
 * since 1.5 */

if (isset($_GET['L']) == TRANSLATIONURL) $TRANS = 1;
if (substr($PERMALINK, -11) == '%slug%.html') $fancyEnd = 1;
else $fancyEnd = 0;

if ($fancyEnd && $TRANS) $langBase = TRANSLATIONURL;
else $langBase = '';


/* Make Hierachical Menu 
 * since 1.0 */

function makeMenu($currentpage,$OA) {
  global $PERMALINK, $pagesArray, $langBase, $TRANS;
  $pagesSorted = subval_sort($pagesArray,'menuOrder');
  $result = '';

  // for each matching page
  foreach ($pagesSorted as $page) {
  
    // show hidden pages only as list menu
    if ($page['menuStatus'] == 'Y' || isset($OA['listA']) && in_array($page['url'], $OA['listA'])) {
      if (isset($OA['listA']) && in_array($page['url'], $OA['listA']) || !isset($OA['list'])) {
        if (!$page['parent'] || isset($OA['listA'])) {

          // fallback menu title
          if (!isset($page['menu']) || !$page['menu']) $page['menu'] = $page['title'];
          
          // translations
          $menu_ = menuTranslation($page,'menu');
          $title_ = menuTranslation($page,'title');
          
          // find url
          $findUrl = getUrl($page['url'],$page['parent']);
          
          // get language param and base
          $langParam = getLangParam($PERMALINK,$findUrl);
          if(!$TRANS) $langBase = '';

          // make <li> tags and go for sub
          $result .= liTag($currentpage,$page,$OA,0).'<a class="'.$page['url'].'" href="'.$langBase.$findUrl.$langParam.'"';
          if (isset($OA['title']) && $OA['title'] == 1) $result.= ' title="'.strip_quotes($title_).'"';
          $result .= '>'.$menu_.'</a>';
          
          $makeSub = makeSub($page['url'],$currentpage,$OA);
          if ($makeSub) $result .= '<ul>'.$makeSub.'</ul>';
          
          $result .= liTag($currentpage,$page,$OA,1); 
        }
      }
    }
  }
  echo exec_filter('menuitems',$result);
}


/* Make Submenu
 * since 1.0 */

function makeSub($url,$currentpage,$OA) {
  global $PERMALINK, $pagesArray, $langBase, $TRANS;
  $pagesSorted = subval_sort($pagesArray,'menuOrder');
  $result = '';
  
  // for each matching page
  foreach ($pagesSorted as $page) {
  
    // show hidden pages only as list menu
    if ( ($page['menuStatus'] == 'Y' || isset($OA['listA']) && in_array($page['url'], $OA['listA'])) && $url == $page['parent'] ) {
      if (isset($OA['listA']) && in_array($page['url'], $OA['listA']) || !isset($OA['listA'])) {

        // fallback menu title
        if (!isset($page['menu']) || !$page['menu']) $page['menu'] = $page['title'];
        
        // translations
        $menu_ = menuTranslation($page,'menu');
        $title_ = menuTranslation($page,'title');
        
        // find url
        $findUrl = getUrl($page['url'],$page['parent']);
        
        // get language param and base
        $langParam = getLangParam($PERMALINK,$findUrl);
        if(!$TRANS) $langBase = '';
        
        // make <li> tags and go for sub
        $result .= liTag($currentpage,$page,$OA,0).'<a class="'.$page['url'].'" href="'.$langBase.$findUrl.$langParam.'"';
        if (isset($OA['title']) && $OA['title'] == 1) $result.= ' title="'.strip_quotes($title_).'"';
        $result .= '>'.$menu_.'</a>';
          
        $makeSub = makeSub($page['url'],$currentpage,$OA);
        if ($makeSub) $result .= '<ul>'.$makeSub.'</ul>';
          
        $result .= liTag($currentpage,$page,$OA,1);
      }
    }
  }
  return exec_filter('menuitems',$result);
}


/* <li> Tag
 * since 1.0 */

function liTag($currentpage,$page,$OA,$which) {
  
  // check state of page
  if ($currentpage == $page['url']) { 
    if (isset($OA['curB'])) { $liB = $OA['curB']; $liA = $OA['curA']; } 
    elseif (isset($OA['actB'])) { $liB = $OA['actB']; $liA = $OA['actA']; }
    elseif (isset($OA['itemB'])) { $liB = $OA['itemB']; $liA = $OA['itemA']; }
  } 
  elseif (isset($OA['item'])) { 
    $liB = $OA['itemB']; $liA = $OA['itemA'];
  }

  // is active through child?
  if (isset($OA['act']) && hasActChild($page['url'],$currentpage) == true) {
    $liB = $OA['actB']; $liA = $OA['actA'];
  }

  // if no li defined
  if (!isset($liB)) $liB = '<li>'; $liA = '</li>';

  // return li tags
  if ($which == 0) return $liB;
  elseif ($which == 1) return $liA;
}


/* Has Act Child 
 * since 1.0 */

function hasActChild($this,$currentpage) {
  global $pagesArray;
  $pagesSorted = subval_sort($pagesArray,'menuOrder');
       
  foreach ($pagesSorted as $page) {
        
    // if there is child fot $this
    if ($page['parent'] == $this) {
        
      // child url
      $child = $page['url'];
      if ($child == $currentpage) return true;
          
      if (hasActChild($child,$currentpage) == true) return true; 
    } 
  }  
}


/* Make Breadcrumb
 * since 1.0 */

function makeBreadcrumb($currentpage,$initial,$OA) {
  global $PERMALINK, $pagesArray, $langBase, $TRANS;
  $pagesSorted = subval_sort($pagesArray,'menuOrder');

  // for each page
  if (count($pagesSorted) != 0) { 
    foreach ($pagesSorted as $page) {
      $i = 1; $i <= count($pagesSorted); $i++;

      if ($currentpage == $page['url']) {
        
        // default item wraps
        if (!isset($OA['itemB'])) { $OA['itemB'] = '<li>'; $OA['itemA'] = '</li>'; }
        if (!isset($OA['actB'])) { $OA['actB'] = $OA['itemB']; $OA['actA'] = $OA['itemA']; }
        if (!isset($OA['curB'])) { $OA['curB'] = $OA['actB']; $OA['curA'] = $OA['actA']; }

        // if no menu title
        if (!isset($page['menu']) || !$page['menu']) $page['menu'] = $page['title'];
        
        // translations
        $menu_ = menuTranslation($page,'menu');
        $title_ = menuTranslation($page,'title');
        
        // find url
        $findUrl = getUrl($page['url'],$page['parent']);
        
        // get language param and base
        $langParam = getLangParam($PERMALINK,$findUrl);
        if(!$TRANS) $langBase = '';

        // a tag with href
        if ($initial == 1) $breadcrumb[$i] = $OA['curB'].'<a class="'.$page['url'].'" href="'.$langBase.$findUrl.$langParam;
        elseif ($initial == 0) $breadcrumb[$i] = $OA['itemB'].'<a class="'.$page['url'].'" href="'.$langBase.$findUrl.$langParam;
          
        // if title is 'none'
        if (isset($OA['title']) && $OA['title'] == 1) $breadcrumb[$i].=  '" title="'. strip_quotes($title_);            
          
        // a tag and visible text
        if ($initial == 1) $breadcrumb[$i].= '">'.$menu_.'</a>'.$OA['curA'];
        elseif ($initial == 0) $breadcrumb[$i].= '">'.$menu_.'</a>'.$OA['itemA'];
        
        // again for parent
        if ($page['parent']) makeBreadcrumb($page['parent'],0,$OA);
      }
    }
  }
  print_r($breadcrumb[$i]);
}


/* Make Language Menu
 * since 1.5 */

function makeLangNav($currentpage,$OA) {
  global $configOA, $parent, $PERMALINK, $TRANS, $fancyEnd, $langBase;
  
  if(!isset($OA['curB'])) $OA['curB'] = '';
  if(!isset($OA['curA'])) $OA['curA'] = '';
  if(!isset($OA['actB'])) $OA['actB'] = '';
  if(!isset($OA['actA'])) $OA['actA'] = '';
  if(!isset($OA['itemB'])) $OA['itemB'] = '';
  if(!isset($OA['itemA'])) $OA['itemA'] = '';
  
  // get langTitles
  $langTitlesA = explComma($OA['language']);
  
  // check param separator
  if (strstr(find_url($currentpage,$parent), '?')) $sep = '&';
  else $sep = '?';
  
  $langParam = $sep.'L='.TRANSLATIONURL;
  
  // check permalink
  if ($fancyEnd) { $langBase = TRANSLATIONURL; $langParam = ''; }
  
  // find url
  $findUrl = find_url($currentpage,$parent,'relative');

  // check if subdirectory
  if(SUBDIRPATH != '/' && SUBDIRPATH != '') {
    $urlContainsSubdir = substr($findUrl, 0, strlen(SUBDIRPATH));
    if(SUBDIRPATH == $urlContainsSubdir) $findUrl = substr($findUrl, strlen(SUBDIRPATH));
  }

  // get default lang item wrap
  $liB = ''; $liA = '';
  if(!$TRANS) { $liB = $OA['curB']; $liA = $OA['curA']; }
  if(!$TRANS && !$liB) { $liB = $OA['actB']; $liA = $OA['actA']; }
  if(!$liB) { $liB = $OA['itemB']; $liA = $OA['itemA']; }
  if(!$liB) { $liB = '<li>'; $liA = '</li>'; }
  
  // echo <li> tag for default language
  echo $liB.'<a href="'.$findUrl.'">'.$langTitlesA[0].'</a>'.$liA;

  // adjust translated path
  if($fancyEnd && SUBDIRPATH != '/' && SUBDIRPATH != '') $findUrl = '/'.$findUrl;

  // adjust translated url
  if($currentpage == 'index') {
    if($fancyEnd == 1) $findUrl = '/index.html';
    if($fancyEnd == 2) $findUrl = '/index/';
  }

  // get translated lang item wrap
  $liB = ''; $liA = '';
  if($TRANS) { $liB = $OA['curB']; $liA = $OA['curA']; }
  if($TRANS && !$liB) { $liB = $OA['actB']; $liA = $OA['actA']; }
  if(!$liB) { $liB = $OA['itemB']; $liA = $OA['itemA']; }
  if(!$liB) { $liB = '<li>'; $liA = '</li>'; }
  
  // echo <li> tag for translation
  echo $liB.'<a href="'.$langBase.$findUrl.$langParam.'">'.$langTitlesA[1].'</a>'.$liA;
}


/* Get Language Parameter
 * since 1.5 */

function getLangParam($PERMALINK,$findUrl) {
  global $TRANS, $fancyEnd;
    
  // reset for fancy urls
  if ($fancyEnd) {
    $langParam = '';
      
  // get separator and return param
  } else {
    if (strstr($findUrl, '?')) $sep = '&';
    else $sep = '?';
            
    if($TRANS) $langParam = $sep.'L='.TRANSLATIONURL;
    else $langParam = '';
      
    return $langParam;
  }
}


/* Get Menu Translation
 * since 1.5 */
 
function menuTranslation($page,$elem) {
  global $TRANS;

  // menu translation
  if($menu_ = getPageData('menu', $page['url'])) { } 
  else { $menu_ = ''; }
  
  if($TRANS && $menu_) $menu_transl = $menu_;
  else $menu_transl = $page['menu'];
          
  // attribute title translation
  if($title_ = getPageData('title', $page['url'])) { } 
  else { $title_ = ''; }
  
  if($TRANS && $title_) $transl_title = $title_;
  else $transl_title = $page['title'];
  
  // return translated
  if ($elem = 'menu') return $menu_transl;
  elseif ($elem = 'title') return $transl_title;
}


/* Get Url
 * since 1.5 */
          
function getUrl($url,$parent) {
  global $fancyEnd, $TRANS;
  
  // find url
  $findUrl = find_url($url,$parent,'relative');
  
  // check if subdirectory
  if(SUBDIRPATH != '/' && SUBDIRPATH != '') {
    $urlContainsSubdir = substr($findUrl, 0, strlen(SUBDIRPATH));
    if(SUBDIRPATH == $urlContainsSubdir) $findUrl = substr($findUrl, strlen(SUBDIRPATH));
    if($TRANS && $fancyEnd) $findUrl = '/'.$findUrl;
  }
  
  // findurl for translated index
  if($url == 'index' && $TRANS) {
    if($fancyEnd == 1) $findUrl = '/index.html';
    if($fancyEnd == 2) $findUrl = '/index/';
  } 

  return $findUrl;
}
?>
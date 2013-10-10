<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/*
Make Page Objects
Echo HTML Body
*/


/* Text Object
 * since 1.0 */

function textObj($OA) {

  // echo 'wrap' before
  if(isset($OA['wrapB'])) echo $OA['wrapB'];
          
  // echo 'link' begin
  if (isset($OA['link'])) {
  
    // check for class
    if($OA['class']) $class = ' class="'.$OA['class'].'"';
    else $class = '';
    
    echo '<a'.$class.' href="'. $OA['link'] .'"';
    if(isset($OA['linkT'])) echo ' target="'. $OA['linkT'] .'"';
    echo '>';
  }
          
  // echo 'value'
  if(isset($OA['value'])) echo $OA['value'];  

  // echo 'link' end
  if(isset($OA['link'])) echo '</a>';
          
  // echo 'wrap' after
  if(isset($OA['wrapA'])) echo $OA['wrapA']."\n";   
}


/* Image Object 
 * since 1.0 */ 

function imageObj($OA) {
  
  // check isset
  if(!isset($OA['alt'])) $OA['alt'] = '';
  if(!isset($OA['file'])) $OA['file'] = '';
  if(!isset($OA['title'])) $OA['title'] = '';
  
  // wrap before
  if (isset($OA['wrapB'])) echo $OA['wrapB']."\n";
          
  // link begin
  if (isset($OA['link'])) {
    echo '<a href="'. $OA['link'] .'"';
    if($OA['linkT']) echo ' target="'. $OA['linkT'] .'"';
    echo '>';
  }
    
  // check for absolute path
  if (substr($OA['file'],0,4) == 'http') $pT = '';
  else $pT = THEMEPATH;
  
  // check width and height and echo tag
  if (isset($OA['width'])) {
    echo '<img src="'. $pT . $OA['file'] .'"';
    if ($OA['width'] != 'auto') echo ' width="'. $OA['width'] .'"';
    if ($OA['height'] != 'auto') echo ' height="'. $OA['height'] .'"';
    echo ' alt="'. $OA['alt'] .'"';
    if ($OA['title']) echo ' title="'. $OA['title'] .'"';
    echo '>';
  } else {
    echo '<img src="'. $pT . $OA['file'] .'" alt="'. $OA['alt'] .'"';
    if ($OA['title']) echo ' title="'. $OA['title'] .'"';
    echo '>';
  }
          
  // if 'link' exists
  if (isset($OA['link'])) echo '</a>';
          
  // 'wrap' after
  if (isset($OA['wrapA'])) echo "\n".$OA['wrapA'];
  echo "\n\n";
}


/* Place Object
 * since 1.0 */
    
function placeObj($OA) {
  global $configOA;
  
  // 'wrap' before
  if(isset($OA['wrapB'])) echo $OA['wrapB'];
  
  if(isset($OA['file'])) {
    // get file suffix
    $last4 = substr($OA['file'], -4);
    $last5 = substr($OA['file'], -5);
    $last8 = substr($OA['file'], -8);

    // check file type and go
    if(is_file(THEMEPATH.$OA['file']) && $ptf = THEMEPATH.$OA['file']) {
      if ($last4 == '.coa' || $last8 == '.coa.php' || $last8 == '.coa.txt') makePage($ptf, $configOA, $isPlace = 1);
      elseif ($last4 == '.php') include_once($ptf);
      elseif ($last4 == '.htm' || $last5 == '.html') echo file_get_contents($ptf);
      elseif ($last4 == '.txt') echo htmlentities( file_get_contents($ptf) );
    }
  }
  
  // 'wrap' end
  if(isset($OA['wrapA'])) echo $OA['wrapA'];
}


/* Menu Object 
 * since 1.0 */

function menuObj($OA) {
  global $curPid;
  if (!isset($OA['mode'])) $OA['mode'] = '';
  
  // menu 'wrap' begin
  echo "\n";
  if (!isset($OA['wrapB'])) echo '<ul>';
  else echo $OA['wrapB']."\n";
  
  // make default menu
  if ($OA['mode'] == 'default') get_navigation(get_page_slug(false));
  
  // make breadcrumb
  elseif ($OA['mode'] == 'breadcrumb') makeBreadcrumb(return_page_slug(false),1,$OA);

  // make language manu
  elseif (isset($OA['language'])) makeLangNav(return_page_slug(false),$OA);
  
  // make hierachical menu
  else makeMenu(return_page_slug(false),$OA);

  // menu 'wrap' wrap
  if (!isset($OA['wrapA'])) echo '</ul>';
  else echo $OA['wrapA']."\n\n";
}


/* Get Content of Page
 * since 1.3 */

function getContentOfPage($slug) {
  global $TRANS;
   
  // load file
  $file = @file_get_contents('data/pages/'.$slug.'.xml');
  $data = simplexml_load_string($file);
 
  // check for language and additional content
  if ($TRANS) {
    $return = '<div class="get1 '.$data->url.'">'.$data->translationContent.'</div>';
    
    // show additional content on any language until it is translatable
    if( $data->addContent1L ) $return.= '<div class="get2 '.$data->url.'">'.$data->addContent1L.'</div>';
    if( $data->addContent2L ) $return.= '<div class="get3 '.$data->url.'">'.$data->addContent2L.'</div>';
    if( $data->addContent3L ) $return.= '<div class="get4 '.$data->url.'">'.$data->addContent3L.'</div>';
    
    return stripslashes(htmlspecialchars_decode($return,ENT_QUOTES));
  }
  else {
    $return = '<div class="get1 '.$data->url.'">'.$data->content.'</div>';
    if( $data->addContent1 ) $return.= '<div class="get2 '.$data->url.'">'.$data->addContent1.'</div>';
    if( $data->addContent2 ) $return.= '<div class="get3 '.$data->url.'">'.$data->addContent2.'</div>';
    if( $data->addContent3 ) $return.= '<div class="get4 '.$data->url.'">'.$data->addContent3.'</div>';
        
    return stripslashes(htmlspecialchars_decode($return,ENT_QUOTES));
  }
}


/* Get Page Data
 * since 1.7 */

function getPageData($elem,$getThis='curPid') {
  global $curPid;
   
  // for current page or static slug
  if($getThis != 'curPid') $fileName = $getThis;
  else $fileName = $curPid;
   
  // load file
  $file = @file_get_contents('data/pages/'.$fileName.'.xml');
  $data = simplexml_load_string($file);
   
  // get data
  if($elem == 'content') return stripslashes(htmlspecialchars_decode($data->translationContent,ENT_QUOTES));
  elseif(isset($data->translationTitle) && $elem == 'title') return strip_decode($data->translationTitle,ENT_QUOTES);
  elseif(isset($data->translationMenu) && $elem == 'menu') return strip_quotes($data->translationMenu,ENT_QUOTES);
  elseif($elem == 'add-content-1') return stripslashes(htmlspecialchars_decode($data->addContent1,ENT_QUOTES));
  elseif($elem == 'add-content-2') return stripslashes(htmlspecialchars_decode($data->addContent2,ENT_QUOTES));
  elseif($elem == 'add-content-3') return stripslashes(htmlspecialchars_decode($data->addContent3,ENT_QUOTES));
  elseif($elem == 'add-content-translation-1') return stripslashes(htmlspecialchars_decode($data->addContent1L,ENT_QUOTES));
  elseif($elem == 'add-content-translation-2') return stripslashes(htmlspecialchars_decode($data->addContent2L,ENT_QUOTES));
  elseif($elem == 'add-content-translation-3') return stripslashes(htmlspecialchars_decode($data->addContent3L,ENT_QUOTES));
}
?>
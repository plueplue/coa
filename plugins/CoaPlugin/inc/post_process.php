<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/*
Post Process Buffer
Process Image Tags, Cache and Minify Source, Replace Variables, Protect Mail Addresses
*/


/* Make Tags
 * since 1.2 */

function makeTag($buffer, $tag, $what) {
  global $configOA;
  $i = 0;
  
  // wrap begin and $cut
  if($what == 'images:') { $output = '<span class="coa_images">'; $cut = 7; }
  if($what == 'lightbox:') { $output = '<span class="coa_lb">'; $cut = 9; }
  if($what == 'thumbs:') { $output = '<span class="coa_thumbs">'; $cut = 7; }
  
  // get value
  $value = trim(substr(substr($tag, strpos($tag, $what)+$cut), 0, -1));
  
  // adjust value
  if(!$value) $value = '/';
  if(substr($value, -1) != '/') $value = $value.'/';
  if($value == '/') $value = '';

  // get path
  $path = GSDATAUPLOADPATH.$value;
  if($what == 'thumbs:') $path = GSTHUMBNAILPATH.$value;
  
  // get images and make output
  if (is_dir($path)) {
    if ($dh = opendir($path)) {
    
      // sort files
      $files = array();
      while ($files[] = readdir($dh));
      natsort($files);
    
      foreach ($files as $file) {
        $last4 = substr($file, -4);
        $img_ext = array('.jpg','.pdf','.gif','.png','jpeg');

        // make linked images with alt attribut
        if (in_array($last4, $img_ext)) {
          ++$i;
          $alt_temp = str_replace($img_ext, '', $file);
          $alt = preg_replace('/[-._]/', ' ', $alt_temp);

          if($what == 'lightbox:') {
            $output .= '<a class="n'.$i.'" href="data/uploads/'.$value.$file.'">';
            $output .= '<img src="data/uploads/'.$value.$file.'" alt="'.$alt.'"></a>';
          }
          if($what == 'images:') {
            $output .= '<span class="item n'.$i.'"><em>'.$alt.'</em><img src="'.'data/uploads/'.$value.$file.'" alt="'.$alt.'"></span>';
          }
          if($what == 'thumbs:') {
            if(substr($file, 0, 9) == 'thumbnail') {
              $alt = trim(substr($alt, 9));
              $output .= '<span class="item n'.$i.'"><em>'.$alt.'</em><img src="data/thumbs/'.$value.$file.'" alt="'.$alt.'"></span>';
            }  
          }      
        }
      }
      closedir($dh);
    }
     
  // if path does't exist
  } else {
    $output .= 'Sorry, but this is no folder: '.$path;
  }
  
  // wrap end
  $output .= '</span>';
  
  // adjust tag for regex
  $tag = str_replace('/', '\\/', trim($tag,'[]'));
  $tag = '/(\['.$tag.'\])/';
  
  // replace tag with output
  $result = preg_replace($tag, $output, $buffer);
  
  // include lightbox javascript
  if(!isset($configOA['extFiles'])) $configOA['extFiles'] = '';
  if($what == 'lightbox:' && $configOA['extFiles'] != 'none') {
      $js_and_body = '<script src="plugins/CoaPlugin/res/coa.lightbox.min.js"></script>'."\n".'</body>';
      $buffer = preg_replace('[</body>]', $js_and_body, $result);
  } else {
      $buffer = $result;
  }
  return $buffer;
}


/* Replace Tags
 * since 1.2 */
 
function replaceTags($buffer) {

  // tag patterns
  $lb_pattern = '/(\[lightbox:[a-zA-Z_\-\/0-9.\s]*[\]])/';
  $images_pattern = '/(\[images:[a-zA-Z_\-\/0-9.\s]*[\]])/';
  $thumbs_pattern = '/(\[thumbs:[a-zA-Z_\-\/0-9.\s]*[\]])/';
  $page_pattern = '/(\[page:[a-zA-Z_\-\/0-9.\s]*[\]])/';
  
  // make lightbox tags
  $check_lb = preg_match_all($lb_pattern, $buffer, $lb_tag_a);
  foreach ($lb_tag_a[1] as $lb_tag) $buffer = makeTag($buffer, $lb_tag, 'lightbox:');
  
  // make image tags
  $check_images = preg_match_all($images_pattern, $buffer, $images_tag_a);
  foreach ($images_tag_a[1] as $images_tag) $buffer = makeTag($buffer, $images_tag, 'images:');

  // make thumbnail tags
  $check_thumbs = preg_match_all($thumbs_pattern, $buffer, $thumbs_tag_a);
  foreach ($thumbs_tag_a[1] as $thumbs_tag) $buffer = makeTag($buffer, $thumbs_tag, 'thumbs:');

  // make page tags
  $check_page = preg_match_all($page_pattern, $buffer, $page_tag_a);
  foreach ($page_tag_a[1] as $page_tag) {
    $slug = substr($page_tag, 6, -1);
    if( substr($slug,0,1)==' ' ) { $slug = substr($slug,1); $space = ' '; }
    else { $space = ''; }
    
    $content = getContentOfPage($slug);
    $replace = '[\[page:'.$space.$slug.'\]]';
    $buffer = preg_replace($replace, $content, $buffer);
  }
  
  return $buffer;
}


/* Distort Mail (for links only)
 * since 1.0 */

function distortMail($mail) {
    
  // define condition
  $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
  $key = str_shuffle($character_set); 
  $cipher_text = ''; 
    
  // distort
  for($i=0;$i<strlen($mail);$i+=1) $cipher_text.= $key[strpos($character_set,$mail[$i])];
    
  // change <a> tag 
  $onlick = 'href="javascript:return false" onclick="mailTo('."'".$key."','".$cipher_text."')";
  return $onlick;
}


/* Replace MailTo
 * since 1.0 */

function replaceMailTo($buffer) {

  // search for – and cut – mailto:
  $buffer = preg_replace_callback('|(href="mailto:)([a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+)([a-zA-Z?=%&;0-9-.]*)|',
    create_function('$matches', 'return distortMail($matches[2]);'), $buffer);
    
  return $buffer;
}


/* Replace Mail with ASCII
 * since 1.0 */

function replaceMail($buffer) {
  $buffer = preg_replace_callback('|(%)([a-zA-Z0-9_.-]+)(@)([a-zA-Z0-9-]+)(.)([a-zA-Z0-9-.]+)(%)|',
    create_function('$matches', 'return $matches[2].\'<span>&#064;</span>\'.$matches[4].\'<span>&#046;</span>\'.$matches[6];'), $buffer);
    
  return $buffer;
}


/* Protect Mail JavaScript
 * since 1.0 */

function mailToJS() {
  echo '<script>function mailTo(a,c){var b=a.split("").sort().join("");'.
    'var d="";for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));window.location.href="mailto:"+d}</script>'."\n";
}


/* Minify
 * since 1.0 */

function minify($buffer) {
    
  // strip multiple whitespace sequences
  $search = array ('/\>[^\S ]+/s', '/[^\S ]+\</s');
  $replace = array ('>', '<');
  $buffer = preg_replace($search, $replace, $buffer);
   
  return $buffer;
}


/* Cache and Minify
 * since 1.0 */

// no cache if logged in
if ($USR || !isset($configOA['cache'])) $configOA['cache'] = 0;

// caching is on
if ($configOA['cache'] == 1) {

  // create folder
  if( !is_dir(GSDATAOTHERPATH.'coa') ) mkdir(GSDATAOTHERPATH.'coa', 0770);

  // get filename
  $cacheFileName = preg_replace('/[^a-zA-Z0-9-.]/', '_', $_SERVER['REQUEST_URI']);
  if(substr($cacheFileName, 0, 1) == '_') $cacheFileName = substr($cacheFileName, 1);

  // cache directory and file
  $cachedir = GSDATAOTHERPATH .'coa/';
  if ($cacheFileName == '' && $fancyEnd) define('CACHFILE', $cachedir . 'index.html');
  elseif ($cacheFileName == '') define('CACHFILE', $cachedir . 'index');
  else define('CACHFILE', $cachedir . $cacheFileName );

  // cachefile found
  if(is_file(CACHFILE)) {
    readfile(CACHFILE);
    exit;
  }

// else, caching is off
} else {
  $clearDir = GSDATAOTHERPATH .'coa/';
  $clearFiles = $clearDir.'*';

  // clear cache
  if( is_dir($clearDir) ) {
    foreach(glob($clearFiles) as $v){
      unlink($v);
    }
  }
}


/* Cache Callback
 * since 1.0 */
 
function cacheCallback($buffer) {
  if(!is_file(CACHFILE)) file_put_contents(CACHFILE,$buffer);
  return $buffer;
}


/* Make Vars
 * since 1.4 */

function makeVars($buffer) {
  global $content, $title, $url, $metak, $metad, $parent, $SITEURL, $SITENAME, $TEMPLATE, $date, $TRANS;
  
  // translations
  if($TRANS && getPageData('title')) { $title = getPageData('title'); }
  if($TRANS && getPageData('title',$parent)) { $parent = getPageData('title',$parent); }
  if($TRANS && getPageData('content')) { $content = getPageData('content'); }

  // title and slug
  $buffer = preg_replace('/\$title/', strip_decode($title), $buffer);
  $buffer = preg_replace('/\$cleanTitle/', strip_tags(strip_decode($title)), $buffer);
  $buffer = preg_replace('/\$slug/', $url, $buffer);
  
  // meta
  $buffer = preg_replace('/\$keywords/', encode_quotes(strip_decode($metak)), $buffer);
  $buffer = preg_replace('/\$description/', encode_quotes(strip_decode($metad)), $buffer);
  
  // parent and url
  $buffer = preg_replace('/\$parent/', $parent, $buffer);
  $buffer = preg_replace('/\$url/', find_url($url, $parent), $buffer);
  
  // theme path and site name
  $buffer = preg_replace('/\$theme/', trim($TEMPLATE), $buffer);
  $buffer = preg_replace('/\$siteName/', trim(stripslashes($SITENAME)), $buffer);
  
  // date($format)
  $date_p = '/(\$date\()([^\)]+)\)/';
  $is_date = preg_match_all($date_p, $buffer, $is_date_a);
  foreach ($is_date_a[2] as $is_date){
   $buffer = preg_replace($date_p, date($is_date, strtotime($date)), $buffer);
  }
  // date
  $buffer = preg_replace('/\$date/', date('j. M Y', strtotime($date)), $buffer);
  
  // excerpt($num)
  $excerpt_p = '/(\$excerpt\()([0-9)]+)\)/';
  $is_excerpt = preg_match_all($excerpt_p, $buffer, $is_excerpt_a);
  foreach ($is_excerpt_a[2] as $is_excerpt){
   $buffer = preg_replace($excerpt_p, trim(substr(strip_tags(strip_decode($content)), 0, $is_excerpt+2)), $buffer);
  }
  // excerpt
  $get_excerpt = trim(substr(strip_tags(strip_decode($content)), 0, 100+2));
  $buffer = preg_replace('/\$excerpt/', $get_excerpt, $buffer);
  
  return $buffer;
}


/* Post Process
 * since 1.2 */
 
function post_process($buffer) {
  global $configOA;
  
  // Check Isset
  if(!isset($configOA['protMail'])) $configOA['protMail'] = 0;
  if(!isset($configOA['minify'])) $configOA['minify'] = 0;
  if(!isset($configOA['cache'])) $configOA['cache'] = 0;
  
  // Replace ' />' with '>' for consistency
  $search = array ('/ \/>/', '/\/>/');
  $replace = array ('>', '>');
  $buffer = preg_replace($search, $replace, $buffer);
   
  // Protect Mail
  if ($configOA['protMail'] == 1 || $configOA['protMail'] == 2) $buffer = replaceMail($buffer);
  if ($configOA['protMail'] == 2) $buffer = replaceMailTo($buffer);
   
  // Vars
  $buffer = makeVars($buffer);
   
  // Minify
  if ($configOA['minify'] == 1) $buffer = minify($buffer);

  // Cache
  if ($configOA['cache'] == 1) $buffer = cacheCallback($buffer);
   
  // return buffer
  return $buffer;
}
?>
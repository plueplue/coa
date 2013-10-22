<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/*
Parse Coa files to object and attribute arrays
Initiate the rendering if conditions are fine
*/


/* wrapB and wrapA
 * since 1.0 */
 
function wrapB($x) {
  return trim(substr($x, 0, strpos($x, '|')));
}

function wrapA($x) {
  return trim(substr($x, strpos($x, '|')+1));
}


/* commaB and commaA
 * since 1.0 */
 
function commaB($x) {
  return trim(substr($x, 0, strpos($x, ',')));
}

function commaA($x) {
  return trim(substr($x, strpos($x, ',')+1));
}


/* explComma
 * since 1.4 */
 
function explComma($a) {
  return explode(',', preg_replace('/\s+/', '', $a));
}


/* Get Objects
 * since 1.0 */

function getObjects($path) {
  $file = file($path);
  $OBJ = array();
  
  foreach($file as $key => $line) {

    // define objects and comment
    $providedObj = preg_match('/^main|^meta|^css|^script|^text|^menu|^image|^place|^content|^config/i', trim($line));
    $comment = '/^#/';
    
    // check for objects
    if ($providedObj && !preg_match($comment, trim($line))) {
    
      // write object type to array
      $objType = trim($line);
      $objA = array();
      $objA['type'] = $objType;
          
      // get object length
      for ($i=$key; $i<$key+15; $i++) {  
        if (isset($file[$i]) && !trim($file[$i])) { $objLength = $i-$key; break; }
      }
        
      // write attributes and values to array
      for ($k=$key; $k<$key+$objLength; $k++) {
        if (isset($file[$k]) && trim($file[$k]) !== '' && !preg_match($comment, trim($file[$k]))) {  
          $attrLine = explode('=', trim($file[$k]), 2);
          if(isset($attrLine[1])) $objA[trim($attrLine[0])] = trim($attrLine[1]);  
        }
      }
      array_push($OBJ, $objA);
    }
  }
  return $OBJ;
}


/* Get Attributes
 * since 1.0 */

function getAttributes($OA,$curPid) {
  global $TRANS, $USR;

  // get 'wrap'
  if(isset($OA['wrap'])) {
    $OA['wrapB'] = wrapB($OA['wrap']);
    $OA['wrapA'] = wrapA($OA['wrap']);
  } 
    
  // get 'link' with target
  if ( isset($OA['link']) && $l = $OA['link']) {
    if (preg_match('/,/', $l)) { $OA['link'] = commaB($l); $OA['linkT'] = commaA($l); } 
    else { $OA['link'] = trim($l); $OA['linkT'] = 0;
    } 
  }
    
  // get 'list'
  if(isset($OA['list'])) $OA['listA'] = explComma($OA['list']);
    
  // get 'item'
  if(isset($OA['item'])) {
    $OA['itemB'] = wrapB($OA['item']);
    $OA['itemA'] = wrapA($OA['item']);
  }
  
  // get 'cur'
  if(isset($OA['cur'])) {  
    $OA['curB'] = wrapB($OA['cur']);
    $OA['curA'] = wrapA($OA['cur']);
  }
    
  // get 'act'
  if(isset($OA['act'])) {
    $OA['actB'] = wrapB($OA['act']);
    $OA['actA'] = wrapA($OA['act']);
  }
  
  // get 'size'
  if(isset($OA['size'])) {
    $OA['width'] = commaB($OA['size']);
    $OA['height'] = commaA($OA['size']);
  }

  // get prepared for 'css' and 'script' attributes
  $OA_ = $OA;
  $OA['all'] = array();
  $OA['screen'] = array();
  $OA['print'] = array();
  $OA['handheld'] = array();
  $OA['js'] = array();
  
  // get 'all', 'screen', 'print', 'handheld' and 'js'
  if (isset($OA_['all'])) $OA['all'][0] = $OA_['all'];
  if (isset($OA_['screen'])) $OA['screen'][0] = $OA_['screen'];
  if (isset($OA_['handheld'])) $OA['handheld'][0] = $OA_['handheld'];
  if (isset($OA_['print'])) $OA['print'][0] = $OA_['print'];
  if (isset($OA_['js'])) $OA['js'][0] = $OA_['js'];
  
  for ($k=1; $k<10; $k++) {
  
    // get 'all[1-9]'
    if (isset($OA['all'.$k])) { $OA['all'][$k] = $OA['all'.$k]; unset($OA['all'.$k]); }
    
    // get 'screen[1-9]'
    if (isset($OA['screen'.$k])) { $OA['screen'][$k] = $OA['screen'.$k]; unset($OA['screen'.$k]); }
    
    // get'handheld[1-9]
    if (isset($OA['handheld'.$k])) { $OA['handheld'][$k] = $OA['handheld'.$k]; unset($OA['handheld'.$k]); }
    
    // get 'print[1-9]'
    if (isset($OA['print'.$k])) { $OA['print'][$k] = $OA['print'.$k]; unset($OA['print'.$k]); }
    
    // get 'js[1-9]'
    if (isset($OA['js'.$k])) { $OA['js'][$k] = $OA['js'.$k]; unset($OA['js'.$k]); }
  }

  ///////////////////////////////
  // get conditions
  //////////////////////////////

  $ifSub = 0; $ifNoSub = 0; 
  $ifTrans = 0; $ifNoTrans = 0;
  $exclA = array('0','0');
  
  if(isset($OA['only'])) $onlyA = explComma($OA['only']);
  if(isset($OA['exclude'])) $exclA = explComma($OA['exclude']);

  if(isset($OA['if'])) {
    if ($OA['if'] == 'sub') $ifSub = 1;
    if ($OA['if'] == '!sub') $ifNoSub = 1;
    if ($OA['if'] == 'translation') $ifTrans = 1;
    if ($OA['if'] == '!translation') $ifNoTrans = 1;
  }
  
  ///////////////////////////////
  // check conditions
  //////////////////////////////
  
  // reset allTrue
  $OA['allTrue'] = 0;
  
  // 'only','exclude' and 'if' are fine with this object
  if (isset($OA['only']) && ($ifTrans && $TRANS || !$ifTrans) && ($ifNoTrans && !$TRANS || !$ifNoTrans)) {
    if (in_array($curPid, $onlyA) && !in_array($curPid, $exclA)) {
      if ($ifSub && return_parent() != '' || $ifNoSub && return_parent() == '') $OA['allTrue'] = 1;
      elseif (!$ifSub && !$ifNoSub) $OA['allTrue'] = 1;
    } 
  } elseif (!in_array($curPid, $exclA) && ($ifTrans && $TRANS || !$ifTrans) && ($ifNoTrans && !$TRANS || !$ifNoTrans)) {
    if ($ifSub && return_parent() != '' || $ifNoSub && return_parent() == '') $OA['allTrue'] = 1;
    elseif (!$ifSub && !$ifNoSub) $OA['allTrue'] = 1;
  }

  // 'if' logged in/out
  if( $OA['allTrue'] == 1 && isset($OA['if']) ) {
    if ($OA['if'] == 'login' && !$USR) $OA['allTrue'] = 0;
    elseif ($OA['if'] == '!login' && $USR) $OA['allTrue'] = 0;
  }
  
  // filter array
  $OA = array_filter($OA);
  
  // return object array
  return $OA;  
}
?>
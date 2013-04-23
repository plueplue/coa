<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/*
Coa Rendering Template
Initiate Constant and Page Objects
*/


/* Initiate Constant Objects
 * since 1.0 */

function makeConstant($configOA) {
  global $curPid;
  
  // get constant objects and lenght
  $OBJ = getObjects(CONSTANTTEMPLATE);
  $objLength = count($OBJ);
  
  // for each object
  for ($i=0; $i<$objLength; $i++) {
    
    // create object array
    $OA = array();
    $OA = $OBJ[$i];
    
    // get attributes
    $OA = getAttributes($OA,$curPid);

    // make constant objects
    if ($OA['allTrue'] == 1) {
      if ($OA['type'] == 'main') mainObj($OA,$configOA);
      if ($OA['type'] == 'meta') metaObj($OA);
      if ($OA['type'] == 'css') cssObj($OA);
      if ($OA['type'] == 'script') scriptObj($OA);
      if ($OA['type'] == 'text') textObj($OA);
      if ($OA['type'] == 'place') placeObj($OA);
    }  
  }
  
  // return header end
  headEnd($configOA);
}


/* Initiate Page Objects
 * since 1.0 */

function makePage($path, $configOA, $isPlace) {
  global $curPid, $TRANS;
  
  // get template objects and lenght
  $OBJ = getObjects($path);
  $objLength = count($OBJ);
  
  // for each object
  for ($i=0; $i<$objLength; $i++) {
    
    // create object array
    $OA = array();
    $OA = $OBJ[$i];
    
    // get attributes
    $OA = getAttributes($OA,$curPid);

    // make objects
    if (isset($OA['allTrue']) && $OA['allTrue'] == 1) {
      if ($OA['type'] == 'text') textObj($OA);
      if ($OA['type'] == 'menu') menuObj($OA);
      if ($OA['type'] == 'image') imageObj($OA);
      
      // content object
      if ($OA['type'] == 'content') {
        if(isset($OA['wrapB'])) echo $OA['wrapB'];
        
        if (!isset($OA['get']) || $OA['get'] == 'one') {
          $c1 = getPageData('add-content-1');
          $c2 = getPageData('add-content-2');
          $c3 = getPageData('add-content-3');
          
          // get content and translation
          echo '<div class="c1">';
          if ($TRANS) echo getPageData('content');
          else get_page_content();
          echo '</div>';
          
          // get additional content
          if( $c1 ) echo '<div class="c2">'.$c1.'</div>';
          if( $c2 ) echo '<div class="c3">'.$c2.'</div>';
          if( $c3 ) echo '<div class="c4">'.$c3.'</div>';
        }
        
        // get content of certain page
        elseif (substr($OA['get'],0,5) == 'page:') echo getContentOfPage(trim(substr($OA['get'],5)));
        
        // get component
        else get_component($OA['get']);
        
        if(isset($OA['wrapA'])) echo $OA['wrapA'];
        echo "\n\n"; 
      }
      
      if ($OA['type'] == 'place') placeObj($OA);
      if ($OA['type'] == 'script') scriptObj($OA);
    }
  }
  
  ///////////////////////////////
  // echo document end
  //////////////////////////////
  
  if ($isPlace == 0) {
  
    // wrap page
    if (isset($configOA['wrapPageB'])) echo "\n".$configOA['wrapPageA']."\n";
    else echo "\n";
    
    // GS footer
    get_footer();
    echo "\n";
    
    // mail protection
    if (isset($configOA['protMail']) && $configOA['protMail'] == 2) mailToJS();
    
    // remove body class 'no_js'
    if(isset($configOA['bodyTag'])) {
      if($configOA['bodyTag'] == 'basic' || $configOA['bodyTag'] == 'extended') {
        echo '<script>document.getElementsByTagName("body")[0].className='.
          'document.getElementsByTagName("body")[0].className.replace(/(?:^|\s)no_js(?!\S)/,\'\');</script>'."\n";
      }
    }

    // end tags
    echo '</body>'."\n".'</html>';
  }
}
?>
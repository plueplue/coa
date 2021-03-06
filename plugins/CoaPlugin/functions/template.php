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
    if (isset($OA['allTrue']) && $OA['allTrue'] == 1) {
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
          $c1L = getPageData('add-content-translation-1');
          $c2L = getPageData('add-content-translation-2');
          $c3L = getPageData('add-content-translation-3');
          
          // content wrap begin
          if( $c1 || $c2 || $c3) echo '<article class="c1 c_more">';
          else echo '<article class="c1">';
          
          // get content and translation
          if ($TRANS) {
            echo getPageData('content');
            echo '</article>';

            if( $c1L ) echo '<article class="c2">'.$c1L.'</article>';
            if( $c2L ) echo '<article class="c3">'.$c2L.'</article>';
            if( $c3L ) echo '<article class="c4">'.$c3L.'</article>';

          }
          else {
            get_page_content();
            echo '</article>';

            if( $c1 ) echo '<article class="c2">'.$c1.'</article>';
            if( $c2 ) echo '<article class="c3">'.$c2.'</article>';
            if( $c3 ) echo '<article class="c4">'.$c3.'</article>';
          }
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
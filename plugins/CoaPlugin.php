<?php
/*
Plugin Name: Coa Plugin
Version: 1.10 Beta
Author: Benjamin Scherer
Author URI: http://www.plue.me/
License: MIT License
*/


////////////////////
// Get Prepared
////////////////////

$thisfileCoa = basename(__FILE__, '.php');
$coa_settings_file = GSDATAOTHERPATH .'CoaSettings.xml';

register_plugin(
  $thisfileCoa,
  'Coa Plugin',
  '1.10 Beta',
  'Benjamin Scherer',
  'http://www.plue.me/',
  'Coa is a powerful framework to create lean and functional templates. It provides CoaScript and some useful out of the box features for fast and flexible development.',
  'plugins',
  'coa_settings'
);

// add in this plugin's language file
i18n_merge($thisfileCoa) || i18n_merge($thisfileCoa, 'en_US') || i18n_merge($thisfileCoa, 'de_DE');

// coa settings
if( file_exists($coa_settings_file) ) {
  $getData = getXML($coa_settings_file);
  $translation_off = $getData->translation_off;
  $fullscreen_off = $getData->fullscreen_off;
  $addcontent_off = $getData->addcontent_off;
  $styles_off = $getData->styles_off;
}
else {
	$translation_off = ''; 
	$fullscreen_off = ''; 
	$addcontent_off = '';
	$styles_off = '';
}


////////////
// Hooks
////////////

// settings
add_action('plugins-sidebar','createSideMenu',array($thisfileCoa, i18n_r($thisfileCoa.'/COA_SETTINGS')));
if( strstr($_SERVER["REQUEST_URI"], 'load.php?id=CoaPlugin') ) add_action('header','backend_css');

// styles
if( $styles_off!='on' ) add_action('header','be_styles');

// on edit Page
if( strstr($_SERVER["REQUEST_URI"], '/edit.php') ) {
  if( $translation_off!='on' ) add_action('pages-sidebar','translate_page_button');
  add_action('footer','edit_page_footer');
  add_action('header-body','backend_js');
  add_action('header', 'backend_css');
}
if( $translation_off!='on' ) {
  add_action('edit-extras', 'translate_page_extras');
  add_action('edit-content', 'translate_page_editor');
}
if( $addcontent_off!='on' ) add_action('edit-content', 'add_content_element');
add_action('changedata-save', 'save_data');

// on edit Theme
if( strstr($_SERVER["REQUEST_URI"], 'theme-edit.php') && $fullscreen_off!='on' ) {
  add_action('header-body','backend_js');
  add_action('header','backend_css');
}


////////////////////
// Settings
////////////////////

function coa_settings() {
	global $coa_settings_file, $thisfileCoa, $fullscreen_off, $translation_off, $addcontent_off, $styles_off;
	$ok = null;
	
	// save data
	if( isset($_POST['save']) ) {
		$xml = @new SimpleXMLElement('<item></item>');
		
		if( isset($_POST['translation_off']) && $translation_off = $_POST['translation_off'] ) $xml->addChild('translation_off', $translation_off);
		if( isset($_POST['fullscreen_off']) && $fullscreen_off = $_POST['fullscreen_off'] ) $xml->addChild('fullscreen_off', $fullscreen_off);
		if( isset($_POST['addcontent_off']) && $addcontent_off = $_POST['addcontent_off']) $xml->addChild('addcontent_off', $addcontent_off);
		if( isset($_POST['styles_off']) && $styles_off = $_POST['styles_off']) $xml->addChild('styles_off', $styles_off);
		
		if ( $xml->asXML($coa_settings_file) ) {
		  $data = getXML($coa_settings_file);
		  $translation_off = $data->translation_off;
		  $fullscreen_off = $data->fullscreen_off;
		  $addcontent_off = $data->addcontent_off;
		  $styles_off = $data->styles_off;
		  echo '<div class="updated"><p>'.i18n_r('CoaPlugin/COA_SETTINGS_UPDATED').'</p></div>';
		}
		else {
  		echo '<div class="error"><p>'.i18n_r('CoaPlugin/COA_ERROR').'</p></div>';
		}
	}
	
	if( $translation_off=='on' ) $translation_off = ' checked="checked"';
	if( $fullscreen_off=='on' ) $fullscreen_off = ' checked="checked"';
	if( $addcontent_off=='on' ) $addcontent_off = ' checked="checked"';
	if( $styles_off=='on' ) $styles_off = ' checked="checked"';

	echo '<h3>'.i18n_r('CoaPlugin/COA_SETTINGS').'</h3>
	  <form id="coa_settings" method="post" action="'.$_SERVER['REQUEST_URI'].'">

		<p><input name="translation_off" id="translation_off" class="text"'.$translation_off.' type="checkbox" />
		<label for="translation_off">'.i18n_r('CoaPlugin/COA_TRANSLATION_OFF').'</label></p>
		
		<p><input name="fullscreen_off" id="fullscreen_off" class="text"'.$fullscreen_off.' type="checkbox" />
		<label for="fullscreen_off">'.i18n_r('CoaPlugin/COA_FULLSCREEN_OFF').'</label></p>
		
		<p><input name="addcontent_off" id="addcontent_off" class="text"'.$addcontent_off.' type="checkbox" />
		<label for="addcontent_off">'.i18n_r('CoaPlugin/COA_ADDCONTENT_OFF').'</label></p>

		<p><input name="styles_off" id="styles_off" class="text"'.$styles_off.' type="checkbox" />
		<label for="styles_off">'.i18n_r('CoaPlugin/COA_STYLES_OFF').'</label></p>
		
		<p><input type="submit" id="save" class="submit" value="'.i18n_r('CoaPlugin/COA_SAVE').'" name="save" /></p></form>';
}


///////////////////////////
// Translate Page Button
///////////////////////////

function translate_page_button() {
  echo '<li id="sb_CoaPlugin"><a href="#">'.i18n_r('CoaPlugin/COA_EDIT_TRANSLATION').'</a></li>';
}


///////////////////////////
// Translate Page Extras
///////////////////////////

function translate_page_extras() {
  $id = isset($_GET['id']) ? $_GET['id'] : null;
  $path = GSDATAPAGESPATH;
  $file = $id .'.xml';
  
  $translation_title = '';
  $translation_menu = '';
  
  $data = getXML($path . $file);
  
  if(isset($data->translationTitle)) $translation_title = stripslashes($data->translationTitle);
  else $translation_title = '';
  
  if(isset($data->translationMenu)) $translation_menu = stripslashes($data->translationMenu);
  else $translation_menu = '';

  echo '<div id="extra_CoaPlugin">
    <p id="translation_title">
    <label for="post-title-translation">'.i18n_r('CoaPlugin/COA_TITLE_TRANSLATION').'</label>
    <input class="text short" id="post-title-translation" name="post-title-translation" type="text" value="'.$translation_title.'" />
    </p>
    <p id="translation_menu">
    <label for="post-menu-translation">'.i18n_r('CoaPlugin/COA_MENU_TRANSLATION').'</label>
    <input class="text short" id="post-menu-translation" name="post-menu-translation" type="text" value="'.$translation_menu.'" />
    </p>
    <div class="clear"></div>
    </div>';
}


///////////////////////////
// Translate Page Editor
///////////////////////////

function translate_page_editor() {
  $id = isset($_GET['id']) ? $_GET['id'] : null;
  $path = GSDATAPAGESPATH;
  $file = $id .'.xml';
  $translation = '';
  
  $data = getXML($path . $file);
  if(isset($data->translationContent)) $translation = stripslashes($data->translationContent);
  else $translation = '';
  
  echo '<textarea id="post-content-translation" name="post-content-translation">'. $translation .'</textarea>';
}


///////////////
// Save Data
///////////////

function save_data() {
  global $note, $xml;
  
  // save title
  if(isset($_POST['post-title-translation']) && $_POST['post-title-translation'] != '') {
    $translation_title = safe_slash_html($_POST['post-title-translation']);
    $note = $xml->addChild('translationTitle');
    $note->addCData($translation_title);
  }

  // save menu title
  if(isset($_POST['post-menu-translation']) && $_POST['post-menu-translation'] != '') {
    $translation_menu = safe_slash_html($_POST['post-menu-translation']);
    $note = $xml->addChild('translationMenu');
    $note->addCData($translation_menu);
  }
  
  // save content
  if(isset($_POST['post-content-translation']) && $_POST['post-content-translation'] != '') {
    $translation = safe_slash_html($_POST['post-content-translation']);
    $note = $xml->addChild('translationContent');
    $note->addCData($translation);
  }
  
  // save add content editors and translations
  for( $i=0; $i<4; ++$i ) {
  
    if(isset($_POST['add-content-'.$i]) && $_POST['add-content-'.$i] != '') {
      $content = safe_slash_html($_POST['add-content-'.$i]);
      $note = $xml->addChild('addContent'.$i);
      $note->addCData($content);
    }
    if(isset($_POST['add-content-translation-'.$i]) && $_POST['add-content-translation-'.$i] != '') {
      $content = safe_slash_html($_POST['add-content-translation-'.$i]);
      $note = $xml->addChild('addContent'.$i.'L');
      $note->addCData($content);
    }
    
  }

}


///////////////////////
// Edit Page Footer
///////////////////////

function edit_page_footer() {
  global $SITEURL, $HTMLEDITOR, $TEMPLATE;
  
  // get editor constants
  if (defined('GSEDITORHEIGHT')) { $EDHEIGHT = GSEDITORHEIGHT .'px'; } else {  $EDHEIGHT = '500px'; }
  if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else {  $EDLANG = i18n_r('CKEDITOR_LANG'); }
  if (defined('GSEDITORTOOL')) { $EDTOOL = GSEDITORTOOL; } else {  $EDTOOL = 'basic'; }
  if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {  $EDOPTIONS = ''; }
  
  // define toolbar
  if ($EDTOOL == 'advanced') {
   $toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter'".
     ",'JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat',".
     "'Source'], '/', ['Styles','Format','Font','FontSize']";
  } elseif ($EDTOOL == 'basic') {
   $toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter',".
     "'JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
  } else {
   $toolbar = GSEDITORTOOL;
  }
  
  // prepare editor settings
  $editorSetup = 'skin : \'getsimple\',forcePasteAsPlainText : true,language : \''.$EDLANG.'\',defaultLanguage : \'en\',
    entities : false,uiColor: \'#FFFFFF\',height: \''.$EDHEIGHT.'\',baseHref: \''.$SITEURL.'\',
    toolbar: ['.$toolbar.']'.$EDOPTIONS.',tabSpaces:10,filebrowserBrowseUrl : \'filebrowser.php?type=all\',
    filebrowserImageBrowseUrl : \'filebrowser.php?type=images\',filebrowserWindowWidth : \'730\',filebrowserWindowHeight : \'500\'});';
    
  // enable translation editor
  if ($HTMLEDITOR != '') {
    echo '<script type="text/javascript">
      if( document.getElementById("post-content-translation") ) {
        var editor0 = CKEDITOR.replace( \'post-content-translation\', {'
          .$editorSetup.' CKEDITOR.instances["post-content-translation"].on("instanceReady", InstanceReadyEvent);
      } </script>';
    
    // enable 3 add content editors
    for($i=1;$i<4;++$i) {
      echo '<script type="text/javascript">
        if( document.getElementById("add-content-'.$i.'") ) {
          var editor'.$i.' = CKEDITOR.replace( \'add-content-'.$i.'\', {'
            .$editorSetup.'CKEDITOR.instances["add-content-'.$i.'"].on("instanceReady", InstanceReadyEvent);
        } </script>
        
        <script type="text/javascript">
        if( document.getElementById("add-content-translation-'.$i.'") ) {
          var editor'.$i.' = CKEDITOR.replace( \'add-content-translation-'.$i.'\', {'
            .$editorSetup.' CKEDITOR.instances["add-content-translation-'.$i.'"].on("instanceReady", InstanceReadyEvent);
        } </script>';
    }
    
    // add link type "local page" to new editors
    echo '<script type="text/javascript">'."
    	// modify existing Link dialog
    	CKEDITOR.on( 'dialogDefinition', function( ev )	{
    	
    	  // exclude default editor
    		if ((ev.editor == editor) || (ev.data.name != 'link')) return;
    
    		// Overrides definition.
    		var definition = ev.data.definition;
    		definition.onFocus = CKEDITOR.tools.override(definition.onFocus, function(original) {
    			return function() {
    				original.call(this);
    					if (this.getValueOf('info', 'linkType') == 'localPage') {
    						this.getContentElement('info', 'localPage_path').select();
    					}
    			};
    		});
    
    		// Overrides linkType definition.
    		var infoTab = definition.getContents('info');
    		var content = getById(infoTab.elements, 'linkType');
    
    		content.items.unshift(['Link to local page', 'localPage']);
    		content['default'] = 'localPage';
    		infoTab.elements.push({
    			type: 'vbox',
    			id: 'localPageOptions',
    			children: [{
    				type: 'select',
    				id: 'localPage_path',
    				label: 'Select page:',
    				required: true,
    				items: " . list_pages_json() . ",
    				setup: function(data) {
    					if ( data.localPage )
    						this.setValue( data.localPage );
    				}
    			}]
    		});
    		content.onChange = CKEDITOR.tools.override(content.onChange, function(original) {
    			return function() {
    				original.call(this);
    				var dialog = this.getDialog();
    				var element = dialog.getContentElement('info', 'localPageOptions').getElement().getParent().getParent();
    				if (this.getValue() == 'localPage') {
    					element.show();
    					if (editor.config.linkShowTargetTab) {
    						dialog.showPage('target');
    					}
    					var uploadTab = dialog.definition.getContents('upload');
    					if (uploadTab && !uploadTab.hidden) {
    						dialog.hidePage('upload');
    					}
    				}
    				else {
    					element.hide();
    				}
    			};
    		});
    		content.setup = function(data) {
    			if (!data.type || (data.type == 'url') && !data.url) {
    				data.type = 'localPage';
    			}
    			else if (data.url && !data.url.protocol && data.url.url) {
    				if (path) {
    					data.type = 'localPage';
    					data.localPage_path = path;
    					delete data.url;
    				}
    			}
    			this.setValue(data.type);
    		};
    		content.commit = function(data) {
    			data.type = this.getValue();
    			if (data.type == 'localPage') {
    				data.type = 'url';
    				var dialog = this.getDialog();
    				dialog.setValueOf('info', 'protocol', '');
    				dialog.setValueOf('info', 'url', dialog.getValueOf('info', 'localPage_path'));
    			}
    		};
    	});
    ".'</script>';
  }
}


////////////////////////
// Add Content Element
////////////////////////

function add_content_element() {
  if(isset($_GET['id'])) $url = 'edit.php?id='.$_GET['id'];
  
  $label = i18n_r('CoaPlugin/COA_ADD_CONTENT');
  $addEditor1 = get_content_editor(1);
  $addEditor2 = get_content_editor(2);
  $addEditor3 = get_content_editor(3);
  
  // arrange editors and add button
  if( isset($_GET['addcontent']) ) {
    for( $i=1; $i<=$_GET['addcontent']; $i++ ) {
      echo get_content_editor($i);
    }
    if( $_GET['addcontent']<3 ) 
      echo '<a class="add_content" href="'.$url.'&addcontent='.$i.'">'.$label.'</a>'; 
  }
  else {
    for( $i=1; $i<=3; $i++ ) {
      if( $editorI = get_content_editor($i) ) {
        echo $editorI;
        $add_link = '<a class="add_content" href="'.$url.'&addcontent='.($i+1).'">'.$label.'</a>';
        if( $i>2 ) $add_link = '';
      }
    }
    
    // exceptions to show add button
    if(isset($url)) {
      if( $addEditor3 && !$addEditor1 || $addEditor1 && $addEditor3 && !$addEditor2) 
        $add_link = '<a class="add_content" href="'.$url.'&addcontent=3">'.$label.'</a>';
  
      if( $addEditor2 && !$addEditor3 && !$addEditor1 ) 
        $add_link = '<a class="add_content" href="'.$url.'&addcontent=2">'.$label.'</a>';
        
      if( !$addEditor1 && !$addEditor3 && !$addEditor2 )
        $add_link = '<a class="add_content" href="'.$url.'&addcontent=1">'.$label.'</a>'; 
        
      echo $add_link;
    }
  }
}


///////////////////////////
// Get Content Editor
///////////////////////////

function get_content_editor($num) {
  $id = isset($_GET['id']) ? $_GET['id'] : null;
  $path = GSDATAPAGESPATH;
  
  $data = getXML($path . $id .'.xml');
  $field = 'addContent'.$num;
  $fieldL = $field.'L';
  
  if( $num<4 ) {
    if( isset($data->$field) || isset($_GET['addcontent']) ) {
     return '<textarea class="add-content" id="add-content-'.$num.'" name="add-content-'.$num.'">'. stripslashes($data->$field) .'</textarea>
       <textarea class="add-content-translation" id="add-content-translation-'.$num.'" name="add-content-translation-'.$num.'">'. stripslashes($data->$fieldL) .'</textarea>'; 
    }
  }
}


////////////////////////
// Backend JS and CSS
////////////////////////

function backend_js() {
  echo '<script type="text/javascript" src="../plugins/CoaPlugin/res/coa.backend.js"></script>';
}

function backend_css() {
  echo '<link rel="stylesheet" href="../plugins/CoaPlugin/res/coa_backend.css"></link>';
}

function be_styles() {
  echo '<link rel="stylesheet" href="../plugins/CoaPlugin/res/admin_styles.css"></link>';
}
?>
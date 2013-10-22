/*
Coa Backend JS
http://coa.plue.me
*/

jQuery(function($) {

/////////////////////////////
// Edit Page Translation
/////////////////////////////

if($('#sb_CoaPlugin').length) {

	// adjust environment
	$('#post-content').parent('p').css('margin','0');
	$('#extra_CoaPlugin').hide();

	// click the translation button
	$('#sb_CoaPlugin a').click(function() {
    
		// show translated page
		if(!$('#sb_CoaPlugin').is('.act_content') && !$('#sb_CoaPlugin').is('.animated')) {
			
			// arrange extras
			$('.rightopt, .leftopt').slideUp();
			$('#extra_CoaPlugin').slideDown();
			if(!$('#metadata_toggle').is('.current')) {
				$('.rightopt, .leftopt').hide();
				$('#extra_CoaPlugin').show();
			}
			
			// get editor wrap height
			var editH = $('#cke_post-content').height();
			
			// set animated class
			$('#sb_CoaPlugin').addClass('animated');
			window.setTimeout(function() { 
			  $('#sb_CoaPlugin').removeClass('animated'); 
			}, 1000);
		
			// adjust nav item classes
			$('#sb_CoaPlugin').addClass('act_content');
		
			// animate editor fields
			$('#cke_post-content-translation').animate({'height':editH}, 500).css('border','1px solid #999');
			$('#cke_post-content').animate({'height':'0'}, 500).animate({'border':'0'}, 500);
			$('#cke_add-content-translation-1,#cke_add-content-translation-2,#cke_add-content-translation-3').animate({'height':editH}, 500).css('border','1px solid #999');
			$('#cke_add-content-1,#cke_add-content-2,#cke_add-content-3').animate({'height':'0'}, 500).animate({'border':'0'}, 500);
		}
	
		// show default page
		else if(!$('#sb_CoaPlugin').is('.animated')) {
			
			// arrange extras
			$('.rightopt, .leftopt').slideDown();
			$('#extra_CoaPlugin').slideUp();
			if(!$('#metadata_toggle').is('.current')) {
				$('.rightopt, .leftopt').show();
				$('#extra_CoaPlugin').hide();
			}
			
			// get editor wrap height
			var editH = $('#cke_post-content-translation').height();
			
			// set animated class
			$('#sb_CoaPlugin').addClass('animated');
			window.setTimeout(function() { $('#sb_CoaPlugin').removeClass('animated'); }, 1000);
		
			// adjust nav item classes
			$('#sb_CoaPlugin').removeClass('act_content');
		
			// animate editor fields
			$('#cke_post-content-translation').animate({'height':'0'}).animate({'border':'0'}, 500);
			$('#cke_post-content').css('border','1px solid #999').animate({'height': editH});
			$('#cke_add-content-translation-1,#cke_add-content-translation-2,#cke_add-content-translation-3').animate({'height':'0'}).animate({'border':'0'}, 500);
			$('#cke_add-content-1,#cke_add-content-2,#cke_add-content-3').css('border','1px solid #999').animate({'height': editH});
		}
		return false;
	});
}

/////////////////////////////
// Fullscreen Edit Mode
/////////////////////////////

if( $('body#theme-edit').length && !$('.edit-nav').length ) {

  // language
  if( $('html').attr('lang') == 'de' ) {
    var label_fullscreen = '<em>V</em>ollbild';
    var label_close = '<em>X</em>';
  }
  else {
    var label_fullscreen = '<em>F</em>ullscreen';
    var label_close = '<em>X</em>';
  }

  // insert edit-nav and button
  var editNav = '<div class="edit-nav"><a href="#" id="edit_mode">'+label_fullscreen+'</a><div class="clear"></div></div>';
  $('.main > h3:first-child').addClass('floated').after(editNav);

  // arrange edit mode on click
  $('a#edit_mode').click(function() {
    
    // unhover
    $(this).addClass('unhover').mouseover(function(){
      $(this).removeClass('unhover');
    });
     
    // arrange cookie and button value
    if( !$.cookie('isfullscreeneditor') ) {
      $.cookie('isfullscreeneditor', '1');
      $('a#edit_mode').html(label_close);
    } else {
      $.cookie('isfullscreeneditor', null);
      $('a#edit_mode').html(label_fullscreen);
    }
    
    // toggle classes and visibility
    $('.bodycontent').toggleClass('edit_mode_act');
    $('.CodeMirror-scroll, .CodeMirror').toggleClass('code_act');
    $('#footer').fadeToggle(0);
    
    // dynamic editor height
    editorH();
    return false;
  });
  
  // editor height on resize
  $(window).resize(function() {
    editorH();
  });
 
  // editor height on load 
  $(window).load(function() {
    editorH();
  });
  
  // arrange edit mode on load
  if( $.cookie('isfullscreeneditor') == '1' ) {
    $('a#edit_mode').html(label_close);
    $('.bodycontent').addClass('edit_mode_act');
    $('.CodeMirror-scroll, .CodeMirror').addClass('code_act');
    $('#footer').hide();
    editorH();
  }

}

});

/////////////////////////////
// Dynamic Editor Height
/////////////////////////////

function editorH() {
  var formH = $('.main > form').eq(0).height();
  var dynamicH = window.innerHeight - 250 - formH;
  
  if(dynamicH < 200) dynamicH = 200;
  $('.CodeMirror-scroll, .CodeMirror').height(dynamicH);
}


/*!
 * jQuery Cookie Plugin v1.2
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function($,document,undefined){var pluses=/\+/g;function raw(s){return s}function decoded(s){return decodeURIComponent(s.replace(pluses,' '))}var config=$.cookie=function(key,value,options){if(value!==undefined){options=$.extend({},config.defaults,options);if(value===null){options.expires=-1}if(typeof options.expires==='number'){var days=options.expires,t=options.expires=new Date();t.setDate(t.getDate()+days)}value=config.json?JSON.stringify(value):String(value);return(document.cookie=[encodeURIComponent(key),'=',config.raw?value:encodeURIComponent(value),options.expires?'; expires='+options.expires.toUTCString():'',options.path?'; path='+options.path:'',options.domain?'; domain='+options.domain:'',options.secure?'; secure':''].join(''))}var decode=config.raw?raw:decoded;var cookies=document.cookie.split('; ');for(var i=0,parts;(parts=cookies[i]&&cookies[i].split('='));i++){if(decode(parts.shift())===key){var cookie=decode(parts.join('='));return config.json?JSON.parse(cookie):cookie}}return null};config.defaults={};$.removeCookie=function(key,options){if($.cookie(key,options)!==null){$.cookie(key,null,options);return true}return false}})(jQuery,document);
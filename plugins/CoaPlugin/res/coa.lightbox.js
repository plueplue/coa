/*
Coa Lightbox JS
http://coa.plue.me
*/


jQuery(function($) {


//////////////////
// Get Prepared
//////////////////

// fadetime
var fade_lb = 460;

// include css
$('head').append('<link rel="stylesheet" href="plugins/CoaPlugin/res/coa_lightbox.css">');

// append divs
$('body').append('<div id="lb_full"><div></div></div><div id="lb_preload"></div>');

// preload images
$('.coa_lb a').each(function() {
    $('#lb_preload').append('<img src="' + $(this).attr('href') + '">');
});

/////////////////////////////////////
// Click Thumbnail
/////////////////////////////////////

$('.coa_lb a img').click(function() {
    
    // vars
    var desc = $(this).attr('alt');
    if(!desc) desc = '';
    var img = '<img src="' + $(this).parent().attr('href') + '" alt="' + desc + '">';
    
    // append big image and caption
    $('#lb_full > div').append('<figure>' + img + '<figcaption>' + desc + '</figcaption></figure>');

    // append and activate lightbox nav
    $('#lb_full > div').append('<div id="lb_nav"><span class="prev">&lt;</span><span class="next">&gt;</span><span class="close">x</span></div>');
    activateNav();
    
    // fade in lightbox wrap
    $('#lb_full').fadeIn(fade_lb);

    // when image loaded
    $('#lb_full img').load(function() {
        
        // show figure and nav
        $('#lb_full figure, #lb_full figcaption, #lb_nav').animate({opacity: 1}, fade_lb);
        
        // check nav
        checkNextPrev();
    });

    // add close events
    $('#lb_full .close, #lb_full figure, #lb_full').click(function() {
        $('#lb_full').fadeOut(fade_lb);
        window.setTimeout(function() { $('#lb_full > div').html('') }, fade_lb);
    });
    
    // stop propagation
    $('#lb_full img, #lb_full figcaption, #lb_full #lb_nav').click(function(e) {
         e.stopPropagation();
    });
    
    return false;
});


// jQuery function end
});



/////////////////////////////////////
// Activate Navigation
/////////////////////////////////////

function activateNav() {
    $('#lb_full .next, #lb_full .prev').click(function() {
        
        // vars
        var $cur_a = $('.coa_lb').find('a[href="' + $(this).parent().parent().find('img').attr('src') + '"]');
        var prev_desc = $cur_a.prev().find('img').attr('alt');
        var next_desc = $cur_a.next().find('img').attr('alt');
        if(!prev_desc) prev_desc = '';
        if(!next_desc) next_desc = '';
        
        // change prev img and description
        if( $(this).attr('class') == 'prev' ) {
            $('#lb_full img').attr('src', $cur_a.prev().attr('href') );
            $('#lb_full').find('figcaption').html(prev_desc);
        }
        
        // change next img and description
        else if( $(this).attr('class') == 'next' ) {
            $('#lb_full img').attr('src', $cur_a.next().attr('href') );
            $('#lb_full').find('figcaption').html(next_desc);
        }

    });
}


/////////////////////////////////////
// Check Next Prev
/////////////////////////////////////

function checkNextPrev() {

    // vars
    var $cur_a = $('.coa_lb').find('a[href="' + $('#lb_full').find('img').attr('src') + '"]');
    var prev_img = $cur_a.prev().attr('href');
    var next_img = $cur_a.next().attr('href');

    // if prev img doesn't exist
    if (!prev_img) {
        $('#lb_full .prev').hide().after('<span class="passive p">&lt;</span>');
    } else {
        $('#lb_full .prev').show();
        $('#lb_full .passive.p').remove();
    }

    // if next img doesn't exist
    if (!next_img) {
        $('#lb_full .next').hide().after('<span class="passive n">&gt;</span>');
    } else {
        $('#lb_full .next').show();
        $('#lb_full .passive.n').remove();
    }
}
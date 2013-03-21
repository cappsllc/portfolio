// initialise plugins
jQuery(document).ready(function() {
	
	
	jQuery("ul.sf-menu").superfish({ animation: {height:'show'}, delay: 1200 }); 
	
	jQuery('#slider').nivoSlider({ effect:'sliceUpRight', slices:30, pauseTime:5000, pauseOnHover:false });
	
	jQuery('#works_list').carouFredSel({ auto: false, prev: "#prev1", next: "#next1", items : 3 });
	
	
	jQuery("nav ul .search_icon").hover(function() {
		jQuery(this).find("div").stop()
		.animate({top: "37", opacity:0}, "fast")
		jQuery(this).find("div").stop()
		.css("display","block")
		.css("top","37px")
		.animate({top: "37", opacity:1}, "fast")

	}, function() {
		
		jQuery(this).find("div").stop()
		.animate({top: "37", opacity: 0}, "fast")
		.css("display","none")
		
	});
	
	/*fanybox*/
	if(jQuery.fancybox){
		   jQuery("a[rel=image_group]").fancybox({
				'transitionIn'		: 'elastic',
				'transitionOut'		: 'elastic',
				'titlePosition' 	: 'over',
				'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
					return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
				}
			});
	}
	
	
	/*ajax form*/
	jQuery("#ajax-contact-form").submit(function(){

	var str = jQuery(this).serialize();

   jQuery.ajax({
   type: "POST",
   url: "contact.php",
   data: str,
   success: function(msg){
    
	jQuery("#note").ajaxComplete(function(event, request, settings){
	
	if(msg == 'OK') // Message Sent? Show the 'Thank You' message and hide the form
	{
	result = '<div class="notification_ok">Your message has been sent. Thank you!</div>';
	jQuery("#fields").hide();
	}
	else
	{
	result = msg;
	}
	
	jQuery(this).html(result);
	
	});
	
	}
	
	 });
	
	return false;

	});
		
		if (jQuery().slides) {
		/*slides*/
		jQuery("#slides").slides({
		preload: true,
		effect: 'fade',
		fadeSpeed: 250,
		play: 3000,
		crossfade: true,
		generatePagination: true,
		autoHeight: true
		});
		
		jQuery("#slides").hover( function() {
			jQuery('.slides-nav').fadeIn(200);
		}, function () {
			jQuery('.slides-nav').fadeOut(200);
		});
		
		}
	
		/* image fade when hover */
		jQuery('#list li a img').animate({'opacity' : 1}).hover(function() {
			jQuery(this).animate({'opacity' : .3});
		}, function() {
			jQuery(this).animate({'opacity' : 1});
		});
		
		jQuery('.preview img').animate({'opacity' : 1}).hover(function() {
			jQuery(this).animate({'opacity' : .3});
		}, function() {
			jQuery(this).animate({'opacity' : 1});
		});
		
		jQuery('.fade img').animate({'opacity' : 1}).hover(function() {
			jQuery(this).animate({'opacity' : .5});
		}, function() {
			jQuery(this).animate({'opacity' : 1});
		});
		
		jQuery('.social a img').animate({'opacity' : .5}).hover(function() {
			jQuery(this).animate({'opacity' : 1});
		}, function() {
			jQuery(this).animate({'opacity' : .5});
		});

	//When page loads...
	jQuery(".tab_content").hide(); //Hide all content
	jQuery("ul.tabs li:first").addClass("active").show(); //Activate first tab
	jQuery(".tab_content:first").show(); //Show first tab content
	//On Click Event
	jQuery("ul.tabs li").click(function() {
		jQuery("ul.tabs li").removeClass("active"); //Remove any "active" class
		jQuery(this).addClass("active"); //Add "active" class to selected tab
		jQuery(".tab_content").hide(); //Hide all tab content
		var activeTab = jQuery(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		jQuery(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
	
	
	jQuery(".tab2_content").hide(); //Hide all content
	jQuery("ul.tabs2 li:first").addClass("active").show(); //Activate first tab
	jQuery(".tab2_content:first").show(); //Show first tab content
	//On Click Event
	jQuery("ul.tabs2 li").click(function() {
		jQuery("ul.tabs2 li").removeClass("active"); //Remove any "active" class
		jQuery(this).addClass("active"); //Add "active" class to selected tab
		jQuery(".tab2_content").hide(); //Hide all tab content
		var activeTab = jQuery(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		jQuery(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
	
	/*quicksand -----------------------------------------------------------------*/
	var read_button = function(class_names) {
    var r = {
      selected: false,
      type: 0
    };
    for (var i=0; i < class_names.length; i++) {
      if (class_names[i].indexOf('selected-') == 0) {
        r.selected = true;
      }
      if (class_names[i].indexOf('segment-') == 0) {
        r.segment = class_names[i].split('-')[1];
      }
    };
    return r;
  };
  
  var determine_sort = function($buttons) {
    var $selected = $buttons.parent().filter('[class*="selected-"]');
    return $selected.find('a').attr('data-value');
  };
  
  var determine_kind = function($buttons) {
    var $selected = $buttons.parent().filter('[class*="selected-"]');
    return $selected.find('a').attr('data-value');
  };
  
  var $preferences = {
    duration: 800,
    easing: 'easeInOutQuad',
    adjustHeight: false
  };
  
  var $list = $('#list');
  var $data = $list.clone();
  
  var $controls = $('ul.splitter ');
  
  $controls.each(function(i) {
    
    var $control = $(this);
    var $buttons = $control.find('a');
    
    $buttons.bind('click', function(e) {
      
      var $button = $(this);
      var $button_container = $button.parent();
      var button_properties = read_button($button_container.attr('class').split(' '));      
      var selected = button_properties.selected;
      var button_segment = button_properties.segment;

      if (!selected) {

        $buttons.parent().removeClass('selected-0').removeClass('selected-1').removeClass('selected-2').removeClass('selected-3').removeClass('selected-4');
        $button_container.addClass('selected-' + button_segment);
        
        var sorting_type = determine_sort($controls.eq(1).find('a'));
        var sorting_kind = determine_kind($controls.eq(0).find('a'));
        
        if (sorting_kind == 'all') {
          var $filtered_data = $data.find('li');
        } else {
          var $filtered_data = $data.find('li.' + sorting_kind);
        }
        
        if (sorting_type == 'size') {
          var $sorted_data = $filtered_data.sorted({
            by: function(v) {
              return parseFloat($(v).find('span').text());
            }
          });
        } else {
          var $sorted_data = $filtered_data.sorted({
            by: function(v) {
              return $(v).find('strong').text().toLowerCase();
            }
          });
        }
        
        $list.quicksand($sorted_data, $preferences, 
			function(){
			//end callback
			imageHoverFade();
			setLightbox();
			
			});
        
      }
      
      
    });
    
  }); 

  var high_performance = true;  
  var $performance_container = $('#performance-toggle');
  var $original_html = $performance_container.html();
  
  $performance_container.find('a').live('click', function(e) {
    if (high_performance) {
      $preferences.useScaling = false;
      $performance_container.html('CSS3 scaling turned off. Try the demo again. <a href="#toggle">Reverse</a>.');
      high_performance = false;
    } else {
      $preferences.useScaling = true;
      $performance_container.html($original_html);
      high_performance = true;
    }
    e.preventDefault();
  });
  
  /*end quicksand*/
  
   var setLightbox = function() {
           // do some stuff here
		  
		   if(jQuery.fancybox){
		   jQuery("a[rel=image_group]").fancybox({
				'transitionIn'		: 'elastic',
				'transitionOut'		: 'elastic',
				'titlePosition' 	: 'over',
				'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
					return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
				}
			});
		   }
		   
   }; // end setLightbox
   var imageHoverFade = function() {
           // do some stuff here
		jQuery('#list li a img').animate({'opacity' : 1}).hover(function() {
		$(this).animate({'opacity' : .2});
		}, function() {
			jQuery(this).animate({'opacity' : 1});
		});
	
   };// end imageHoverFade
	
});

(function($) {
	$.fn.sorted = function(customOptions) {
		var options = {
			reversed: false,
			by: function(a) {
				return a.text();
			}
		};
		$.extend(options, customOptions);
	
		$data = $(this);
		arr = $data.get();
		arr.sort(function(a, b) {
			
		   	var valA = options.by($(a));
		   	var valB = options.by($(b));
			if (options.reversed) {
				return (valA < valB) ? 1 : (valA > valB) ? -1 : 0;				
			} else {		
				return (valA < valB) ? -1 : (valA > valB) ? 1 : 0;	
			}
		});
		return $(arr);
	};

})(jQuery);


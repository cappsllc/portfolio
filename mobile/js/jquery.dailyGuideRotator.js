// JavaScript Document
// women rotator 
function womenRotator() {
	//Set the opacity of all images to 0
	$('div.women_rotator ul li').css({opacity: 0.0});
	
	//Get the first image and display it (gets set to full opacity)
	$('div.women_rotator ul li:first').css({opacity: 1.0});
		
	//Call the rotator function to run the slideshow, 6000 = change to next image after 6 seconds
	setInterval('womenrotate()',4000);
	
}

function womenrotate() {	
	//Get the first image
	var current = ($('div.women_rotator ul li.show1')?  $('div.women_rotator ul li.show1') : $('div.women_rotator ul li:first'));

	//Get next image, when it reaches the end, womenrotate it back to the first image
	var next = ((current.next().length) ? ((current.next().hasClass('show1')) ? $('div.women_rotator ul li:first') :current.next()) : $('div.women_rotator ul li:first'));
	
	//Set the fade in effect for the next image, the show class has higher z-index
	next.css({opacity: 0.0})
	.addClass('show1')
	.animate({opacity: 1.0}, 1000);

	//Hide the current image
	current.animate({opacity: 0.0}, 1000)
	.removeClass('show1');
	
};

$(document).ready(function() {		
	//Load the slideshow
	womenRotator();
});










// men rotator
function menRotator() {
	//Set the opacity of all images to 0
	$('div.men_rotator ul li').css({opacity: 0.0});
	
	//Get the first image and display it (gets set to full opacity)
	$('div.men_rotator ul li:first').css({opacity: 1.0});
		
	//Call the rotator function to run the slideshow, 6000 = change to next image after 6 seconds
	setInterval('menrotate()',4000);
	
}

function menrotate() {	
	//Get the first image
	var current = ($('div.men_rotator ul li.show2')?  $('div.men_rotator ul li.show2') : $('div.men_rotator ul li:first'));

	//Get next image, when it reaches the end, menrotate it back to the first image
	var next = ((current.next().length) ? ((current.next().hasClass('show2')) ? $('div.men_rotator ul li:first') :current.next()) : $('div.men_rotator ul li:first'));
	
	//Set the fade in effect for the next image, the show class has higher z-index
	next.css({opacity: 0.0})
	.addClass('show2')
	.animate({opacity: 1.0}, 1000);

	//Hide the current image
	current.animate({opacity: 0.0}, 1000)
	.removeClass('show2');
	
};

$(document).ready(function() {		
	//Load the slideshow
	menRotator();
});



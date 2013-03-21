/**********************************
 Free code from dyn-web.com 
***********************************/

// arguments: iframe id, height  (e.g., .5 for height="50%")
function setIframeHeight(id, h) {
	if ( document.getElementById ) {
		var theIframe = document.getElementById(id);
		if (theIframe) {
			dw_Viewport.getWinHeight();
			theIframe.style.height = Math.round( h * dw_Viewport.height ) + "px";
			theIframe.style.marginTop = "0px";
		}
	}
}

// for sizing and positioning the iframe in the window
// pass iframe id and height (e.g., .5 for height="50%")
// .5 for height="50%"
window.onload = function() { setIframeHeight('tutorial_frame', .8); }
window.onresize = function() { setIframeHeight('tutorial_frame', .8); }
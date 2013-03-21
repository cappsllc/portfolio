//*****MANDATORY HTML CONTROLS IN THE PAGE********//
	//Div with ID 'loadHTMLDATA'
	//Load button with ID 'load-more'
	//Page with ID 'page-mipim-feed'
//*************//

//show the page loader image
$('#page-mipim-feed').bind('pageshow', function() {       
        $.mobile.pageLoading();
     });
 
//button event to load data
$("#load-more").click(
		function(event)
		{
			toggleloadmore();
			loadData();
		}); 
		
//load data using ajax
function loadData()
	{
		$.mobile.pageLoading();
		$.ajax(
				{
				  url: "./mipim_ajax.cfm",
				  type: "POST",				  
				  cache: false,
				  data: 'feedtype='+feedtype+'&pageStart=' + pageIndex,
				  dataType: 'html',
				  success: function(htmlData){
						 	pageIndex++; 						 	
						 	var dataToDisp =$(htmlData).find("#ajaxDataDiv").html();
						 	if($.trim(dataToDisp) == '')
						 		{
						 		dataToDisp = '<br>No more data is available';						 							 		
						 		}
						 	else
						 		{
						 		toggleloadmore();						 		
						 		}
						 	$("#load-html-data").append(dataToDisp);
						 	$.mobile.pageLoading(true);
				  		}
				});	
	}
	
//show/hide the load more button	
function toggleloadmore()
	{
		$("#load-more").toggle(); 		
	} 
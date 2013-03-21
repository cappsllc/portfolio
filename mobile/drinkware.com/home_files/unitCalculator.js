var timeoutMiliseconds=600; 
var selectText = "-- select --"; 
$htmlTags = $( "html" );

function showSelects(a, b, c){
  if(a){
    $("select#select-choice-2").parent().parent().parent().parent().css("display", "block");
  }
  if(b){
    $("select#select-choice-3").parent().parent().parent().parent().css("display", "block");
  }
  if(c){
    $("select#select-choice-4").parent().parent().parent().parent().css("display", "block");
  } 
  $htmlTags.removeClass( "ui-loading" );
}
$(document).ready(function () {
  
  // Hide select boxes we are not ready for
  $("select#select-choice-2").attr("disabled", "disabled");
  $("select#select-choice-2").parent().parent().parent().parent().css("display", "none");
  $("select#select-choice-3").attr("disabled", "disabled");
  $("select#select-choice-3").parent().parent().parent().parent().css("display", "none");
  $("select#select-choice-4").attr("disabled", "disabled");
  $("select#select-choice-4").parent().parent().parent().parent().css("display", "none");
  $("div.units_number").children().html('0');
    
  $("select#select-choice-1").change(function () {
    $htmlTags.addClass( "ui-loading" );
    var select1 = $('select#select-choice-1 option:selected').val();
    $.getJSON("/json/Drinkaware.ashx?url=http://my.drinkaware.co.uk/drink/categories/"+select1 +"/types.json", {}, function (data) {
   
      var opts = '';
      if (data.length > 1) {opts +='<option value="">' + selectText + '</option>';   }
    
      $("select#select-choice-3").html('<option value="">' + selectText + '</option>');
      $("select#select-choice-4").html('<option value="">' + selectText + '</option>');  
      $("select#select-choice-3").parent().find("span.ui-btn-text").html(selectText);
      $("select#select-choice-4").parent().find("span.ui-btn-text").html(selectText); 
      
      $("select#select-choice-2").attr("disabled", "disabled");
      $("select#select-choice-2").parent().parent().parent().parent().css("display", "none");
      $("select#select-choice-3").attr("disabled", "disabled");
      $("select#select-choice-3").parent().parent().parent().parent().css("display", "none");
      $("select#select-choice-4").attr("disabled", "disabled");
      $("select#select-choice-4").parent().parent().parent().parent().css("display", "none");
      $("div.units_number").children().html('0');
      
      $.each(data, function (drink, val) {          
        opts += '<option value="' + val.drink_type.id+ '">' + val.drink_type.display_name+ '</option>';
      });
      
      $("select#select-choice-2").html(opts); 
      $("select#select-choice-2").removeAttr("disabled");       
      if (data.length == 1) {
        $("select#select-choice-2").val(data[0].drink_type.id);
        $("select#select-choice-2").parent().find("span.ui-btn-text").html($('select#select-choice-2 option:selected').text());
        
        var select2 = $('select#select-choice-2 option:selected').val();
        opts = '<option value="">' + selectText + '</option>';            
        $.each(data[0].drink_type.drink_products, function (drink, val) {
          opts += '<option value="' + val.id+ '">' + val.name_with_abv+ '</option>';
        });
        $("select#select-choice-3").html(opts);
        $("select#select-choice-3").removeAttr("disabled");
        var t=setTimeout("showSelects(true, true, false)", timeoutMiliseconds);
        
      }
      else {
        $("select#select-choice-2").parent().find("span.ui-btn-text").html(selectText);
        var t=setTimeout("showSelects(true, false, false)", timeoutMiliseconds);
      }      
    });
   
  });      
   
  $("select#select-choice-2").change(function () {
    $htmlTags.addClass( "ui-loading" );
    var select2 = $('select#select-choice-2 option:selected').val();
    $.getJSON("/json/Drinkaware.ashx?url=http://my.drinkaware.co.uk/drink/types/"+select2 +"/products.json", {}, function (data) {
      
      var select1 = $('select#select-choice-1 option:selected').val();
      var opts = '<option value="">' + selectText + '</option>';  
      $("select#select-choice-4").html(opts); 
      $("select#select-choice-4").parent().find("span.ui-btn-text").html(selectText);
      
      $("select#select-choice-3").attr("disabled", "disabled");
      $("select#select-choice-3").parent().parent().parent().parent().css("display", "none");
      $("select#select-choice-4").attr("disabled", "disabled");
      $("select#select-choice-4").parent().parent().parent().parent().css("display", "none");      
      $("select#select-choice-4").parent().parent().parent().parent().css("display", "none");
      $("div.units_number").children().html('0');
      
      $.each(data, function (drink, val) {
            opts += '<option value="' + val.drink_product.id + '">' + val.drink_product.name_with_abv + '</option>';
        });
        $("select#select-choice-3").html(opts);
        $("select#select-choice-3").parent().find("span.ui-btn-text").html(selectText);
        $("select#select-choice-3").removeAttr("disabled");
        var t=setTimeout("showSelects(false, true, false)", timeoutMiliseconds);
      
    });
  });            

$("select#select-choice-3").change(function () {
    $htmlTags.addClass( "ui-loading" );
    var select3 = $('select#select-choice-3 option:selected').val();
    $.getJSON("/json/Drinkaware.ashx?url=http://my.drinkaware.co.uk/drink/products/"+select3 +"/amounts.json", {}, function (data) {
        var opts = '<option value="">' + selectText + '</option>';
        $("select#select-choice-4").attr("disabled", "disabled");
        $("select#select-choice-4").parent().parent().parent().parent().css("display", "none");
        $("div.units_number").children().html('0');
        $.each(data, function (drink, val) {
            opts += '<option value="' + val.drink_amount.product_units+ '">' + val.drink_amount.alias_with_volume + '</option>';
        });
        $("select#select-choice-4").html(opts);
        $("select#select-choice-4").parent().find("span.ui-btn-text").html(selectText);
        $("select#select-choice-4").removeAttr("disabled");
        var t=setTimeout("showSelects(false, false, true)", timeoutMiliseconds);
    });
  });      

$("select#select-choice-4").change(function () {
  $htmlTags.addClass( "ui-loading" );
  var select4 = $('select#select-choice-4 option:selected').val();
  $("div.units_number").children().html(parseFloat(select4).toFixed(1));
  $htmlTags.removeClass( "ui-loading" );
});
});


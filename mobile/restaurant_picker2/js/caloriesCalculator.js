function togglefemale() {
      var ele = document.getElementById("togglePercentageFemale");
      var text = document.getElementById("overlay-percentage-female");
      if(ele.style.display == "block") {
        ele.style.display = "none";
        text.innerHTML = "<span class='drink_values_btn'><img src='/images/calculate_drink_values.png'></span>";
      }
      else {
        ele.style.display = "block";
        text.innerHTML = "<span class='drink_values_btn'><img src='/images/hide_calculate_drink_values.png'></span>";
      }
    } 
    function togglemale() {
      var ele = document.getElementById("togglePercentageMale");
      var text = document.getElementById("overlay-percentage-male");
      if(ele.style.display == "block") {
        ele.style.display = "none";
        text.innerHTML = "<span class='drink_values_btn'><img src='/images/calculate_drink_values.png'></span>";
      }
      else {
        ele.style.display = "block";
        text.innerHTML = "<span class='drink_values_btn'><img src='/images/hide_calculate_drink_values.png'></span>";
      }
    }    

 
  $(document).ready(function() {
    
    
    // Results Arrays
    var femaleResultsArr = { "units": 0, "calories": 0 };
    var maleResultsArr = { "units": 0, "calories": 0 };
      
    //Load drinks into picker
    $.getJSON("/json/calculator.ashx", {}, function(data){
      //loop through male and female
      for(i=0; i<2; i++){
        //set the gender
        if(i == 0){var gender = "female";}else{var gender = "male";}
        
        var pickerHTML = "";
        var toggleHTML = '<div class="overlay_percentage_holder">';
        pickerHTML += '<a name="'+gender+'-shelf"></a>';
        //loop through each drink
        $.each(data.drinkslist.drinks, function(drink, val){
          var dTitle = val.title;
          //convert title to a useable string
          var dClass = dTitle.replace("(", "");
          dClass = dClass.replace(")", "");
          dClass = dClass.replace(" ", "-");
          dClass = dClass.replace(" ", "-");
          var dA = dClass.replace("_", "-");
          if(dA == "Cider"){dA = "Cider-Glass";}
          var dStrength = val.text;
          var dImage = val.image;
          var dUnits = val.units;
          var dCalories = val.calories;
          
          //create html for each drinks toggled display
          toggleHTML += '<span class="addToShelf" style="cursor:pointer;" rel="'+drink+'"><h6 rel="'+dA.toLowerCase()+'">'+dStrength+'<br>'+dUnits+' units<br>'+dCalories+' calories</h6></span>'
                  
          //build each drink
                    pickerHTML += '<a href="#'+gender+'-shelf" class="'+dClass.toLowerCase()+'_'+gender+' addToShelf" rel="'+drink+'"><div class="drink_item">';
          pickerHTML += '<img src="'+dImage+'"><strong rel="'+dA.toLowerCase()+'">'+dTitle+'</strong></div></a>';
          
        });
        toggleHTML += '<span><!--important for alignment--></span></div>';
        
        pickerHTML += '<a id="overlay-percentage-'+gender+'" href="javascript:toggle'+gender+'();" class="overlay_percentage_link"><div class="drink_values_btn"><img src="/images/calculate_drink_values.png"></div></a>';
        
        //load into the dom
        $("#"+gender+" div.overlay_percentage_holder_box").html(toggleHTML);
        $("#"+gender+" div.row_items").html(pickerHTML);
        $("#"+gender+"_calculation div.drink_calculation_results_title h1").html("Results for "+gender);
        
        
      }
      
      // Click on drinks
      $('a.addToShelf').click(function(){
        var i0 = $(this).parent().parent().parent().attr("id");
        var i1 = $(this).find("strong").attr("rel")
        var i2 = $(this).attr("rel")
        addShelf(i0, i1, i2);
        location.href=$(this).attr("href");
      });
      $('span.addToShelf').click(function(){
        var i0 = $(this).parent().parent().parent().parent().attr("id");
        var i1 = $(this).find("h6").attr("rel")
        var i2 = $(this).attr("rel")
        addShelf(i0, i1, i2);
        location.href=$(this).parent().parent().parent().find("a.addToShelf").attr("href");
      });
    });
    
    //Click drink function
    function addShelf(a, b, c){
      $.getJSON("/json/calculator.ashx", {}, function(data){
        var dU = data.drinkslist.drinks[c].units;
        
        var dC = data.drinkslist.drinks[c].calories;
        if (maxDrinks(a)) {
          $('<ins class="' + a + '"><span class="' + b + '">&nbsp;</span></ins>').animate({ opacity: "show" }, "fast").appendTo('.drink_shelf_' + a);
          $('<ins class="' + a + '"><span class="' + b + '">&nbsp;</span></ins>').animate({ opacity: "show" }, "fast").appendTo('.drink_shelf_' + a + '2');
          updateResults(a, dU, dC);
        }
      });
    }
    
    // Check for drink limit
    function maxDrinks(p0) {
      if (($('.drink_shelf_' + p0 + ' .' + p0).length < 9)) {
        return true;
      }else{
        alert("Oops, that's one too many. You can only enter up to 9 drinks in one go.");
        return false;
      }
    }
    
    // Update results
    function updateResults(gender, units, calories){
      $.getJSON("/json/calculator.ashx", {}, function(data){
        if(gender == "female"){
          //add the units/calories from the selected drink to the totals
          femaleResultsArr.units = parseFloat(femaleResultsArr.units) + parseFloat(units);
          femaleResultsArr.calories = parseFloat(femaleResultsArr.calories) + parseFloat(calories);

          // get the different limits
          var unitLimitA = data.results.genres[0].unitresults[0].unitsto;
          var unitLimitB = data.results.genres[0].unitresults[1].unitsto;
          var unitTextColor = "";
          
          // set the font colour and display text depending on the limit reached
          if (parseFloat(femaleResultsArr.units) < unitLimitA){
            unitTextColor = "#" + data.results.genres[0].unitresults[0].hexcolour;
            $("#female_calculation .units_results .right_results span").html('<a href="'+data.results.genres[0].unitresults[0].healthharmurl+'" title="" data-ajax="false">'+data.results.genres[0].unitresults[0].healthharmtext+'</a>');
          }else if(parseFloat(femaleResultsArr.units) < unitLimitB){
            unitTextColor = "#" + data.results.genres[0].unitresults[1].hexcolour;
            $("#female_calculation .units_results .right_results span").html('<a href="'+data.results.genres[0].unitresults[1].healthharmurl+'" title="" data-ajax="false">'+data.results.genres[0].unitresults[1].healthharmtext+'</a>');
          }else{
            unitTextColor = "#" + data.results.genres[0].unitresults[2].hexcolour;
            $("#female_calculation .units_results .right_results span").html('<a href="'+data.results.genres[0].unitresults[2].healthharmurl+'" title="" data-ajax="false">'+data.results.genres[0].unitresults[2].healthharmtext+'</a>');
          }
          $(".units_results").removeClass("red_txt").css('color', unitTextColor);
          $("#female_calculation .units_results .right_results span a").css("color", unitTextColor);
        }else{
          //add the units/calories from the selected drink to the totals
          maleResultsArr.units = parseFloat(maleResultsArr.units) + parseFloat(units);
          maleResultsArr.calories = parseFloat(maleResultsArr.calories) + parseFloat(calories);
          
          // get the different limits
          var unitLimitA = data.results.genres[1].unitresults[0].unitsto;
          var unitLimitB = data.results.genres[1].unitresults[1].unitsto;
          var unitTextColor = "";
          
          // set the font colour and display text depending on the limit reached
          if (parseFloat(maleResultsArr.units) < unitLimitA){
            unitTextColor = "#" + data.results.genres[1].unitresults[0].hexcolour;
            $("#male_calculation .units_results .right_results span").html('<a href="'+data.results.genres[1].unitresults[0].healthharmurl+'" title="" data-ajax="false">'+data.results.genres[1].unitresults[0].healthharmtext+'</a>');
          }else if(parseFloat(maleResultsArr.units) < unitLimitB){
            unitTextColor = "#" + data.results.genres[1].unitresults[1].hexcolour;
            $("#male_calculation .units_results .right_results span").html('<a href="'+data.results.genres[1].unitresults[1].healthharmurl+'" title="" data-ajax="false">'+data.results.genres[1].unitresults[1].healthharmtext+'</a>');
          }else{
            unitTextColor = "#" + data.results.genres[1].unitresults[2].hexcolour;
            $(".units_results").removeClass("red_txt").css('color', unitTextColor);
            $("#male_calculation .units_results .right_results span").html('<a href="'+data.results.genres[1].unitresults[2].healthharmurl+'" title="" data-ajax="false">'+data.results.genres[1].unitresults[2].healthharmtext+'</a>');
          }
          $(".units_results").removeClass("red_txt").css('color', unitTextColor);
          $("#male_calculation .units_results .right_results span a").css("color", unitTextColor);
        }
        
        var exerciseFemale = (femaleResultsArr.calories / 10);
        var exerciseMale = (maleResultsArr.calories / 10);
        
        var femaleBurgersVal = Math.round(femaleResultsArr.calories * 10 / 285) / 10;
        var femaleBurgerBGPic = "url(/images/burger_pic.png)";
        var femaleBurgerBGSize = "40px 36px";
        if(femaleBurgersVal > 1){
          femaleBurgerBGPic = "url(/images/2burger_pic.png)";
          femaleBurgerBGSize = "80px 36px";
        }else if(femaleBurgersVal > 2){
          femaleBurgerBGPic = "url(/images/3burger_pic.png)";
          femaleBurgerBGSize = "120px 36px";
        }else if(femaleBurgersVal > 3){
          femaleBurgerBGPic = "url(/images/4burger_pic.png)";
          femaleBurgerBGSize = "160px 36px";
        }else if(femaleBurgersVal > 4){
          femaleBurgerBGPic = "url(/images/5burger_pic.png)";
          femaleBurgerBGSize = "200px 36px";
        }else{
          femaleBurgerBGPic = "url(/images/burger_pic.png)";
          femaleBurgerBGSize = "40px 36px";
        }
        // Update the results page with the live results
        $("#female_calculation .units_results h1").html(femaleResultsArr.units.toFixed(1));
        $("#female_calculation .calories_results h1").html(Math.round(femaleResultsArr.calories));
        $("#female_calculation .equivalent_results h3").html(femaleBurgersVal + " BURGER(S)");
        $("#female_calculation .calories_results .right_results span").html("Exercise needed to burn: "+Math.round(exerciseFemale)+" minute run.");
        $("#female_calculation .equivalent_results span.burger_icon").css({"background-image":femaleBurgerBGPic, "background-size":femaleBurgerBGSize});
        
        var maleBurgersVal = Math.round(maleResultsArr.calories * 10 / 285) / 10;
        var maleBurgerBGPic = "url(/images/burger_pic.png)";
        var maleBurgerBGSize = "40px 36px";
        if(maleBurgersVal > 1){
          maleBurgerBGPic = "url(/images/2burger_pic.png)";
          maleBurgerBGSize = "80px 36px";
        }else if(maleBurgersVal > 2){
          maleBurgerBGPic = "url(/images/3burger_pic.png)";
          maleBurgerBGSize = "120px 36px";
        }else if(maleBurgersVal > 3){
          maleBurgerBGPic = "url(/images/4burger_pic.png)";
          maleBurgerBGSize = "160px 36px";
        }else if(maleBurgersVal > 4){
          maleBurgerBGPic = "url(/images/5burger_pic.png)";
          maleBurgerBGSize = "200px 36px";
        }else{
          maleBurgerBGPic = "url(/images/burger_pic.png)";
          maleBurgerBGSize = "40px 36px";
        }
        
        $("#male_calculation .units_results h1").html(maleResultsArr.units.toFixed(1));
        $("#male_calculation .calories_results h1").html(Math.round(maleResultsArr.calories));
        $("#male_calculation .equivalent_results h3").html(maleBurgersVal + " BURGER(S)");
        $("#male_calculation .equivalent_results span.burger_icon").css({"background-image":maleBurgerBGPic, "background-size":maleBurgerBGSize});
        $("#male_calculation .calories_results .right_results span").html("Exercise needed to burn: "+Math.round(exerciseMale)+" minute run.");
        
      });
    }
    $('a.reset_women').click(function () {
      $('.female').remove();
      femaleResultsArr.calories = 0;
      femaleResultsArr.units = 0;
      $("#female_calculation .units_results h1").html('No drinks selected');
      $("#female_calculation .calories_results h1").html('No drinks selected');
      $("#female_calculation .equivalent_results h3").html("0 BURGER(S)");
      $("#female_calculation .calories_results .right_results span").html("It takes 10 minutes of exercise to burn 100 calories");
      $("#female_calculation .units_results .right_results span").html("A female recommended daily guideline is 2-3 units.");
    });
    $('a.reset_male').click(function () {
      $('.male').remove();
      maleResultsArr.calories = 0;
      maleResultsArr.units = 0;
      $("#male_calculation .units_results h1").html('No drinks selected');
      $("#male_calculation .calories_results h1").html('No drinks selected');
      $("#male_calculation .equivalent_results h3").html("0 BURGER(S)");
      $("#male_calculation .calories_results .right_results span").html("It takes 10 minutes of exercise to burn 100 calories");
      $("#male_calculation .units_results .right_results span").html("A male recommended daily guideline is 2-3 units.");
    });
  });
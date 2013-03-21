(function () {
    var DA = {
        Settings: {
            Time: 500
        },
        Init: function () {
            this.Accordion.Init();
            this.Search.Init();
        },
        Accordion: {
            ClassName: "ul.jsAccordion",
            Init: function () {
                $(DA.Accordion.ClassName + " li > ul").css("display", "none");

                // Find each instance matching of the ClassName in the DOM
                $(DA.Accordion.ClassName + " li > a.tip-section").each(function (x) {

                    var parent = $(this).parents(DA.Accordion.ClassName);

                    // Bind a click event to each <a> tag found in the above loop
                    $(this).bind("click", function () {

                        $(parent).addClass("testing");

                        // Slide the content in or out via an animation
                        DA.Accordion.Animate(this, parent);

                    });

                });
            },
            Animate: function (el, parent) {




                // Using jQuery's slideToggle, slide the sibling <ul> element of the selected <a> tag in and out of view.
                $(el).siblings("ul").slideToggle(DA.Settings.Time / 2, function () {

                    // Test  if the content of the parent is expanded or not, and remove the attribute specifying this if true, otherwise add it.
                    if ($(this).parent().attr("jsAccordion")) {

                        $(el).find("span").removeClass("contract");
                        $(this).parent().removeAttr("jsAccordion");

                    } else {

                        $(el).find("span").addClass("contract");
                        $(this).parent().attr("jsAccordion", "true");

                    };
                });

                // If the selected child elements are not being viewed, loop through the parent and test if any content is visible, if there is
                // hide the content and remove the attribute specifying that the contnent is visable.
                $(el).parents(DA.Accordion.ClassName).find("li[jsAccordion]").not($(el).parent()).find("ul").slideToggle(DA.Settings.Time / 2, function () {


                    if ($(this).siblings("a").find("span").hasClass("contract")) {
                        $(this).siblings("a").find("span").removeClass("contract");
                    } else {
                        $(this).siblings("a").find("span").addClass("contract");
                    };


                    $(this).parent().removeAttr("jsAccordion");

                });

            }
        },
        /*
        This shows and hides the content in the seach box when the <input> element has focus or blur.
        */
        Search: {
            // Settings
            JSHook: "div#search",
            Store: "",
            MinLength: 3,
            Init: function () {
                $el = $(this.JSHook + " input:text");

                $el.bind("focus", function () {
                    DA.Search.Store = $(this).val();
                    $(this).val("");
                });

                $el.bind("blur", function () {
                    if ($(this).val().length >= DA.Search.MinLength) {
                        DA.Search.Store = $(this).val();
                    } else {
                        $(this).val(DA.Search.Store)
                    };
                });
            }
        }
    };
    DA.Init();
})()
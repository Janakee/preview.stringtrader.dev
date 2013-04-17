jQuery(document).ready(function($) {

        var date = $.datepicker.regional[dateOptions.locale];
            if(dateOptions.date_format != "")
                date.dateFormat = dateOptions.date_format;

                date.showAnim = dateOptions.animation;

            if(dateOptions.multi_month){
                date.numberOfMonths = parseInt(dateOptions.multi_month,10);}

            if(dateOptions.button_bar){
                date.showButtonPanel = true;}

            if(dateOptions.menus){
                date.changeMonth = true;
                date.changeYear = true;}

            if(dateOptions.other_dates){
                date.showOtherMonths = true;
                date.selectOtherMonths = true;}

            if(dateOptions.icon_trigger){
                date.showOn = "both";
                date.buttonImage = $( "#calendar" ).attr("src");
                date.buttonImageOnly = true;}

   $( ".dateCustom" ).datepicker(date);

       $.validator.addMethod(
      "dateCustom",
       function(value, element) {
           var ret = true;
           var format = date.dateFormat;
            try{$.datepicker.parseDate( format, value, date);}
            catch(e){ret = false;}
            return this.optional(element) || ret;
           },
           'Please enter date in valid format!'
   );
});
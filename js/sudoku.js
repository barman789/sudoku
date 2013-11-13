    $(document).ready(function() {
         currentSelectedValue = 1;
         
         $('.fillgrid').click(function() {
                 $('.fillGridTable td').removeClass('highlight');
                 currentSelectedValue = $(this).html()
                 $(this).addClass('highlight');
         });

         
         $('.grid').click(function() {
                 $(this).html(currentSelectedValue);
                 aid = $(this).attr('id');
                 reqId = aid.replace('grid', 'hidden_grid');
                 
                 reqHtml = currentSelectedValue;
                 if(currentSelectedValue == '') {
                     reqHtml = 0;
                 }
                 $('#'+reqId).val(reqHtml);
         });
         
         $('#clearAll').click(function() {
              $('.grid').each(function() {
                 $(this).html('');
              });
              $('.hidden_grid').each(function() {
                 $(this).val('0');
              });              
         });
                  
         $('#clearOne').click(function() {
              $('.fillGridTable td').removeClass('highlight');
              currentSelectedValue = '';       
         });         
    
    });
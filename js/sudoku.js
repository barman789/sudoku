    $(document).ready(function() {
         currentSelectedValue = 1;
         setEvenflag = false;
         
         $('.fillgrid').click(function() {
                 $('.fillGridTable td').removeClass('highlight');
                 currentSelectedValue = $(this).html()
                 $(this).addClass('highlight');
         });

         
         $('.grid').click(function() {
              if(!setEvenflag) {   
                 $(this).html(currentSelectedValue);
                 aid = $(this).attr('id');
                 reqId = aid.replace('grid', 'hidden_grid');
                 
                 reqHtml = currentSelectedValue;
                 if(currentSelectedValue == '') {
                     reqHtml = 0;
                     $(this).removeClass('even');
                     reqId1 = aid.replace('grid', 'hidden_grid_even_odd');
                     $('#'+reqId1).val(0);
                 }
                 $('#'+reqId).val(reqHtml);
              } else {
                 aid = $(this).attr('id');
                 reqId = aid.replace('grid', 'hidden_grid_even_odd');
                 $('#'+reqId).val(1);
                 $(this).addClass('even');              
              }   
         });
         
         $('#setEven').click(function() {
              setEvenflag = true;
              $('#span_doneEven').show();
              $('#span_setEven').hide();
         });

         $('#doneEven').click(function() {
              setEvenflag = false;
              $('#span_doneEven').hide();
              $('#span_setEven').show();
         });         
         
         $('#clearAll').click(function() {
              $('.grid').each(function() {
                 $(this).html('');
                 $(this).removeClass('even');
              });
              $('.hidden_grid').each(function() {
                 $(this).val('0');
              });
              $('.hidden_grid_even_odd').each(function() {
                 $(this).val('0');
              });                                 
         });
                  
         $('#clearOne').click(function() {
              $('.fillGridTable td').removeClass('highlight');
              currentSelectedValue = '';       
         });         
    
    });
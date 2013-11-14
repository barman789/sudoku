<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="">
  <title>Suodku Solver</title>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="./js/sudoku.js"></script>
  <link rel="stylesheet" type="text/css" href="./css/sudoku.css"/>
  </head>
  <body>
  
       <h1>Evn Odd Sudoku Solver Demo</h1>
         
       <form action="solve.php" method="POST">
       <table bgcolor = "#000000" cellspacing = "1" cellpadding = "2" class="gridTable">
       <?php
       
            for($i = 0; $i < 9; $i++){
                echo '<tr bgcolor = "white" align = "center">';
                for($j = 0; $j < 9; $j++){
                    echo '<td width="20" height="20" class="grid r'.$i.' c'.$j.'" id="grid'.$i.$j.'"></td>';
                    echo '<input type="hidden" class="hidden_grid" name="hidden_grid'.$i.$j.'" id="hidden_grid'.$i.$j.'" value="0" />';
                    echo '<input type="hidden" class="hidden_grid_even_odd" name="hidden_grid_even_odd'.$i.$j.'" id="hidden_grid_even_odd'.$i.$j.'" value="0" />'; 
                }
                echo "</tr>";
            }
       ?>
       </table>
       <input type="hidden" id="isEvenOdd" name="isEvenOdd" value="1" />
       <input type="submit" value="Solve" />
       </form>
       
       <br />
       <table bgcolor = "#000000" cellspacing = "1" cellpadding = "2" class="fillGridTable">
             <?php
                 for($i = 0; $i < 3; $i++){
                    echo '<tr bgcolor = "white" align = "center">';
                    for($j = 0; $j < 3; $j++){
                        $value = $i * 3 + $j + 1;
                        $class = '';
                        if($value == 1) {
                           $class = 'highlight';
                        }
                        echo '<td width="20" height="20" class="fillgrid '.$class.'" id="fillgrid'.$value.'">'.$value.'</td>';
                    }
                    echo "</tr>";                 
                 }        
             ?>
       </table>
       <span id="span_setEven">
         <a href="#" title="Select this and click on the Cell to Mark it Even" onclick="return false;" id="setEven">Mark Even Cells</a>
       </span>
       <span id="span_doneEven">
         <a href="#" title="Select this when cells are marked" onclick="return false;" id="doneEven">Done Marking Cells</a>
       </span>
       <a href="#" title="Select this and click on the Cell to Clear it" onclick="return false;" id="clearOne">Clear Cell</a>
       <a href="#" id="clearAll" onclick="return false;">Reset Grid</a>

       <div id="footer">
            Author Sandeep Barman. Can be reached via <a href="https://github.com/barman789">Github</a>
       </div>       
  </body>
</html>

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

       <h1>Sudoku Solution</h1>
       
<?php
  
  $i = 0;
  $j = 1;
  $sudoku = array();
  
   for($i = 0; $i < 9; $i++) {
       $sudoku[$i] = array();
       for($j = 0; $j < 9; $j++) {
            $var = 'hidden_grid'.$i.$j;
            $sudoku[$i][] = $_POST[$var];
       }
   }
  
	include_once "SudokuSolver.class.php";

	set_time_limit(0);
	
  $start = microtime();
  
	$solver = new SudokuSolver();
	$solver->setSudoku($sudoku);
  if(isset($_POST['isX']) && $_POST['isX'] == 1) {
      $solver->isSudokuX(true);
  }
	$solved = $solver->solveSudoku();
	
  $end = microtime();
  $ms_start = explode(" ",$start);
  $ms_end = explode(" ",$end);
  $total_time = round(($ms_end[1] - $ms_start[1] + $ms_end[0] - $ms_start[0]),2);
  echo "completed in $solver->loop loop in $total_time seconds";
    

  $html = $solver->print_clean();
  echo $html;	

?>

       <div id="footer">
            Author Sandeep Barman. Can be reached via <a href="https://github.com/barman789">Github</a>
       </div>
       
  </body>
</html>

sudoku
======

Sudoku and Sudoku X Solver Class in PHP and sample integration using HTML using Jquery


Sudoku Class written in PHP which can be used to solve simple as well as Sudoku-X using logic as well as hit 
and trial with backtracking. Following steps are performed during algorithm

1. The candidates are determined for the cells which are empty.

2. The cells which have only 1 candidates are filled.

3. The above 2 steps are repeated in loop untill we have no cell with only 1 candidate. At this step hit and trial
method is used on the cell having least number of candidates. This cell is filled with one of its available candidate.

4. Steps 1 and 2 are repeated in loop.

5. If something goes wrong then we backtrack to Step 3 and remove the candidate that we had used from that cell. 

The above steps are repeated untill we have a solution.

Using the above class the Sudoku which are marked as "Evil" in http://www.websudoku.com/ were solved in less than 2 
seconds. 


Below mentioned are the steps to use this Class in your code.

1. Include the Class

	include_once "SudokuSolver.class.php";

2. Create the Object.
	
	$solver = new SudokuSolver();

3. Pass the Sudoku to be solved to the Class as an Array. The structure of array to be passed can be seen in solve.php

	$solver->setSudoku($sudoku);
	

4. Call the solveSudoku Method and it will return the Solved Array.

	$solved = $solver->solveSudoku();
	
Note: If you want to Solve SudokuX, call the following function before calling solveSudoku Function.

  $solver->isSudokuX(true);
  
  
You can check solve.php File in order to see how the Class has been used. In order to check how the Form and HTML
has been structured you can check simple.php (For Simple Sudoku) and x.php (For Sudoku X). 

The Design is not fancy at all and you are free to change it as per your need.

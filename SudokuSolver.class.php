<?php

/**
* @author Sandeep <barman789@gmail.com>
*/

Class SudokuSolver{
	

/**
 * This is the number of times we had to loop for solution
 *
 * @var array
 * @access public
 */
	public $loop = 0;
	
/**
 * This is the initial input Sudoku Array
 *
 * @var array
 * @access private
 */
	private $_initialSudoku;

/**
 * This flag is used to determine Sudoku X
 *
 * @var boolean
 * @access private
 */
	private $_isSudokuX = false;
	
/**
 * This tells if Sudoku is solved or not
 *
 * @var boolean
 * @access private
 */
	private $_is_solved = false;
	
/**
 * This contains probable values for all the cells. 
 *
 * @var array
 * @access private
 */
	private $_candidates;
/**
 * This contains candidates where we used hit and trial 
 * It is used to backtrack in case of error
 *
 * @var array
 * @access private
 */
	private $_trial_sudoku_candidates = array();

/**
 * This contains the state of the sudoku while we used hit and trial method
 * It is used to backtrack in case of error  
 * @var array
 * @access private
 */
	private $_trial_sudoku_state = array();


/**
 * Constructor
 */
	function __construct() {
  
  }


/**
 * Saves the Sudoku in $_initialSudoku Array
 *
 * @return void
 * @access public
 */
	public function setSudoku($input) { 	
  	   $this->_initialSudoku = $input;
  }

/**
 * Saves the Sudoku in $_initialSudoku Array
 *
 * @return void
 * @access public
 */
	public function isSudokuX($boolean = false) { 	
  	   $this->_isSudokuX = $boolean;
  }  

/**
 * Solves the Sudoku
 *
 * @return void
 * @access public
 */
	public function solveSudoku() { 	
       $this->loop = 0;
       $c = 0;
       
       while( !$this->_is_solved ) {
            $this->_determineCandidates();

            $this->_isEverythingOkSoFar();
            if($this->_errorOccured) {
                $c++;
                $this->_candidates = array_pop($this->_trial_sudoku_candidates);
                $this->_initialSudoku = array_pop($this->_trial_sudoku_state);
            }
            
            $this->_fillCellsWithOneCandidate();
            
            //$this->needToGuess = true;
            if($this->needToGuess) {
                $this->_getCellWithLeastCandidates();
                if($this->activeRow !== null && $this->activeColumn !== null) {
                    $cellValue = reset($this->_candidates[$this->activeRow][$this->activeColumn]);
                    if(count($this->_candidates[$this->activeRow][$this->activeColumn]) > 1) {
                         $key = array_search($cellValue, $this->_candidates[$this->activeRow][$this->activeColumn]);
                         unset($this->_candidates[$this->activeRow][$this->activeColumn][$key]);
                         array_push($this->_trial_sudoku_candidates, $this->_candidates);
                         array_push($this->_trial_sudoku_state, $this->_initialSudoku);
                    }
                         
                    unset($this->_candidates[$this->activeRow][$this->activeColumn]);
                    $this->_initialSudoku[$this->activeRow][$this->activeColumn] = $cellValue;
                }
            }
            $this->_checkSolved();
            $this->loop++;
            //if($this->loop == 200) {
              //  $this->print_clean();
                //die;
            //}
       }

       return $this->_initialSudoku;
  }	  	
  
/**
 * Determine the Candidates for each cell
 *
 * @return void
 * @access private
 */
	private function _determineCandidates() { 
	
     $this->_errorOccured = false;
     
     for($i = 0; $i < 9; $i++) {
         for($j = 0; $j < 9; $j++) {
             if( isset($this->_initialSudoku[$i][$j]) && $this->_initialSudoku[$i][$j] == 0 ) {
                  $candidates = range(1, 9);
                  $candidates = $this->_removeRowDuplicates($i, $candidates);
                  $candidates = $this->_removeColumnDuplicates($j, $candidates);
                  $candidates = $this->_removeBlockDuplicates($i, $j, $candidates);
                  if($this->_isSudokuX) {
                  //if(false) {
                      $candidates = $this->_removeXDuplicates($i, $j, $candidates);
                  }
         
                  if(empty($candidates)) {
                      $this->errorOccured = true;
                      break;
                  } 
                  $this->_candidates[$i][$j] = $candidates;
             }
         }
         if($this->_errorOccured) {
             break;
         }
     }
	}

/**
 * Fill the Cell with only one Candidate
 *
 * @return void
 * @access private
 */
	private function _fillCellsWithOneCandidate() { 
	
     $this->needToGuess = true;
     for($i = 0; $i < 9; $i++) {
         for($j = 0; $j < 9; $j++) {
             if(isset($this->_candidates[$i][$j])) {
                 if( is_array($this->_candidates[$i][$j]) && (count($this->_candidates[$i][$j]) == 1)) {
                      $cellValue = reset($this->_candidates[$i][$j]);
                      unset($this->_candidates[$i][$j]);
                      $this->_initialSudoku[$i][$j] = $cellValue;
                      $this->needToGuess = false;
                 }
             }
         }
     }
	}	
	
/**
 * Gets the Cell with Least Candidates
 *
 * @return void
 * @access private
 */
	private function _getCellWithLeastCandidates() { 
	
     $this->activeRow = null;
     $this->activeColumn = null;
     $minimum_candidates = 9;
     for($i = 0; $i < 9; $i++) {
         for($j = 0; $j < 9; $j++) {
             if(isset($this->_candidates[$i][$j])) {
                 if( is_array($this->_candidates[$i][$j]) && (count($this->_candidates[$i][$j]) <= $minimum_candidates) && (count($this->_candidates[$i][$j]) > 0)) {
                      $this->activeRow = $i;
                      $this->activeColumn = $j;
                      $minimum_candidates = count($this->_candidates[$i][$j]);
                 }
             }
         }
     }
	}	

/**
 * Removes the candidates which are already present in the Row
 *
 * @return array
 * @access private
 */
	private function _removeRowDuplicates($row, $candidates) { 
	
     for($i = 0; $i < 9; $i++) {
        if( isset($this->_initialSudoku[$row][$i]) && $this->_initialSudoku[$row][$i] != 0 ) {
           $key = array_search($this->_initialSudoku[$row][$i], $candidates);
           if($key !== false) {
              unset($candidates[$key]);
           }
        }
     }
     return $candidates;
	}	

/**
 * Removes the candidates which are already present in the Column
 *
 * @return array
 * @access private
 */
	private function _removeColumnDuplicates($column, $candidates) { 
	
     for($i = 0; $i < 9; $i++) {                    
        if( isset($this->_initialSudoku[$i][$column]) && $this->_initialSudoku[$i][$column] != 0 ) {
           $key = array_search($this->_initialSudoku[$i][$column], $candidates);
           if($key !== false) {
              unset($candidates[$key]);
           }
        }
     }
     return $candidates;
	}

/**
 * Removes the candidates which are already present in the 9*9 block
 *
 * @return array
 * @access private
 */
	private function _removeBlockDuplicates($row, $column, $candidates) { 
	
     $rowStart = floor( $row / 3 ) * 3;
     $colStart = floor( $column / 3 ) * 3;
     
     for($i = $rowStart; $i < $rowStart + 3; $i++) {
        for($j = $colStart; $j < $colStart + 3; $j++) {
            if( isset($this->_initialSudoku[$i][$j]) && $this->_initialSudoku[$i][$j] != 0 ) {
               $key = array_search($this->_initialSudoku[$i][$j], $candidates);
               if($key !== false) {
                  unset($candidates[$key]);
               }
            }               
        }
     }
     return $candidates;
	}
	
/**
 * Removes the candidates which are in X
 *
 * @return array
 * @access private
 */
	private function _removeXDuplicates($row, $column, $candidates) { 
	
     //Check if canditate fall in first Diagnol
     if($row == $column) {
         for($i = 0; $i < 9; $i++) {
             if( isset($this->_initialSudoku[$i][$i]) && $this->_initialSudoku[$i][$i] != 0 ) {
                 $key = array_search($this->_initialSudoku[$i][$i], $candidates);
                 if($key !== false) {
                    unset($candidates[$key]);
                 }
             } 
         }
     }
     
     //Check if canditate fall in second Diagnol
     if($column == 8 - $row) {
         for($i = 0; $i < 9; $i++) {
             $j = 8 - $i;
             if( isset($this->_initialSudoku[$i][$j]) && $this->_initialSudoku[$i][$j] != 0 ) {
                 $key = array_search($this->_initialSudoku[$i][$j], $candidates);
                 if($key !== false) {
                    unset($candidates[$key]);
                 }   
             } 
         }
     }
     
     return $candidates;
	}	

/**
 * Check if it is solved completely
 *
 * @return array
 * @access private
 */
	private function _checkSolved() { 
	
     $flag = true;
     for($i = 0; $i < 9; $i++) {
         for($j = 0; $j < 9; $j++) {
             if( isset($this->_initialSudoku[$i][$j]) && $this->_initialSudoku[$i][$j] == 0 ) {
                $flag = false;
                break;
             }
         }
         
         if( !$flag ) {
            break;
         }
     }
     $this->_is_solved = $flag;    
	}    	

/**
 * Check if Sudoku Constrains are broken
 *
 * @return array
 * @access private
 */
	private function _isEverythingOkSoFar() { 
	
     if($this->_isSudokuX) {
         //Check First Diagnol
         $rowValues = array();
         for($i = 0; $i < 9; $i++) {
             if( isset($this->_initialSudoku[$i][$i]) && $this->_initialSudoku[$i][$i] != 0 ) {
                  $rowValues[] = $this->_initialSudoku[$i][$i];
             } 
         }
         if(count(array_unique($rowValues)) < count($rowValues)) {
              $this->_errorOccured = true;
              return;
         }         
         
         //Check Second Diagnol
         $rowValues = array(); 
         for($i = 0; $i < 9; $i++) {
             $j = 8 - $i;
             if( isset($this->_initialSudoku[$i][$j]) && $this->_initialSudoku[$i][$j] != 0 ) {
                  $rowValues[] = $this->_initialSudoku[$i][$j];
             } 
         }
         
         if(count(array_unique($rowValues)) < count($rowValues)) {
              $this->_errorOccured = true;
              return;
         }                
     }

     //Check Row Constraint
     for($i = 0; $i < 9; $i++) {
         $rowValues = array();
         for($j = 0; $j < 9; $j++) {
             if( isset($this->_initialSudoku[$i][$j]) && $this->_initialSudoku[$i][$j] != 0 ) {
                  $rowValues[] = $this->_initialSudoku[$i][$j];
             } 
         }
         
         if(count(array_unique($rowValues)) < count($rowValues)) {
              $this->_errorOccured = true;
              return;
         }    
     }

     //Check Column Constraint
     for($i = 0; $i < 9; $i++) {
         $rowValues = array();
         for($j = 0; $j < 9; $j++) {
             if( isset($this->_initialSudoku[$j][$i]) && $this->_initialSudoku[$j][$i] != 0 ) {
                  $rowValues[] = $this->_initialSudoku[$j][$i];
             } 
         }
         
         if(count(array_unique($rowValues)) < count($rowValues)) {
              $this->_errorOccured = true;
              return;
         }    
     }
	}	
  
  
  function print_clean() {
  
    $html = '<table bgcolor = "#000000" cellspacing = "1" cellpadding = "2">';
    for($i = 0; $i < 9; $i++){
        $html .= '<tr bgcolor = "white" align = "center">';
        for($j = 0; $j < 9; $j++){
            $xx = '';
            if($this->_isSudokuX) {
                if($i == $j || $j == 8 - $i) {
                    $xx = 'xx';
                }
            }
            $html .= '<td width="20" height="20" class="grid r'.$i.' c'.$j.' '.$xx.'" id="grid'.$i.$j.'">'.$this->_initialSudoku[$i][$j].'</td>';
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
      
  }	
}
?>
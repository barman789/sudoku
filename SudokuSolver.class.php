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
 * This flag is used to determine EvenOdd Sudoku
 *
 * @var boolean
 * @access private
 */
	private $_isEvenOdd = false;
 	
/**
 * This holds the Even-Odd Stream for EvenOdd Sudoku
 *
 * @var array
 * @access private
 */
	private $_evenOddStream;

 	
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
 * Sets the Flag whether its a SudokuX
 *
 * @return void
 * @access public
 */
	public function isSudokuX($boolean = false) { 	
  	   $this->_isSudokuX = $boolean;
  }  

/**
 * Sets the Flag whether its a Even/Odd Sudoku
 *
 * @return void
 * @access public
 */
	public function isSudokuEvenOdd($boolean = false) { 	
  	   $this->_isEvenOdd = $boolean;
  }    

/**
 * Solves the Sudoku
 *
 * @return void
 * @access public
 */
	public function solveSudoku() { 	
          $this->loop = 0;
          $this->total_guesses = 0;
       
          while( !$this->_is_solved ) {
            $this->_determineCandidates();

            $this->_isEverythingOkSoFar();
            if($this->_errorOccured) {
                $this->_candidates = array_pop($this->_trial_sudoku_candidates);
                $this->_initialSudoku = array_pop($this->_trial_sudoku_state);
            }

            $this->_fillCellsWithOneCandidate();
            $this->_fillCellsWithHiddenSingles();

            if($this->needToGuess) {
                $this->total_guesses++;
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
       }

       echo 'guessed '. $this->total_guesses;
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
                          $candidates = $this->_removeXDuplicates($i, $j, $candidates);
                      }
                      if($this->_isEvenOdd) {
                          $candidates = $this->_removeEvenOddDuplicates($i, $j, $candidates);
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
 * Check if it is a Valid Even Odd Sudoku
 *
 * @return void
 * @access public
 */
	public function isValidEvenOddSudoku($evenOddStream) { 
	
            $this->_evenOddStream = $evenOddStream;
            for($x = 0; $x < 3; $x++) {
                for($y = 0; $y < 3; $y++) {
                    $rowStart = $x * 3;
                    $columnStart = $y * 3;
                    $no_of_even_blocks = 0;
                    for($i = $rowStart; $i < $rowStart + 3; $i++) {
                        for($j = $columnStart; $j < $columnStart + 3; $j++) {
                            if( isset($this->_evenOddStream[$i][$j]) && $this->_evenOddStream[$i][$j] != 0 ) {
                                $no_of_even_blocks++;
                            }               
                        }
                    }
                    if($no_of_even_blocks != 4) {
                         return false;
                    }
                }
            }
            
            //Check all even Marked Cells have Even No.s and all Unmarked Cells have Odd No.s
            for($i = 0; $i < 9; $i++) {
               for($j = 0; $j < 9; $j++) {
                    if( isset($this->_evenOddStream[$i][$j]) && $this->_evenOddStream[$i][$j] != 0 ) {
                        //Should be even
                        if( isset($this->_initialSudoku[$i][$j]) && $this->_initialSudoku[$i][$j] != 0 ) {
                             if($this->_initialSudoku[$i][$j] % 2 != 0) {
                                 return false;
                             }
                        }
                    } else {
                        //Should be Odd
                        if( isset($this->_initialSudoku[$i][$j]) && $this->_initialSudoku[$i][$j] != 0 ) {
                             if($this->_initialSudoku[$i][$j] % 2 == 0) {
                                 return false;
                             }
                        }                                            
                    }
               }
            }            
            
            return true;
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
 * Find and Fill the Hidden Single Cells. 
 * Hidden Single means the only Cell in the Row, Block or Column which allows the digit to be placed. 
 *
 * @return void
 * @access private
 */
	private function _fillCellsWithHiddenSingles() {
 
      $this->_checkRowsForHiddenSingles();
      $this->_checkColumnsForHiddenSingles();
      $this->_checkBlocksForHiddenSingles();
 }
 	
/**
 * Check Rows for Hidden Singles 
 *
 * @return void
 * @access private
 */
	private function _checkRowsForHiddenSingles() { 
	
           for($i = 0; $i < 9; $i++) {
              $candidates_not_to_check = array();
              foreach($this->_initialSudoku[$i] as $candidate) {
                  if($candidate != 0 ) {
                     $candidates_not_to_check[] = $candidate;
                  }
              }
              
              for($j = 1; $j <= 9; $j++) {
                  if(in_array($j, $candidates_not_to_check)) {
                       continue;
                  }
                  $potential_cells = 0;
                  $column_no = 0;
                  for($k = 0; $k < 9; $k++) {
                      if(isset($this->_candidates[$i][$k])) {
                          $key = array_search($j, $this->_candidates[$i][$k]);
                          if($key !== false) {
                              $potential_cells++;
                              $column_no = $k;
                              if($potential_cells > 1) {
                                  break;
                              }
                          }
                      }
                  }
                  if($potential_cells == 1) {
                      $this->_initialSudoku[$i][$column_no] = $j;
                      unset($this->_candidates[$i][$column_no]);
                      $this->needToGuess = false;
                      $candidates_not_to_check[] = $j;                      
                  }
              }
           }
	}

/**
 * Check Columns for Hidden Singles 
 *
 * @return void
 * @access private
 */
	private function _checkColumnsForHiddenSingles() { 
	

           for($i = 0; $i < 9; $i++) {
              $candidates_not_to_check = array();
              for($j = 0; $j < 9; $j++) { 
                  if($this->_initialSudoku[$j][$i] != 0 ) {
                     $candidates_not_to_check[] = $this->_initialSudoku[$j][$i];
                  }
              }
              
              for($j = 1; $j <= 9; $j++) {
                  if(in_array($j, $candidates_not_to_check)) {
                       continue;
                  }
                  $potential_cells = 0;
                  $row_no = 0;
                  for($k = 0; $k < 9; $k++) {
                      if(isset($this->_candidates[$k][$i])) {
                          $key = array_search($j, $this->_candidates[$k][$i]);
                          if($key !== false) {
                              $potential_cells++;
                              $row_no = $k;
                              if($potential_cells > 1) {
                                  break;
                              }
                          }
                      }
                  }
                  if($potential_cells == 1) {
                      $this->_initialSudoku[$row_no][$i] = $j;
                      unset($this->_candidates[$row_no][$i]);
                      $this->needToGuess = false;
                      $candidates_not_to_check[] = $j;                      
                  }
              }
           }
	} 		
	
/**
 * Check Blocks for Hidden Singles 
 *
 * @return void
 * @access private
 */
	private function _checkBlocksForHiddenSingles() { 
	
            for($x = 0; $x < 3; $x++) {
                for($y = 0; $y < 3; $y++) {
                    $rowStart = $x * 3;
                    $columnStart = $y * 3;
                    $candidates_not_to_check = array();
                    //Get Values of Cells which are already Filled
                    for($i = $rowStart; $i < $rowStart + 3; $i++) {
                        for($j = $columnStart; $j < $columnStart + 3; $j++) {
                            if( isset($this->_initialSudoku[$i][$j]) && $this->_initialSudoku[$i][$j] != 0 ) {
                                $candidates_not_to_check[] = $this->_initialSudoku[$i][$j];
                            }               
                        }
                    }

                    //Loop Through each number in the block and get no. of cells for which it is a potential candidate
                    for($k = 1; $k <= 9; $k++) {
                        if(in_array($k, $candidates_not_to_check)) {
                             continue;
                        }

                        $potential_cells = 0;
                        $column_no = 0;
                        $row_no = 0;
                                                                  
                        for($i = $rowStart; $i < $rowStart + 3; $i++) {
                            for($j = $columnStart; $j < $columnStart + 3; $j++) {
                                if( isset($this->_candidates[$i][$j])) {
                                    $key = array_search($k, $this->_candidates[$i][$j]);
                                    if($key !== false) {
                                        $potential_cells++;
                                        $row_no = $i;
                                        $column_no = $j;
                                        if($potential_cells > 1) {
                                            break;
                                        }
                                    }                                    
                                }               
                            }
                        }
                        
                        //If the Number is candidate at only one cell, then thats the cell
                        if($potential_cells == 1) {
                            $this->_initialSudoku[$row_no][$column_no] = $k;
                            unset($this->_candidates[$row_no][$column_no]);
                            $this->needToGuess = false;
                            $candidates_not_to_check[] = $k;                      
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
            $columnStart = floor( $column / 3 ) * 3;
     
            for($i = $rowStart; $i < $rowStart + 3; $i++) {
                for($j = $columnStart; $j < $columnStart + 3; $j++) {
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
 * Removes the candidates based on Even/Odd
 *
 * @return array
 * @access private
 */
	private function _removeEvenOddDuplicates($row, $column, $candidates) { 

            if($this->_evenOddStream[$row][$column] == 1) {
                $candidates = $this->_removeOddCandiates($candidates);
            } else {
                $candidates = $this->_removeEvenCandiates($candidates);                
            }
            return $candidates;
	}
	
/**
 * Removes the Odd Candidates
 *
 * @return array
 * @access private
 */
	private function _removeOddCandiates($candidates) { 
	

            foreach($candidates as $key => $candidate) {
               if($candidate % 2 != 0) {
                   unset($candidates[$key]);               
               }
            }
            return $candidates;
	}	

/**
 * Removes the Even Candidates
 *
 * @return array
 * @access private
 */
	private function _removeEvenCandiates($candidates) { 
	
            foreach($candidates as $key => $candidate) {
               if($candidate % 2 == 0) {
                   unset($candidates[$key]);               
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
            $even = '';
            if($this->_isSudokuX) {
                if($i == $j || $j == 8 - $i) {
                    $xx = 'xx';
                }
            }

            if($this->_isEvenOdd) {
                if($this->_evenOddStream[$i][$j] == 1) {
                    $even = 'even';
                }
            }            
            $html .= '<td width="20" height="20" class="grid r'.$i.' c'.$j.' '.$xx.' '.$even.'" id="grid'.$i.$j.'">'.$this->_initialSudoku[$i][$j].'</td>';
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
      
  }	
}
?>
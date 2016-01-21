<?php

/**
 * This is a PHP implementation of the Munkres's Algorithm inspired from 
 * http://csclab.murraystate.edu/bob.pilgrim/445/munkres.html
 * 
 * author: liqul@outlook.com
 */

final class MukresAlgorithm
{
    //cost matrix
    private $C = array();
    //mask matrix
    private $M = array();
    private $rowCover = array();
    private $colCover = array();
    private $C_orig = array();
    private $path = array();

    private $nrow = 0;
    private $ncol = 0;
    private $step = 1;
    private $path_row_0 = 0;
    private $path_col_0 = 0;
    private $path_count = 0;
    private $asgn = 0;
    private $debug = false;

    public function initData($aCostMatrix){
        $iSize = count($aCostMatrix);
        $this->nrow = $iSize;
        $this->ncol = $iSize;
        $this->C = $aCostMatrix;

        $this->M = array();
        for($i = 0; $i < $iSize; $i++){
            $this->M[] = array_pad(array(), $iSize, 0);
        }
        $this->rowCover = array_pad(array(), $iSize, 0);
        $this->colCover = array_pad(array(), $iSize, 0);

        $this->C_orig = array();
        $this->path = array();
        $this->step = 1;
        $this->path_row_0 = 0;
        $this->path_col_0 = 0;
        $this->path_count = 0;
        $this->asgn = 0;
    }

    private function showCostMatrix(){
        for($r = 0; $r < $this->nrow; $r++){
            $str = "";
            for($c = 0; $c < $this->ncol; $c++){
                $str .= $this->C[$r][$c] . ",";
            }
            $str = substr($str, 0, strlen($str) - 1) . "\n";
            echo $str;
        }
    }

    private function showRowCover(){
        $str = "row cover:";
        for($r = 0; $r < $this->nrow; $r++){
            $str .= $this->rowCover[$r] . ",";
        }
        $str = substr($str, 0, strlen($str) - 1) . "\n";
        echo $str;
    }

    private function showMaskMatrix(){
        for($r = 0; $r < $this->nrow; $r++){
            $str = "";
            for($c = 0; $c < $this->ncol; $c++){
                $str .= $this->M[$r][$c] . ",";
            }
            $str = substr($str, 0, strlen($str) - 1) . "\n";
            echo $str;
        }
    }

    private function showColCover(){
        $str = "col cover:";
        for($c = 0; $c < $this->ncol; $c++){
            $str .= $this->colCover[$c] . ",";
        }
        $str = substr($str, 0, strlen($str) - 1) . "\n";
        echo $str;
    }

    public function runMunkres(){
        $done = false;
        while(!$done){
            if($this->debug){
                $this->showCostMatrix();
                $this->showMaskMatrix();
            }
            switch ($this->step) {
                case 1:
                    $this->step_one();
                    break;
                case 2:
                    $this->step_two();
                    break;
                case 3:
                    $this->step_three();
                    break;
                case 4:
                    $this->step_four();
                    break;
                case 5:
                    $this->step_five();
                    break;
                case 6:
                    $this->step_six();
                    break;
                case 7:
                    $this->step_seven();
                    $done = true;
                    break;
            }
        }
        return $this->M;
    }

    private function step_one(){
        $min_in_row = 10000;
        for($r = 0; $r < $this->nrow; $r++){
            $min_in_row = $this->C[$r][0];
            for($c = 0; $c < $this->ncol; $c++){
                if($this->C[$r][$c] < $min_in_row){
                    $min_in_row = $this->C[$r][$c];
                }
            }
            for($c = 0; $c < $this->ncol; $c++){
                $this->C[$r][$c] -= $min_in_row;
            }
        }
        $this->step = 2;
        if($this->debug){
            echo "-------step1:step2-------\n";
        }
    }

    private function step_two(){
        for($r = 0; $r < $this->nrow; $r++){
            for($c = 0; $c < $this->ncol; $c++){
                if($this->C[$r][$c] == 0 &&
                        $this->rowCover[$r] == 0 &&
                        $this->colCover[$c] == 0){
                    $this->M[$r][$c] = 1;
                    $this->rowCover[$r] = 1;
                    $this->colCover[$c] = 1;
                }
            }
        }
        for($r = 0; $r < $this->nrow; $r++){
            $this->rowCover[$r] = 0;
        }
        for($c = 0; $c < $this->ncol; $c++){
            $this->colCover[$c] = 0;
        }
        $this->step = 3;
        if($this->debug){
            echo "-------step2:step3-------\n";
        }
    }

    private function step_three(){
        $colcount = 0;
        for($r = 0; $r < $this->nrow; $r++){
            for($c = 0; $c < $this->ncol; $c++){
                if($this->M[$r][$c] == 1){
                    $this->colCover[$c] = 1;
                }
            }
        }
        for($c = 0; $c < $this->ncol; $c++){
            if($this->colCover[$c] == 1){
                $colcount++;
            }
        }
        if($colcount >= $this->ncol ||
                $colcount >= $this->nrow){
            $this->step = 7;
            if($this->debug){
                echo "-------step3:step7-------\n";
            }
        }
        else{
            $this->step = 4;
            if($this->debug){
                echo "-------step3:step4-------\n";
            }
        }
    }

    private function find_a_zero(&$row, &$col){
        $r = 0;
        $c = 0;
        $done = false;
        $row = -1;
        $col = -1;
        while(!$done){
            $c = 0;
            while(true){
                if($this->C[$r][$c] == 0 &&
                        $this->rowCover[$r] == 0 &&
                        $this->colCover[$c] == 0){
                    $row = $r;
                    $col = $c;
                    $done = true;
                }
                $c++;
                if($c >= $this->ncol || $done){
                    break;
                }
            }
            $r++;
            if($r >= $this->nrow){
                $done = true;
            }
        }
    }

    private function star_in_row($row){
        $tmp = false;
        for($c = 0; $c < $this->ncol; $c++){
            if($this->M[$row][$c] == 1){
                $tmp = true;
            }
        }
        return $tmp;
    }

    private function find_star_in_row($row){
        $col = -1;
        for($c = 0; $c < $this->ncol; $c++){
            if($this->M[$row][$c] == 1){
                $col = $c;
            }
        }
        return $col;
    }

    private function step_four(){
        $row = -1;
        $col = -1;
        $done = false;
        while(!$done){
            $this->find_a_zero($row, $col);
            if($row == -1){
                $done = true;
                $this->step = 6;
                if($this->debug){
                    echo "-------step4:step6-------\n";
                }
            }else{
                $this->M[$row][$col] = 2;
                if($this->star_in_row($row)){
                    $col = $this->find_star_in_row($row);
                    $this->rowCover[$row] = 1;
                    $this->colCover[$col] = 0;
                }else{
                    $done = true;
                    $this->step = 5;
                    if($this->debug){
                        echo "-------step4:step5-------\n";
                    }
                    $this->path_row_0 = $row;
                    $this->path_col_0 = $col;
                }
            }
        }
    }

    private function find_star_in_col($c){
        $r = -1;
        for($i = 0; $i < $this->nrow; $i++){
            if($this->M[$i][$c] == 1){
                $r = $i;
            }
        }
        return $r;
    }

    private function find_prime_in_row($r){
        $c = -1;
        for($i = 0; $i < $this->ncol; $i++){
            if($this->M[$r][$i] == 2){
                $c = $i;
            }
        }
        return $c;
    }

    private function augment_path(){
        for($p = 0; $p < $this->path_count; $p++){
            if($this->M[$this->path[$p][0]][$this->path[$p][1]] == 1){
                $this->M[$this->path[$p][0]][$this->path[$p][1]] = 0;
            }
            else{
                $this->M[$this->path[$p][0]][$this->path[$p][1]] = 1;
            }
        }
    }

    private function clear_covers(){
        for($r = 0; $r < $this->nrow; $r++){
            $this->rowCover[$r] = 0;
        }
        for($c = 0; $c < $this->ncol; $c++){
            $this->colCover[$c] = 0;
        }
    }

    private function erase_primes(){
        for($r = 0; $r < $this->nrow; $r++){
            for($c = 0; $c < $this->ncol; $c++){
                if($this->M[$r][$c] == 2){
                    $this->M[$r][$c] = 0;
                }
            }
        }
    }

    private function step_five(){

        $done = false;
        $r = -1;
        $c = -1;
        $this->path_count = 1;
        $this->path[$this->path_count - 1][0] = $this->path_row_0;
        $this->path[$this->path_count - 1][1] = $this->path_col_0;

        while(!$done){

            $r = $this->find_star_in_col($this->path[$this->path_count - 1][1]);

            if($r > -1){
                $this->path_count++;
                $this->path[$this->path_count - 1][0] = $r;
                $this->path[$this->path_count - 1][1] = $this->path[$this->path_count - 2][1];
            }else{
                $done = true;
            }

            if(!$done){
                $c = $this->find_prime_in_row($this->path[$this->path_count - 1][0]);
                $this->path_count++;
                $this->path[$this->path_count - 1][0] = $this->path[$this->path_count - 2][0];
                $this->path[$this->path_count - 1][1] = $c;
            }

        }

        $this->augment_path();
        $this->clear_covers();
        $this->erase_primes();
        $this->step = 3;
        if($this->debug){
            echo "-------step5:step3-------\n";
        }
    }

    private function find_smallest(&$minval){
        for($r = 0; $r < $this->nrow; $r++){
            for($c = 0; $c < $this->ncol; $c++){
                if($this->rowCover[$r] == 0 &&
                        $this->colCover[$c] == 0){
                    if($minval > $this->C[$r][$c]){
                        $minval = $this->C[$r][$c];
                    }
                }
            }
        }
    }
    private function step_six(){
        $minval = 1000000;
        $this->find_smallest($minval);
        for($r = 0; $r < $this->nrow; $r++){
            for($c = 0; $c < $this->ncol; $c++){
                if($this->rowCover[$r] == 1){
                    $this->C[$r][$c] += $minval;
                }
                if($this->colCover[$c] == 0){
                    $this->C[$r][$c] -= $minval;
                }
            }
        }
        $this->step = 4;
        if($this->debug){
            echo "-------step6:step4-------\n";
        }
    }

    private function step_seven(){
        if($this->debug){
            echo "\n\n-------Run Complete--------";
        }
    }
}

?>




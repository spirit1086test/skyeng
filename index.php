<?php
function sum($a,$b)
{
    // является ли число отрицательным
    $is_minusA = substr($a, 0,1);
    $is_minusB = substr($b, 0,1);
    $withOutMinus_A = ($is_minusA=='-') ?  substr($a, 1)  : $a;
    $withOutMinus_B = ($is_minusB=='-') ?  substr($b, 1) : $b;
    
    
    // получаем длину чисел
    $aLen = strlen($withOutMinus_A);
    $bLen = strlen($withOutMinus_B);
    
    // узнаем у какого больше знаков
    if($aLen>$bLen)
    {
       $size = $aLen;
    }    
    elseif($aLen<$bLen)
    {
       $size = $bLen;
    }    
    else
    {
       $size = $aLen;
    }    
    
    
    $result = ''; // результат
    $offset = 0; // остатки
    $byZero=[];
    $minus = '';
    
    for($i=0;$i<$size;$i++)
    {
        $a = ($i>$aLen) ? 0 : substr($withOutMinus_A, 0,$aLen-$i);
        $b = ($i>$bLen) ? 0 : substr($withOutMinus_B, 0,$bLen-$i);    
        
        $numA = substr($a, -1); //у каждого числа берем значение с конца
        $numB = substr($b, -1);
       
        if( ($is_minusA!='-' && $is_minusB!='-') ||  ($is_minusA=='-' && $is_minusB=='-') )
        {
            $sum = $numA+$numB + $offset; // если есть остаток от предущего арифметич выражения 
            $minus = ($is_minusA=='-' && $is_minusB=='-') ? '-' : '';
        }  
        elseif($withOutMinus_A>$withOutMinus_B)
        {
            // если занимали десяток на предыдущем этапе
            if(in_array(1, $byZero))
            {
              $numA = $numA-1;
              $byZero=[];
            }        
            
            if($numA<$numB)
            {
                $numA+= 10;
                $byZero[]= 1;
            }    
            
            
            $sum = $numA-$numB;
            $minus = ($is_minusA=='-') ? $is_minusA : '';
        }
        elseif($withOutMinus_B>$withOutMinus_A)
        {
            // если занимали десяток на предыдущем этапе
            if(in_array(1, $byZero))
            {
              $numB = $numB-1;
              $byZero=[];
            }        
            
            if($numB<$numA)
            {
                $numB+= 10;
                $byZero[]= 1;
            }    
            
            $sum = $numB-$numA;
            $minus = ($is_minusB=='-') ? $is_minusB : '';
        } 
        
             // сохраняем остаток
            if(strlen($sum)>1)
            {
                // если не последняя арифметическая операция
                if(($size-1)>$i)
                {    
                  $offset = substr($sum, 0,1); //остаток
                  $number = substr($sum, 1); //число после арифметической операции
                }
                else
                {
                  $number = $sum;  
                }    
            }  
            else
            {
                $offset = 0;
                 //число после арифметической операции, если это последняя операция и значение равно нуль, не пишем его в результат
                $number = $sum;
            }    
       
            $result = $number.$result;
    }
    
    return $minus.ltrim($result,0);
}        


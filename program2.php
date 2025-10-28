<?php


function isPrime($num) {
   
    if ($num < 2) {
        return false;
    }
    
    
    for ($i = 2; $i <= sqrt($num); $i++) {
        if ($num % $i == 0) {
            return false; 
        }
    }
    
    return true; 
}


function findPrimeNums($start, $end) {
    $primes = [];
    
    for ($i = $start; $i <= $end; $i++) {
        if (isPrime($i)) {
            $primes[] = $i; 
        }
    }
    return $primes;
}


$start_range = 10;
$end_range = 50;


$prime_numbers = findPrimeNums($start_range, $end_range);


echo "Prime numbers between $start_range and $end_range are: ";







echo implode(", ", $prime_numbers);
?>
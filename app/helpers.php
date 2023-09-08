<?php 

function calculateCommissionHelper($totalReferrer)
{
    if($totalReferrer < 1){
        return 0;
    }elseif($totalReferrer >= 1 && $totalReferrer <= 4){
        return 5;
    }elseif($totalReferrer >= 5 && $totalReferrer <= 10){
        return 10;
    }elseif($totalReferrer >= 11 && $totalReferrer <= 20){
        return 15;
    }elseif($totalReferrer >= 21 && $totalReferrer <= 30){
        return 20;
    }elseif($totalReferrer >= 31){
        return 30;
    }
}
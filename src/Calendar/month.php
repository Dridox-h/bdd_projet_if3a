<?php

namespace Calendar;


class month {

    public $month;
    public $year; 
    private $months = [
        "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", 
        "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
    ];

    public $days = ["Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche"];
    public function __construct(?int $month = null, ?int $year = null)

    {
        if($month===null){
            $month = intval(date('n'));
        }
        if($year===null){
            $year = intval(date('Y'));
        }
        if($month<1 || $month >12){
            throw new \Exception("Le mois $month n'est pas valide");
        }
        if($year<1970 ){
            throw new \Exception("L'année n'est pas valide");
        }
        $this->month = $month;
        $this->year = $year;
    }

    public function toString() {
        return $this->months[$this->month-1].' '.$this->year;


    }

    public function getWeeks (){
        $start = new \DateTime("{$this->year}-{$this->month}-01");
        $end = (clone $start)->modify('+1 month -1 day'); 
        $weeks =  intval($end->format('W')) - intval($start->format('W'))+1;

        if($weeks<0){
            $weeks = intval($end->format('W'));
        }
        return $weeks;
    }

    public function getStartingDay() {
        return new \DateTime("{$this->year}-{$this->month}-01");
    }

    public function withinMonth($date){
        return $this->getStartingDay()->format('Y-m') === $date->format('Y-m');
        }

    public function nextMonth(){
        $month = $this->month+1;
        $year = $this->year;
        if ($month>12){
            $month = 1;
            $year +=1;
        }
        return new month($month,$year);
    }

    public function previousMonth(){
        $month = $this->month-1;
        $year = $this->year;
        if ($month<1){
            $month = 12;
            $year -=1;
        }
        return new month($month,$year);
    }

    
}


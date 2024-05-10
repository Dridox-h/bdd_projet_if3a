<?php

namespace Calendar;

class Event{

    private $id;
    private $name;
    private $start;

    private $end;

    private $descritpion;

    public function getId(){
        return $this->id;
    }
    public function getname(){
        return $this->name;
    }

    public function getDescription(){
        return $this->descritpion ?? '';
    }

    public function getstart(){
        return new \DateTime($this->start);
    }
    public function getend(){
        return new \DateTime($this->end);
    }


}
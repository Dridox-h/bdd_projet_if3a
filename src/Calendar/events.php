<?php

namespace Calendar;

class Events {

    private $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;

    }
    public function getEventBetween(\DateTime $start, \DateTime $end){
        $pdo = new \PDO("mysql:host=localhost;dbname=tennis","root","",[
            \PDO ::ATTR_ERRMODE => \PDO :: ERRMODE_EXCEPTION,
            \PDO :: ATTR_DEFAULT_FETCH_MODE =>\PDO::FETCH_ASSOC
        ]);

        $sql = "SELECT * FROM reservation WHERE start BETWEEN '{$start->format('Y-m-d 00:00:00')}' AND '{$end->format('Y-m-d 23:59:59')}' ";
        $statement = $this->pdo->query($sql);
        $results = $statement->fetchAll();
        return $results;
    }

    public function getEventBetweenByDay(\DateTime $start, \DateTime $end){
        $events = $this->getEventBetween($start,$end);
        $days = [];
        foreach($events as $event){
            $date = explode(' ',$event['start'])[0];
            if(!isset($days[$date])){
                $days[$date] = [$event];

            }else{
                $days[$date][]=$event;

            }
        }
        return $days;

    }

    public function find(int $id){
        require 'Event.php';
        $statement = $this->pdo->query("SELECT * FROM reservation WHERE id = $id LIMIT 1");
        $statement->setFetchMode(\PDO::FETCH_CLASS,\Calendar\Event::class);
        $result = $statement->fetch();

        if($result == false){
            throw new \Exception("aucun résultat n'a été trouvé");
        }
        return $result;
    }
}
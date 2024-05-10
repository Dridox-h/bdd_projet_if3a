<?php
require '../src/Calendar/events.php';
require '../src/bootstrap.php';

//dd($_GET);
$pdo = get_pdo();
$events = new Calendar\Events($pdo);

if(!isset($_GET['id'])){
    header('location : /404.php');
}

try{
    
    $event = $events->find($_GET['id']);
}catch (\Exception $e){
    e404();
}
require '../view/header.php';


dd($event);
?>

<h1><?php $event->getName(); ?></h1>

<ul>
    <li>Date : <?= $event->getstart()->format('d/m/Y'); ?></li>
    <li>Heure de démarrage : <?= $event->getstart()->format('H:i'); ?></li>
    <li>Heure de fin : <?= $event->getend()->format('H:i'); ?></li>
    <li>Description :<br>
     <?=  h($event->getDescription());?></li>



</ul>

<?php require '../view/footer.php'; ?>
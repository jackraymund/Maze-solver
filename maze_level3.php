<?php
header('Content-Type: text/html; charset=utf-8');

$mazeStructure = array
  (
  array(1,1,1,1,1),
  array(1,0,0,0,0),
  array(1,0,1,0,1),
  array(1,0,1,1,1)
  );
$entranceInput = array(1,3); 
$priceWall = 2;
$priceField = 2;
$priceTorch = 2;

try
  {
  $maze = new MazeAnalyzer;
  $maze->setAndValidMazeStructure($mazeStructure);
  $maze->setUpEntrance($entranceInput[0],$entranceInput[1]);
  $mazePrice = $maze->getCostOfCreating($priceWall, $priceField, $priceTorch);
  
  echo sprintf('Całkowity koszt wybudowania labiryntu to: %d', $mazePrice);
  // echo $maze->getNumberOfSectors();
  } 
catch (Exception $e) 
  {
  echo 'Caught exception: ',  $e->getMessage(), "\n", $e->getCode();
  }
 
function __autoload($className) 
  {
  include 'classes/class.'.$className . '.php';
  }





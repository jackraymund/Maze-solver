<?php
header('Content-Type: text/html; charset=utf-8');

$mazeStructure = array
  (
  array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
  array(1,0,0,0,0,1,0,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1),
  array(0,0,1,1,0,1,0,0,0,1,1,0,0,0,0,0,0,1,0,0,1,1),
  array(1,1,1,1,0,1,0,1,0,1,1,0,1,1,1,1,0,1,0,1,1,1),
  array(1,0,0,0,0,1,0,1,0,1,1,0,0,0,0,1,0,0,0,1,1,1),
  array(1,0,1,1,0,1,0,1,0,1,1,1,1,1,0,1,1,1,0,1,1,1),
  array(1,1,0,1,0,0,0,1,0,1,0,1,0,0,0,1,1,0,0,1,1,1),
  array(1,1,0,1,1,1,1,1,0,1,0,1,1,1,0,1,1,1,1,1,1,1),
  array(1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
  array(1,1,0,1,1,1,1,1,1,0,1,1,1,1,0,1,1,1,1,1,1,1),
  array(1,1,0,1,1,1,1,1,1,0,1,1,1,1,0,1,1,1,1,1,1,1),
  array(1,1,0,1,0,0,0,1,1,0,1,1,1,1,0,1,1,1,1,1,1,1),
  array(1,1,0,1,1,1,0,0,0,0,0,1,1,1,0,1,1,1,1,1,1,1),
  array(1,1,0,1,1,1,0,1,1,1,1,0,0,0,0,1,1,1,1,1,1,1),
  array(1,1,0,0,0,0,0,1,1,1,1,0,1,1,1,1,1,1,1,1,1,1),
  array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1)
  );  
$priceOfWall = 2;
$priceOfField = 2;
$priceOfTorch = 2;
$entranceInput = array(0,2); 
try
  {
  echo '<pre>';
  //level1
  $maze = new MazeAnalyzer;
  $maze->setAndValidMazeStructure($mazeStructure);
  $maze->countWallsAndFields();
  echo sprintf('Labirynt ma %d ścian i %d pól', $maze->getNumberOfWalls(), $maze->getNumberOfFields()). PHP_EOL;
  unset($maze);
  
  //level2
  $maze = new MazeAnalyzer;
  $maze->setAndValidMazeStructure($mazeStructure);
  $maze->setUpEntrance($entranceInput[0],$entranceInput[1]);
  $exitCord = $maze->getExitCords();
  echo sprintf('Wyjście z labiryntu znajduje się na X: %d Y: %d', $exitCord[0], $exitCord[1]). PHP_EOL;
  unset($maze);
  
  //level3
  $maze = new MazeAnalyzer;
  $maze->setAndValidMazeStructure($mazeStructure);
  $maze->setUpEntrance($entranceInput[0],$entranceInput[1]);
  $mazePrice = $maze->getCostOfCreating($priceOfWall, $priceOfField, $priceOfTorch);
  echo sprintf('Całkowity koszt wybudowania labiryntu to: %d', $mazePrice) . PHP_EOL;
  echo 'Liczba korytarzy ' . $maze->getNumberOfCorridors();
  unset($maze);
  
  //level4
  $maze = new MazeAnalyzer;
  $maze->setAndValidMazeStructure($mazeStructure);
  $maze->setUpEntrance($entranceInput[0],$entranceInput[1]);
  $roadToExit =  $maze->findExitOfMaze();
  
  echo '<pre>';
  print_r($roadToExit);
  
  
  
  } 
catch (Exception $e) 
  {
  echo 'Caught exception: ',  $e->getMessage(), "\n", $e->getCode();
  }
  
  

function __autoload($className) 
  {
  include 'classes/class.'.$className . '.php';
  }





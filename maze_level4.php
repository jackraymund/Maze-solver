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

try
  {
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





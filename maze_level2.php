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
  $exitCord = $maze->getExitCords();
  
  echo sprintf('Wyjście z labiryntu znajduje się na X: %d Y: %d', $exitCord[0], $exitCord[1]);
  } 
catch (Exception $e) 
  {
  echo 'Caught exception: ',  $e->getMessage(), "\n", $e->getCode();
  }

function __autoload($className) 
  {
  include 'classes/class.'.$className . '.php';
  }





<?php
header('Content-Type: text/html; charset=utf-8');
$urlFormatter = new Rewrite(dirname(__FILE__));
$urlFormatter->searchForScriptDirectory('maze');

var_dump($urlFormatter->args);

$mazeStructure = array
  (
  array(1,1,1,1,1,1,1,1,1,1),
  array(1,0,0,0,0,0,0,0,0,1),
  array(1,0,1,1,1,1,0,1,0,0),
  array(1,0,1,0,0,0,0,1,1,1),
  array(1,0,1,1,0,1,1,1,0,1),
  array(1,0,1,0,0,0,0,0,0,1),
  array(1,0,1,1,1,1,0,1,1,1)
  );
try
  {
  $maze = new MazeAnalyzer;
  $maze->setAndValidMazeStructure($mazeStructure);
  $maze->countWallsAndFields();
  
  echo sprintf('Labirynt ma %d ścian i %d kafelek', $maze->getNumberOfWalls(), $maze->getNumberOfFields());
  
  
  //2 level
  //input 
  $maze = new MazeAnalyzer;
  $maze->setAndValidMazeStructure($mazeStructure);
  $input = array(1,6);
  $maze->setUpEntrance($input[0],$input[1]);
  var_dump($maze->getExitCords());
  unset($maze);
  
  //level 3
  $maze = new MazeAnalyzer;
  // echo 'tu';
  $maze->setAndValidMazeStructure($mazeStructure);
  $input = array(1,6);
  $maze->setUpEntrance($input[0],$input[1]);
  

  var_dump($maze->getCostOfCreating(1, 1, 1));
  
  
  
  
  } 
catch (Exception $e) 
  {
  echo 'Caught exception: ',  $e->getMessage(), "\n", $e->getCode();
  }
  
  

function __autoload($className) 
  {
  include 'classes/class.'.$className . '.php';
  }





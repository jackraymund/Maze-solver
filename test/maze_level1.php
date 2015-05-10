<?php
header('Content-Type: text/html; charset=utf-8');

$mazeStructure = array
  (
  array(1,1,1,1,1),
  array(1,0,0,0,0),
  array(1,0,1,0,1),
  array(1,0,1,1,1)
  );
  
try
  {
  $maze = new MazeAnalyzer;
  $maze->setAndValidMazeStructure($mazeStructure);
  $maze->countWallsAndFields();
  
  echo sprintf('Labirynt ma %d ścian i %d kafelek', $maze->getNumberOfWalls(), $maze->getNumberOfFields());
  } 
catch (Exception $e) 
  {
  echo 'Caught exception: ',  $e->getMessage(), "\n", $e->getCode();
  }
  
function __autoload($className) 
  {
  include 'classes/class.'.$className . '.php';
  }





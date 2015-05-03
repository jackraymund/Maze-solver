<?php
ob_start();
if(ob_get_level() === 0)
  throw new Exception("Please activate output buffering",500);

try
  {
  $RESTManager = new RESTManager;
  if($RESTManager->checkIsAvalibleMazeRewrite() === false)
	  throw new Exception("There isn't request to maze",400);
  
  if($RESTManager->checkIfRequestIsPOST())
    {
	if($RESTManager->isValidNewMazeDataStructure())
	  {
	  $mazeId = $RESTManager->createNewMazeAndGetId();
	  echo $RESTManager->json_encode(array("mazeId" => $mazeId));
	  }
	}	
		
  if($RESTManager->checkIfRequestIsGET())
    {
	if($RESTManager->isValidMazeId() === false)
	  throw new Exception("Wrong maze id",400);
	  
	if($RESTManager->isRequestForDescribe())
	  {
	  $mazeStructure = $RESTManager->getMazeStructure();

	  $maze = new MazeAnalyzer;
	  $maze->setAndValidMazeStructure($mazeStructure);
	  $maze->countWallsAndFields();
	  
	  echo $RESTManager->json_encode
	   (
	    array
		  (
		  'walls' => $maze->getNumberOfWalls(),
		  'corridors' => $maze->getNumberOfFields()
		  )
		);
	  }
	  
	elseif($RESTManager->isRequestForExitCords())
	  {
	  $mazeStructure = $RESTManager->getMazeStructure();
	  $entranceInput = $RESTManager->getMazeEntrance();
	  
	  $maze = new MazeAnalyzer;
	  $maze->setAndValidMazeStructure($mazeStructure);
	  $maze->setUpEntrance($entranceInput[0],$entranceInput[1]);
	  $exitCord = $maze->getExitCords();
	  
	  echo $RESTManager->json_encode
	    (
		array
		  (
		  'exit' => 
		    array
		    (
		    $exitCord[0],
			$exitCord[1]
			)
		  )
		);
	  }
	elseif($RESTManager->isRequestForBuildMazePrice())
	  {
	  $RESTManager->validBuildMazePriceRequest();

	  $mazeStructure = $RESTManager->getMazeStructure();
	  $entranceInput = $RESTManager->getMazeEntrance();
	  $prices = $RESTManager->getBuildPrices();
	  
	  $maze = new MazeAnalyzer;
	  $maze->setAndValidMazeStructure($mazeStructure);
	  $maze->setUpEntrance($entranceInput[0],$entranceInput[1]);
	  $mazePrice = $maze->getCostOfCreating($prices['wallPrice'], $prices['corridorPrice'], $prices['torchPrice']);
	  
	  echo $RESTManager->json_encode(array('price' => $mazePrice));
	  } 
	  
	elseif($RESTManager->isRequestForPath())
	  {
	  $mazeStructure = $RESTManager->getMazeStructure();
	  $entranceInput = $RESTManager->getMazeEntrance();
	  
	  $maze = new MazeAnalyzer;
	  $maze->setAndValidMazeStructure($mazeStructure);
	  $maze->setUpEntrance($entranceInput[0],$entranceInput[1]);
	  $roadToExit =  $maze->findExitOfMaze();
	  
	  echo $RESTManager->json_encode(array('path' => $roadToExit));
	  }   
	}
	
  }
catch (Exception $e) 
  {
  ob_end_clean();//if not expected exception comment this line  
  header('HTTP/1.1 '.$e->getCode().' '.$e->getMessage());
  }	 
function __autoload($className) 
  {
  include 'classes/class.'.$className . '.php';
  }

?>
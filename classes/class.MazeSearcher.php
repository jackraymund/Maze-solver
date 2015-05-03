<?php

class MazeSearcher
  {
  const DEBUG_MODE = false;
  //TODO maximum steps backward on one dead end
  const BACKWARD_SEARCH_STEPS = 50;
  
  private $yAxisArrayLargestIndexValue,
		  $xAxisArrayLargestIndexValue,
		  $checkedBlackListfields,
		  $checkedfields,
		  $actualStepsBackward,
		  $traceToExitArray,
		  $quenedCorridors;
		  
  protected $entranceCordX,
		    $entranceCordY;	  
  public function __construct()
    {
	$this->entranceCordX = NULL;
	$this->entranceCordY = NULL;
	$this->yAxisArrayLargestIndexValue = NULL;
	$this->xAxisArrayLargestIndexValue = NULL;
	$this->checkedBlackListfields = NULL;
	$this->checkedfields = NULL;
	$this->actualStepsBackward = NULL;
	$this->traceToExitArray = NULL;
	$this->quenedCorridors = NULL;
    } 

  protected function getMazeNumberOfCorridors()
    {
	if(self::DEBUG_MODE) echo '<pre>';
	$this->checkIfMazeStructureExistOrThrowException();
	$this->setAxisesLargestIndexValues();
	
	$this->findEntranceIfNotExistOrThrowException();
	
	$this->checkedfields = array();
	$corridorsArray = array();

	if(self::DEBUG_MODE) echo 'Creating Corridor'.PHP_EOL;
	$actualCorridor = array
	  (
	  'aim' => '',
	  'cords' => array
		(
		array
		  (
		  'x' => $this->entranceCordX,
		  'y' => $this->entranceCordY
		  )
		)
	  );
	  
	
	$this->addToCheckedfieldList($this->entranceCordX, $this->entranceCordY);
	$this->quenedCorridors = array();
	
	$end = false;
	while(!$end)
	  {
	  $actualPositionX = $actualCorridor['cords'][count($actualCorridor['cords'])-1]['x'];
	  $actualPositionY = $actualCorridor['cords'][count($actualCorridor['cords'])-1]['y'];
	  $this->addToCheckedfieldList($actualPositionX, $actualPositionY);
	  $ways = $this->getAvalibleWays($actualPositionX, $actualPositionY);
	  if($actualCorridor['aim'] === '')
		  {
		  if(isset($ways['left']))
		    $actualCorridor['aim'] = 'left';
		  elseif(isset($ways['right']))
			$actualCorridor['aim'] = 'right';
	      elseif(isset($ways['bottom']))
			$actualCorridor['aim'] = 'bottom';
		  elseif(isset($ways['top']))
			$actualCorridor['aim'] = 'top';
		  }
	
	  if(count($ways) > 1)
		{
		if(isset($ways['left']) and isset($ways['right']))
		  {
		  if($actualCorridor['aim'] !== 'right' and $actualCorridor['aim'] !== 'left')
			{
			$uniqId = uniqid();
			//to prevent repeart unique id
			usleep(1);
		  
			$this->addToQueneCorridor('left', $actualPositionX, $actualPositionY,$uniqId);
			$this->addToQueneCorridor('right', $actualPositionX, $actualPositionY,$uniqId);
			$this->addToCheckedfieldList($actualPositionX, $actualPositionY);
		  
			if(self::DEBUG_MODE) echo 'Add to Quene(left/right) ', $actualPositionX,' ', $actualPositionY,"<br>";
			}
		  }
		elseif($actualCorridor['aim'] !== 'left' and isset($ways['left']))
		  {
		  $this->addToQueneCorridor('left', $actualPositionX, $actualPositionY);
		  $this->addToCheckedfieldList($actualPositionX, $actualPositionY);
			
		  if(self::DEBUG_MODE) echo 'Add to Quene(left) ', $actualPositionX,' ', $actualPositionY,"<br>";
		  }
		elseif($actualCorridor['aim'] !== 'right' and isset($ways['right']))
		  {
		  $this->addToQueneCorridor('right', $actualPositionX, $actualPositionY);
		  $this->addToCheckedfieldList($actualPositionX, $actualPositionY);
			
		  if(self::DEBUG_MODE) echo 'Add to Quene(right) ', $actualPositionX,' ', $actualPositionY,"<br>";
		  }
		  
		if(isset($ways['top']) and isset($ways['bottom']))
		  {
		  if($actualCorridor['aim'] !== 'top' and $actualCorridor['aim'] !== 'bottom')
			{
			$uniqId = uniqid();
			//to prevent repeart unique id
			usleep(1);

			$this->addToQueneCorridor('top', $actualPositionX, $actualPositionY,$uniqId);
			$this->addToQueneCorridor('bottom', $actualPositionX, $actualPositionY,$uniqId);
			$this->addToCheckedfieldList($actualPositionX, $actualPositionY);

			if(self::DEBUG_MODE) echo 'Add to Quene(top/bottom) ', $actualPositionX,' ', $actualPositionY,"<br>";
			}
		  }
		elseif($actualCorridor['aim'] !== 'top' and isset($ways['top']))
		  {
		  $this->addToQueneCorridor('top', $actualPositionX, $actualPositionY); 
		  $this->addToCheckedfieldList($actualPositionX, $actualPositionY);
			
		  if(self::DEBUG_MODE) echo 'Add to Quene(top) ', $actualPositionX,' ', $actualPositionY,"<br>";
		  }			  
		elseif($actualCorridor['aim'] !== 'bottom' and isset($ways['bottom']))
		  {
		  $this->addToQueneCorridor('bottom', $actualPositionX, $actualPositionY); 
		  $this->addToCheckedfieldList($actualPositionX, $actualPositionY);
			  
		  if(self::DEBUG_MODE) echo 'Add to Quene(bottom) ', $actualPositionX,' ', $actualPositionY,"<br>";
		  }
		
		}
		if($ways === false)
		  {
		  $corridorsArray[] = $actualCorridor;
		  if(count($this->quenedCorridors) > 0)
		    {
			$actualCorridor = $this->quenedCorridors[count($this->quenedCorridors)-1];
			unset($this->quenedCorridors[count($this->quenedCorridors)-1]);
			$this->quenedCorridors = array_values($this->quenedCorridors);
			}
		  else
		    {
			$bannedId = array();
			$arrayRange = count($corridorsArray);
			for($i = 0;$i < $arrayRange;$i++)
			  {
			  if((isset($corridorsArray[$i]['destinationSectionId']) and !isset($bannedId[$corridorsArray[$i]['destinationSectionId']]))
			  or count($corridorsArray[$i]['cords']) == 1)
			    {
				unset($corridorsArray[$i]);
				continue;
				}
				
			  }
			$corridorsArray = array_values($corridorsArray);
			
			while($this->cleanSectionFromRepearts($corridorsArray))
			  {
			  $corridorsArray = array_values($corridorsArray);
			  }
			// var_dump($corridorsArray);
			return count($corridorsArray);
			}
		  }
		elseif(isset($ways[$actualCorridor['aim']]))
		  {
		  if(self::DEBUG_MODE) echo 'Add cords to corridor ', $actualCorridor['aim'], ' ',$actualPositionX,' ', $actualPositionY, "<br>";
		  $actualCorridor['cords'][] = $ways[$actualCorridor['aim']];
		  
		  $this->addToCheckedfieldList($actualPositionX, $actualPositionY);
		  }
		elseif(!isset($ways[$actualCorridor['aim']]))
		  {
		  if(self::DEBUG_MODE) echo 'Make new corridor ' ,$actualPositionX,' ', $actualPositionY, "<br>";
		  $this->addToCheckedfieldList($actualPositionX, $actualPositionY);
		  $corridorsArray[] = $actualCorridor;
		  $actualCorridor = array
		    (
			'aim' => '',
			'cords' => array
			  (
			  array
				(
				'x' => $actualPositionX,
				'y' => $actualPositionY
				)
			  )
		    );
		  }

		}
	}
  public function cleanSectionFromRepearts(&$aCorridorsArray)
    {
	$arrayRange = count($aCorridorsArray);
	$xToCheck = NULL;
	$yToCheck = NULL;
	for($i = 0;$i < $arrayRange;$i++)
	  {
	  $xToCheck = $aCorridorsArray[$i]['cords'][0]['x'];
	  $yToCheck = $aCorridorsArray[$i]['cords'][0]['y'];
	  for($z = 0;$z < $arrayRange;$z++)
		{
		if($i != $z)
		  {
		  if($xToCheck === $aCorridorsArray[$z]['cords'][0]['x'] and $yToCheck === $aCorridorsArray[$z]['cords'][0]['y'] )
		    {
			unset($aCorridorsArray[$z]);
		    return true;
			}
		  }
		}
	  
	  }
	  
	$aCorridorsArray = array_values($aCorridorsArray);
	return false;
	}
  private function addToQueneCorridor($aCorridorAim, $aX, $aY, $aDestinationSectionId = false)
    {
	$this->quenedCorridors[] = array
		(
		'aim' => $aCorridorAim,
		'cords' => array
		  (
		  array
			(
			'x' => $aX,
			'y' => $aY
			)
		  )
		);
	if($aDestinationSectionId != false)
	  $this->quenedCorridors[count($this->quenedCorridors)-1]['destinationSectionId'] = $aDestinationSectionId;
	return count($this->quenedCorridors)-1;
	}
  private function getAvalibleWays($aActualPositionX, $aActualPositionY)
	{
	$avalibleWays = array();
	//right
	if( $cords = $this->checkifIsAvalibeStepOnRight($aActualPositionX, $aActualPositionY) )
	  {
	  if(!$this->checkIfCordsAreOnCheckedfieldList($cords['x'], $cords['y']))
		$avalibleWays['right'] = $cords;
	  }
	//left
	if( $cords = $this->checkifIsAvalibeStepOnLeft($aActualPositionX, $aActualPositionY) )
	  {
	  if(!$this->checkIfCordsAreOnCheckedfieldList($cords['x'], $cords['y']))
	    $avalibleWays['left'] = $cords;
	  }
	//top 
	if( $cords = $this->checkifIsAvalibeStepOnTop($aActualPositionX, $aActualPositionY) )
	  {
	  if(!$this->checkIfCordsAreOnCheckedfieldList($cords['x'], $cords['y']))
		$avalibleWays['top'] = $cords;
	  }
	//bottom
	if( $cords = $this->checkifIsAvalibeStepOnBottom($aActualPositionX, $aActualPositionY) )
	  {
	  if(!$this->checkIfCordsAreOnCheckedfieldList($cords['x'], $cords['y']))
		$avalibleWays['bottom'] = $cords;
	  }
	if(count($avalibleWays) > 0)
	  return $avalibleWays;
	return false;
	}
	
  private function getAllfieldsCords()
    {
	$cords = array();
	for($x = 0;$x < count($this->mazeStructure);$x++)
	  {
	  for($y = 0;$y < count($this->mazeStructure[$x]);$y++)
	  if($this->mazeStructure[$x][$y] === 0)
	    $cords[$y][$x] = '';
	  }
	return $cords;
	}

  
  private function findEntranceIfNotExistOrThrowException()
    {
	if($this->entranceCordX === NULL and $this->entranceCordY === NULL)
	  if($cords = $this->findExitOrEntranceToMaze())
		$this->setUpEntrance($cords['x'], $cords['y']);
	  else
	    throw new Exception("Can't find exit/entrance to maze",400);
	}
  private function findExitOrEntranceToMaze()
    {
	for($i = 0;$i < count($this->mazeStructure);$i++) 
	  {
	  //search on left maze edge
	  if($this->mazeStructure[$i][0] === 0)
	    return array
		  (
		  'x' => 0,
		  'y' => $i
		  );
	  //search on right maze edge
	  if($this->mazeStructure[$i][$this->xAxisArrayLargestIndexValue] === 0)
	    return array
		  (
		  'x' => $this->xAxisArrayLargestIndexValue,
		  'y' => $i
		  );
	  }
	
	for($i = 0; $i < count($this->mazeStructure[0]);$i++)
	  {
	  //search at top maze edge
	  if($this->mazeStructure[0][$i] === 0)
		return array
		  (
		  'x' => $i,
		  'y' => 0
		  );
	  //search at bottom maze edge 
	  if($this->mazeStructure[$this->yAxisArrayLargestIndexValue][$i] === 0)
		return array
		  (
		  'x' => $i,
		  'y' => $this->yAxisArrayLargestIndexValue
		  );
	  }
	return false;
	}
	
	
  
  private function addToBlackList($aX,$aY)
    {
	$this->checkedBlackListfields[$aY][$aX] = '';
	}
  private function checkIfCordsAreOnBlackList($aX, $aY)
    {
	return isset($this->checkedBlackListfields[$aY][$aX]);
	}	
  private function addToCheckedfieldList($aX,$aY)
    {
	$this->checkedfields[$aY][$aX] = '';
	}
  private function checkIfCordsAreOnCheckedfieldList($aX, $aY)
    {
	return isset($this->checkedfields[$aY][$aX]);
	}	
  
   private function setAxisesLargestIndexValues()
    {
	$this->yAxisArrayLargestIndexValue = count($this->mazeStructure)-1; 
	$this->xAxisArrayLargestIndexValue = count($this->mazeStructure[0])-1;
	}
  private function checkIfEntranceCordsExistOrThrowException()
	{
	if($this->entranceCordX === NULL or $this->entranceCordY === NULL)
	  throw new Exception('Entrance cords arent set!',400);
	}
  	
  public function findExitOfMaze()
	{
	if(self::DEBUG_MODE) echo '<pre>';
	$this->checkIfMazeStructureExistOrThrowException();
	$this->checkIfEntranceCordsExistOrThrowException();
	$this->setAxisesLargestIndexValues();
	
	//final road to exit
	$this->traceToExitArray = array();
	//fields pernamently banned from search
    $this->checkedBlackListfields = array();
	//fields "used" at search, prevents bot to dont go backward
	$this->checkedfields = array();
	//actual steps maked backward on dead end
	$this->actualStepsBackward = 0;
	
	//set starting search position
	$actualPositionX = $this->entranceCordX;
	$actualPositionY = $this->entranceCordY;

	//add entrance cords to blacklist
	$this->addToBlackList($actualPositionX,$actualPositionY);
	//add entrance cords to trace
	$this->addfieldToTraceArray($actualPositionX,$actualPositionY);
	while(!$this->checkIfCordsAreExit($actualPositionX, $actualPositionY)) 
	  {
	  if(!$this->oneStepSearchForEntrance($actualPositionX, $actualPositionY))
	    {
		if(self::DEBUG_MODE) echo 'black listed ' . $actualPositionX .' '. $actualPositionY .PHP_EOL;
		
		//add to blackList
		$this->addToBlackList($actualPositionX,$actualPositionY);
		
		//make step backward
		if($this->actualStepsBackward < self::BACKWARD_SEARCH_STEPS and count($this->traceToExitArray) > 0)
		  {
		  if(self::DEBUG_MODE) echo 'make step backward'.PHP_EOL;
		  //make step backward
		  $actualPositionX = $this->traceToExitArray[count($this->traceToExitArray)-2][0];
		  $actualPositionY= $this->traceToExitArray[count($this->traceToExitArray)-2][1];
		  $this->actualStepsBackward++;
		  //delete last step
		  unset($this->traceToExitArray[count($this->traceToExitArray)-1]);
		  //sort array indexes
		  $this->traceToExitArray = array_values($this->traceToExitArray);
		  }
		else
		  {
		  $this->actualStepsBackward = 0;
		  //search from beginning
		  $this->traceToExitArray = array();
		  $this->checkedfields = array();
		  
		  $actualPositionX = $this->entranceCordX;
		  $actualPositionY = $this->entranceCordY;
		  }
		}
	  else
	    {
		$this->actualStepsBackward = 0;
		}
	  }
	if($this->checkIfCordsAreExit($actualPositionX, $actualPositionY))
	  {
	  if(self::DEBUG_MODE) echo 'Found exit at '.$actualPositionX . ' '.$actualPositionY . PHP_EOL;
	  return $this->traceToExitArray;
	  }
	}
  private function addfieldToTraceArray($aX, $aY)
    {
	$this->traceToExitArray[] = array($aX,$aY);
	}
	
  private function checkifIsAvalibeStepOnLeft($aActualPositionX, $aActualPositionY)
    {
	$isAvalibleAreaToMakeStepOnLeft = ($aActualPositionX > 0);
	
	if($isAvalibleAreaToMakeStepOnLeft)
	  {
	  $ifAreaIsfield = ($this->mazeStructure[$aActualPositionY][$aActualPositionX-1] === 0); 
	  if($ifAreaIsfield)
	  return array
	    (
		'x' => $aActualPositionX-1,
		'y' => $aActualPositionY
		);
	  }
	return false;
	}
  private function checkifIsAvalibeStepOnRight($aActualPositionX, $aActualPositionY)
    {
	$isAvalibleAreaToMakeStepOnRight = (($aActualPositionX < $this->xAxisArrayLargestIndexValue));
	
	
	
	if( $isAvalibleAreaToMakeStepOnRight )
	  {
	  $ifAreaIsfield = ($this->mazeStructure[$aActualPositionY][$aActualPositionX+1] === 0);
	  if( $ifAreaIsfield )
		return array
	      (
		  'x' => $aActualPositionX+1,
		  'y' => $aActualPositionY
		  );
	  }
	return false;
	}
  private function checkifIsAvalibeStepOnTop($aActualPositionX, $aActualPositionY)
    {
	$isAvalibleAreaToMakeStepOnTop = ($aActualPositionY > 0);
	$ifAreaIsfield = ($this->mazeStructure[$aActualPositionY-1][$aActualPositionX] === 0);  
	if( $ifAreaIsfield and $isAvalibleAreaToMakeStepOnTop)
	  return array
	    (
		'x' => $aActualPositionX,
		'y' => $aActualPositionY-1
		);
	return false;
	}
  private function checkifIsAvalibeStepOnBottom($aActualPositionX, $aActualPositionY)
    {
	$isAvalibleAreaToMakeStepOnBottom = ($aActualPositionY < $this->yAxisArrayLargestIndexValue);
	
	  
	if( $isAvalibleAreaToMakeStepOnBottom )
	  {
	  $ifAreaIsfield = ($this->mazeStructure[$aActualPositionY+1][$aActualPositionX] === 0);
	  if( $ifAreaIsfield )
		return array
		  (
		  'x' => $aActualPositionX,
		  'y' => $aActualPositionY+1
		  );
	  }
	return false;
	}
  private function addCordToCheckedfieldAndTraceList($aX, $aY)
    {
	$this->addToCheckedfieldList($aX,$aY);
	$this->addfieldToTraceArray($aX,$aY);
	}
  private function oneStepSearchForEntrance(&$aActualPositionX, &$aActualPositionY)
    {
	//right
	if($this->checkifIsAvalibeStepOnRight($aActualPositionX, $aActualPositionY) and
	  !$this->checkIfCordsAreOnCheckedfieldList($aActualPositionX+1, $aActualPositionY) and
	  !$this->checkIfCordsAreOnBlackList($aActualPositionX+1, $aActualPositionY) 
	  )
	  {
	  $this->addCordToCheckedfieldAndTraceList($aActualPositionX+1, $aActualPositionY);
	  //makes step
	  $aActualPositionX++;
	  if(self::DEBUG_MODE) echo 'Found right' . PHP_EOL;
	  return true;
	  }
	//left
	if($this->checkifIsAvalibeStepOnLeft($aActualPositionX, $aActualPositionY) and 
	  !$this->checkIfCordsAreOnCheckedfieldList($aActualPositionX-1, $aActualPositionY) and 
	  !$this->checkIfCordsAreOnBlackList($aActualPositionX-1, $aActualPositionY)
	  )
	  {
	  $this->addCordToCheckedfieldAndTraceList($aActualPositionX-1, $aActualPositionY);
	  //makes step
	  $aActualPositionX--;
	  if(self::DEBUG_MODE) echo 'Found left' . PHP_EOL;
	  return true;
	  }
	//top 
	if($this->checkifIsAvalibeStepOnTop($aActualPositionX, $aActualPositionY) and 
	  !$this->checkIfCordsAreOnCheckedfieldList($aActualPositionX, $aActualPositionY-1) and 
	  !$this->checkIfCordsAreOnBlackList($aActualPositionX, $aActualPositionY-1)
	  )
	  {
	  $this->addCordToCheckedfieldAndTraceList($aActualPositionX, $aActualPositionY-1);
	  //makes step
	  $aActualPositionY--;
	  if(self::DEBUG_MODE) echo 'Found top' . PHP_EOL;
	  return true;
	  }
	//bottom
	if($this->checkifIsAvalibeStepOnBottom($aActualPositionX, $aActualPositionY) and 
	  !$this->checkIfCordsAreOnCheckedfieldList($aActualPositionX, $aActualPositionY+1) and 
	  !$this->checkIfCordsAreOnBlackList($aActualPositionX, $aActualPositionY+1)
	  )
	  {
	  $this->addCordToCheckedfieldAndTraceList($aActualPositionX, $aActualPositionY+1);
	  //makes step
	  $aActualPositionY++;
	  if(self::DEBUG_MODE) echo 'Found bottom' . PHP_EOL;
	  return true;
	  }
	
	return false;
	}
	
  public function checkIfCordsAreExit($aActualPositionX, $aActualPositionY)
    {
	$isXAxisPositionIsOnRightEdge = ($aActualPositionX === $this->xAxisArrayLargestIndexValue);
	$isYAxisPositionIsOnBottomEdge = ($aActualPositionY === $this->yAxisArrayLargestIndexValue);
	$isXAxisPositionIsOnLeftEdge = ($aActualPositionY === 0);
	$isYAxisPositionIsOnTopEdge = ($aActualPositionX === 0 );
	//is on any edge
	if($isXAxisPositionIsOnRightEdge or $isYAxisPositionIsOnTopEdge
	  or $isXAxisPositionIsOnLeftEdge or $isYAxisPositionIsOnBottomEdge)
	  {
	  $isFoundXPositionAreEntrancePosition = ($aActualPositionX === $this->entranceCordX);
	  $isFoundYPositionAreEntrancePosition = ($aActualPositionY === $this->entranceCordY);
	  //if exit cords are equal to entrance cords
	  if($isFoundXPositionAreEntrancePosition and $isFoundYPositionAreEntrancePosition)
		return false;
		
	  return true;
	  }
	return false;
	}
	
  }
  
 ?>
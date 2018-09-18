<?php

class MazeSearcher
  {
  const DEBUG_MODE = false;
  //maximum steps backward on one dead end
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
    $this->drawDebugMessage('<pre>');
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
  private function drawDebugMessage($aMsg)
    {
    if(self::DEBUG_MODE)
      echo $aMsg .PHP_EOL;
    }
  protected function getMazeNumberOfCorridors()
    {
    //check if needed variables are set
    $this->checkIfMazeStructureExistOrThrowException();
    $this->checkIfEntranceCordsExistOrThrowException();

    $this->setAxisesLargestIndexValues();

    $corridorsArray = array();
    $this->checkedfields = array();

    $this->drawDebugMessage('Creating first corridor');
    //setting first corridor
    $actualCorridor =
    array
      (
      'aim' => '',
      'cords' =>
      array
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
  
  while(true)
    {
    $actualPositionX = $actualCorridor['cords'][count($actualCorridor['cords'])-1]['x'];
    $actualPositionY = $actualCorridor['cords'][count($actualCorridor['cords'])-1]['y'];
    $this->addToCheckedfieldList($actualPositionX, $actualPositionY);
    $avalibleWays = $this->getAvalibleWays($actualPositionX, $actualPositionY);

    if($actualCorridor['aim'] === '')
    {
    if(isset($avalibleWays['left']))
      $actualCorridor['aim'] = 'left';
    elseif(isset($avalibleWays['right']))
      $actualCorridor['aim'] = 'right';
    elseif(isset($avalibleWays['bottom']))
      $actualCorridor['aim'] = 'bottom';
    elseif(isset($avalibleWays['top']))
      $actualCorridor['aim'] = 'top';
    }

    $ifAvalibleMoreWaysThanOne = (count($avalibleWays) > 1);
    if($ifAvalibleMoreWaysThanOne)
    {
    $isAvalibleHorizontalWay = (isset($avalibleWays['left']) and isset($avalibleWays['right']));
    if($isAvalibleHorizontalWay)
      {
      $isActualCorridorWayAreVertical = ($actualCorridor['aim'] == 'top' or $actualCorridor['aim'] == 'bottom');
      if($isActualCorridorWayAreVertical)
      {
      $uniqId = uniqid();
      //to prevent repeart unique id
      usleep(1);
        //appending both ways to quene
      $this->addToQueneCorridor('left', $actualPositionX, $actualPositionY,$uniqId);
      $this->addToQueneCorridor('right', $actualPositionX, $actualPositionY,$uniqId);
      $this->addToCheckedfieldList($actualPositionX, $actualPositionY);

        $this->drawDebugMessage('Add to Quene(left/right) '. $actualPositionX.' '. $actualPositionY);
      }
      }
    elseif($actualCorridor['aim'] !== 'left' and isset($avalibleWays['left']))
      {
      $this->addToQueneCorridor('left', $actualPositionX, $actualPositionY);
      $this->addToCheckedfieldList($actualPositionX, $actualPositionY);

      $this->drawDebugMessage('Add to Quene(left) '. $actualPositionX.' '. $actualPositionY);
      }
    elseif($actualCorridor['aim'] !== 'right' and isset($avalibleWays['right']))
      {
      $this->addToQueneCorridor('right', $actualPositionX, $actualPositionY);
      $this->addToCheckedfieldList($actualPositionX, $actualPositionY);

      $this->drawDebugMessage('Add to Quene(right) '. $actualPositionX.' '. $actualPositionY);
      }
    $isAvalibleVerticalWay = (isset($avalibleWays['top']) and isset($avalibleWays['bottom']));
    if($isAvalibleVerticalWay)
      {
      $isActualCorridorWayAreHorizontal = ($actualCorridor['aim'] == 'left' and $actualCorridor['aim'] == 'right');
      if($isActualCorridorWayAreHorizontal)
      {
      $uniqId = uniqid();
      //to prevent repeart unique id
      usleep(1);
      //appending both ways to quene
      $this->addToQueneCorridor('top', $actualPositionX, $actualPositionY,$uniqId);
      $this->addToQueneCorridor('bottom', $actualPositionX, $actualPositionY,$uniqId);
      $this->addToCheckedfieldList($actualPositionX, $actualPositionY);

      $this->drawDebugMessage('Add to Quene(top/bottom) '. $actualPositionX.' '. $actualPositionY);
      }
      }
    elseif($actualCorridor['aim'] !== 'top' and isset($avalibleWays['top']))
      {
      $this->addToQueneCorridor('top', $actualPositionX, $actualPositionY);
      $this->addToCheckedfieldList($actualPositionX, $actualPositionY);

      $this->drawDebugMessage('Add to Quene(top) '. $actualPositionX.' '. $actualPositionY);
      }
    elseif($actualCorridor['aim'] !== 'bottom' and isset($avalibleWays['bottom']))
      {
      $this->addToQueneCorridor('bottom', $actualPositionX, $actualPositionY);
      $this->addToCheckedfieldList($actualPositionX, $actualPositionY);

      $this->drawDebugMessage('Add to Quene(bottom) '. $actualPositionX.' '. $actualPositionY);
      }

    }
    $isNoAvalibleWays = $avalibleWays === false;
    if($isNoAvalibleWays)
      {
      //add corridor
      $corridorsArray[] = $actualCorridor;
      //start researching quened corridors
      if(count($this->quenedCorridors) > 0)
        {
      $actualCorridor = $this->quenedCorridors[count($this->quenedCorridors)-1];
      unset($this->quenedCorridors[count($this->quenedCorridors)-1]);
      $this->quenedCorridors = array_values($this->quenedCorridors);
      }
      //is no avalible quened corridors
      else
        {

      //delete repeartings corridors
      $arrayRange = count($corridorsArray);
      for($i = 0;$i < $arrayRange;$i++)
        {
        $isTwinsSectionsExist = (isset($corridorsArray[$i]['destinationSectionId']));
        $isOnlyOneCordsAtSection = (count($corridorsArray[$i]['cords']) == 1);
        if($isTwinsSectionsExist or $isOnlyOneCordsAtSection)
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
      return count($corridorsArray);

      }
      }
    elseif(isset($avalibleWays[$actualCorridor['aim']]))
      {
      //adds cords to actual corridor
      $this->drawDebugMessage('Add cords to corridor ' . $actualCorridor['aim'] . ' ' . $actualPositionX . ' ' . $actualPositionY);
      $actualCorridor['cords'][] = $avalibleWays[$actualCorridor['aim']];

      $this->addToCheckedfieldList($actualPositionX, $actualPositionY);
      }
    elseif(!isset($avalibleWays[$actualCorridor['aim']]))
      {
      $this->drawDebugMessage('Creating new corridor ' . $actualPositionX . ' ' . $actualPositionY);
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
    if($i != $z)
      if($xToCheck === $aCorridorsArray[$z]['cords'][0]['x'] and $yToCheck === $aCorridorsArray[$z]['cords'][0]['y'] )
        {
      unset($aCorridorsArray[$z]);
        return true;
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
    $this->drawDebugMessage('Black listed ' . $actualPositionX . ' ' . $actualPositionY);

    //add to blackList
    $this->addToBlackList($actualPositionX,$actualPositionY);

    //make step backward
    if($this->actualStepsBackward < self::BACKWARD_SEARCH_STEPS and count($this->traceToExitArray) > 0)
      {
      $this->drawDebugMessage('Make step backward');
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
    $this->drawDebugMessage('Found exit at '.$actualPositionX . ' '.$actualPositionY);
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
    $this->drawDebugMessage('Found right');
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
    $this->drawDebugMessage('Found left');
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
    $this->drawDebugMessage('Found top');
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
    $this->drawDebugMessage('Found bottom');
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

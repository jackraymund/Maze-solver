<?php
class MazeAnalyzer extends MazeSearcher
  {
  private $walls = NULL,
    $fields = NULL,
    $numberOfCorridors = NULL;
  protected $mazeStructure = NULL;

  public function getNumberOfCorridors()
    {
    if($this->numberOfCorridors !== NULL)
      return $this->numberOfCorridors;
    else
      throw new Exception("No data about number of corridors in maze.",400);
    }
  public function getCostOfCreating($aWallPrice, $aFieldPrice, $aTorchPrice)
    {
    $this->checkIfPriceIsNumberOrThrowException($aWallPrice);
    $this->checkIfPriceIsNumberOrThrowException($aFieldPrice);
    $this->checkIfPriceIsNumberOrThrowException($aTorchPrice);

    $this->countWallsAndfields();
    $overAllWallPrice = $this->walls * $aWallPrice;
    $overAllFieldPrice = $this->fields * $aFieldPrice;

    $this->numberOfCorridors = $this->getMazeNumberOfCorridors();

    if($this->numberOfCorridors % 2 == 1)
      $overAllTorchPrice = (($this->numberOfCorridors-1)/2)*$aTorchPrice;
    else
      $overAllTorchPrice = $this->numberOfCorridors*$aTorchPrice;
    return round($overAllTorchPrice+$overAllWallPrice+$overAllFieldPrice, 2);
    }
  private function checkIfPriceIsNumberOrThrowException($aValue)
    {
    $floatVal = floatval($aValue);
    $isNotFloatVal = $floatVal === 0;

    $intVal = intval($aValue);
    $isNotIntVal = $intVal === 0;

    if($isNotFloatVal or $isNotIntVal)
	  throw new Exception("Price isn't float or int.",400);
	}
  public function getExitCords()
    {
	$roadToExit = $this->findExitOfMaze();
	$lastfieldPosition = count($roadToExit)-1;
	$exitCords = array($roadToExit[$lastfieldPosition][0],$roadToExit[$lastfieldPosition][1]);
	return $exitCords;
	}

  public function getNumberOfWalls()
    {
	return $this->walls;
	}
  public function getNumberOffields()
    {
	return $this->fields;
	}

  public function countWallsAndfields()
    {
	$this->checkIfMazeStructureExistOrThrowException();
	$numberOfWalls = 0;
	$numberOffields = 0;
    foreach($this->mazeStructure as $xAxisArray)
	  {
	  foreach($xAxisArray as $rowValue)
	    {
		if($rowValue === 1)
		  $numberOfWalls++;
		elseif($rowValue === 0)
		  $numberOffields++;
		}
	  }

	$this->walls = $numberOfWalls;
	$this->fields = $numberOffields;
	}

  //Maze validating
  public function setAndValidMazeStructure($aMazeStructure)
    {

	MazeValidator::validMazeOrThrowException($aMazeStructure);
	$this->mazeStructure = $aMazeStructure;
	}
  protected function checkIfMazeStructureExistOrThrowException()
    {
	if($this->mazeStructure == NULL)
	  throw new Exception("Maze aren't set.",400);
	}
  public function setUpEntrance($aX, $aY)
    {
	MazeValidator::validEntranceOrThrowException($this->mazeStructure, $aX, $aY);
	$this->entranceCordX = $aX;
	$this->entranceCordY = $aY;
	}
  }

<?php
class MazeValidator
  {
  public static function validEntranceOrThrowException($aMazeStructure, $aX, $aY)
	{
	$isCordsareInMazeStructure = isset($aMazeStructure[$aY][$aX]);
	if($isCordsareInMazeStructure)
	  {
	  $isEntranceOnWall = $aMazeStructure[$aY][$aX] == 1;
	  if($isEntranceOnWall)
	    throw new Exception('Entrance is a wall!',400);
	  }

	$ifEntranceCordIsOnLeftOrTopEdge = ($aY == 0 or $aX == 0);
	$ifEntranceCordIsOnRightOrBottomEdge = ($aY == count($aMazeStructure)-1 or $aX == count($aMazeStructure[0])-1);
	if(!$ifEntranceCordIsOnLeftOrTopEdge and !$ifEntranceCordIsOnRightOrBottomEdge)
	  throw new Exception('Bad entrance cords!',400);

	return true;
	}
   static public $mazeStructure = NULL;
   public static function validMazeOrThrowException($aMazeStructure)
    {
	self::$mazeStructure = $aMazeStructure;
	self::checkIfMazeStrucureIsValidArrayOrThrowException();
	self::checkIfFieldsAreValidOrThrowException();
	self::checkMazeSizeOrThrowException();
	self::checkIfMazeIsSquareOrThrowException();
	}
  private static function checkIfMazeStructureExistOrThrowException()
    {
	if(self::$mazeStructure == NULL)
	  throw new Exception("Maze aren't set.",400);
	}
  private static function checkIfMazeStrucureIsValidArrayOrThrowException()
    {
	//checking only first values
	$isNotYAxisArrayExist = !is_array(self::$mazeStructure);
	$isNotXAxisArrayExist = !is_array(self::$mazeStructure[0]);
	if($isNotYAxisArrayExist or $isNotXAxisArrayExist)
	  throw new Exception('Not valid maze!',400);
	}
  private static function checkMazeSizeOrThrowException()
    {
	//maze must have atleast one dimension greater than 2 to be maze
	$isXAxisIsGreaterThanTwo = count(self::$mazeStructure[0]) > 2;
	$isYAxisIsGreaterThanTwo = count(self::$mazeStructure) > 2;
	if(!$isXAxisIsGreaterThanTwo and !$isYAxisIsGreaterThanTwo)
	  throw new Exception('Maze is too small!',400);
	}
  private static function checkIfMazeIsSquareOrThrowException()
    {
	$firstXAxisCount = count(self::$mazeStructure[0]);
	foreach(self::$mazeStructure as $xAxisArray)
	  if($firstXAxisCount != count($xAxisArray))
		throw new Exception("Maze isn't square!",400);
	}
  private static function checkIfFieldsAreValidOrThrowException()
    {
	foreach(self::$mazeStructure as $xAxisArray)
	  foreach($xAxisArray as $area)
		{
		$isAreaIsDifferentThanWallOrField = ($area !== 1 and $area !== 0);
		if($isAreaIsDifferentThanWallOrField)
		  throw new Exception('Wrong fields!',400);
		}
	}
  }
 ?>
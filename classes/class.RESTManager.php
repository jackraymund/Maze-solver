<?php
class RESTManager extends PDOobject
  {
  public $rewriteArgs;
  public $lastMysqlRequest;
  public function __construct()
    {
    $urlFormatter = new Rewrite(dirname(__FILE__));
    $urlFormatter->searchForScriptDirectoryAndDeleteWrongArgs('maze');
    $this->rewriteArgs = $urlFormatter->args;

    $this->connectToDataBase();
    }
  public static function sendHttpError($aCode, $aMessage)
    {
    header('HTTP/1.1 '.$aCode.' '.$aMessage);
    }
  public function json_encode($arrayToEncode)
    {
    return json_encode($arrayToEncode,JSON_NUMERIC_CHECK);
    }

  public function checkIsAvalibleMazeRewrite()
    {
    $isAvalibleMazeRewrite = (isset($this->rewriteArgs[0]) and $this->rewriteArgs[0] == 'maze');
    return $isAvalibleMazeRewrite;
    }

  public function isValidNewMazeDataStructure()
    {
    return isset($_POST['maze']) and isset($_POST['entrance']);
    }

  public function createNewMazeAndGetId()
    {
    $queryString = 'INSERT INTO  `'.parent::DBNAME.'`.`'.parent::MAZETABLE.'` (`maze_body` ,`maze_entrance`) VALUES ( :MAZE_BODY,  :MAZE_ENTRANCE )';

    $queryData = array
      (
      'MAZE_BODY' => $this->json_encode($_POST['maze']),
      'MAZE_ENTRANCE' => $this->json_encode($_POST['entrance'])
      );
    if($this->runQuery($queryString, $queryData))
      return $this->returnLastInsertedId();
    else
      throw new Exception("Cannot create new maze",400);

    }

  public function isValidMazeId()
    {
    $mazeId = (int)$this->rewriteArgs[1];
    if($mazeId === 0)
      return false;
    $queryString = 'SELECT * FROM  `'.parent::MAZETABLE.'` WHERE  `maze_id` = :MAZE_ID';
    $queryData = array
      (
      'MAZE_ID' => $mazeId
      );
    $this->runQuery($queryString, $queryData);
    $this->fetchLastQuery();
    if($this->getCountOfRecordsReturnedFromLastQuery() === false)
      return false;
    return true;
    }
  public function validBuildMazePriceRequest()
    {
    $wallPrice = $_GET['wallPrice'];
    $corridorPrice = $_GET['corridorPrice'];
    $torchPrice = $_GET['torchPrice'];
    if(isset($wallPrice) and $this->checkIfValueIsFloatAndFix($wallPrice))
      if(isset($corridorPrice) and $this->checkIfValueIsFloatAndFix($corridorPrice))
        if(isset($torchPrice) and $this->checkIfValueIsFloatAndFix($torchPrice))
          return true;
    throw new Exception("Bad price value or value is missing",400);


    }
  private function checkIfValueIsFloatAndFix(&$aValue)
    {
    $aValue = floatval($aValue);
    if($aValue !== 0)
      return true;
    else
      return false;
    }
  public function getMazeStructure()
    {
    //call first isValidMazeId() !
    $queryFetch = $this->getLastQueryFetch();
    return json_decode($queryFetch['maze_body']);
    }
 public function getMazeEntrance()
    {
    //call first isValidMazeId() !
    $queryFetch = $this->getLastQueryFetch();
    return json_decode($queryFetch['maze_entrance']);
    }
  public function getBuildPrices()
    {
    return array
      (
      'wallPrice' => $_GET['wallPrice'],
      'corridorPrice' => $_GET['corridorPrice'],
      'torchPrice' => $_GET['torchPrice']
      );
    }
  public function checkIfRequestIsGET()
    {
    $ifRequestIsGET = ($_SERVER['REQUEST_METHOD'] === 'GET');
    return $ifRequestIsGET;
    }
  public function checkIfRequestIsPOST()
    {
    $ifRequestIsPOST = ($_SERVER['REQUEST_METHOD'] === 'POST');
    return $ifRequestIsPOST;
    }

  public function isRequestForDescribe()
    {
    if($this->rewriteArgs[2] == 'describe')
      return true;
    return false;
    }
  public function isRequestForExitCords()
    {
    if($this->rewriteArgs[2] == 'exit')
      return true;
    return false;
    }
  public function isRequestForBuildMazePrice()
    {
    if($this->rewriteArgs[2] == 'quotation')
      return true;
    return false;
    }
  public function isRequestForGetMazeTrace()
    {
    if($this->rewriteArgs[2] == 'quotation')
      return true;
    return false;
    }
  public function isRequestForPath()
    {
    if($this->rewriteArgs[2] == 'path')
      return true;
    return false;
    }
  }
 ?>

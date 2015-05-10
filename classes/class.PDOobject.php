<?php
class PDOobject 
  { 
  const DBHOST = 'localhost';
  const DBLOGIN = 'xx';
  const DBPASS = 'xx';
  const DBNAME = 'allegro';
  const MAZETABLE = 'allegro_maze';
  
  private $PDO;
  private $lastStatmentQuery, $lastQueryFetch;
  public function connectToDataBase()
    {
    // Conntect and return PDO object.
    try
	  {
      $this->PDO = new PDO
        (
        'mysql:host='. self::DBHOST .';port=3306;dbname='. self::DBNAME,
        self::DBLOGIN,
        self::DBPASS,
        array
          (
          PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
          )
        );
      $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
    catch(PDOException $ex)
	  {
      throw new Exception("Cannot connect to db",500);
      }
    }
  
  private function PDOBindArray(&$poStatement, &$paArray)
    {
    foreach ($paArray as $k=>$v)
	  {
      $poStatement->bindValue(':'.$k,$v);
      }
    }
	
  public function runQuery($aStatement, $aData)
    {
    try
	  {
      $query = $this->PDO->prepare($aStatement);
      $this->PDOBindArray($query, $aData);
      $result = $query->execute();
	  
	  $this->lastStatmentQuery = $query;
	  return $result;
	  }
	catch(PDOException $ex)
	  {
	  throw new Exception("Cannot execute mysql query",400);
      }
    }
	
  public function fetchLastQuery()
    {
	$this->lastQueryFetch = $this->lastStatmentQuery->fetch();
	return $this->lastQueryFetch;
	}
	
  public function getLastQueryFetch()
    {
	return $this->lastQueryFetch;
	}
	
  public function getCountOfRecordsReturnedFromLastQuery()
    {
	return $this->lastStatmentQuery->rowCount();
	}
  
  public function returnLastInsertedId()
    {
	return $this->PDO->lastInsertId();
	}
}
?>

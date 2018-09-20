<?php
class Rewrite
  {
  public $args = array();
  public function searchForScriptDirectoryAndDeleteWrongArgs($aFolderName)
    {
    $rewriteSize = count($this->args);
    //delete get params from last arg
    $this->args[$rewriteSize-1] = strtok($this->args[$rewriteSize-1], '?');

    for($i = 0;$i < $rewriteSize;$i++)
      {
      if($this->args[$i] != $aFolderName)
        unset($this->args[$i]);
      else
        {
        $this->args = array_values($this->args);
        return true;
        }
      }

   }
  public function __construct($FILE)
    {
    $url_all = trim($_SERVER['REQUEST_URI'],'/');
    $file_name = str_replace(__DIR__ . DIRECTORY_SEPARATOR,null,$FILE);
    $dir = str_replace($file_name,null,$_SERVER['SCRIPT_NAME']);
    $dir = substr($dir,1);
    $url_all = str_replace($dir,null,$url_all);
    $this->args = explode('/',$url_all);
    }
 }
?>

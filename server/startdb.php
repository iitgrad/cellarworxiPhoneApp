<?php
$dom = new DomDocument();
$dom->load($_SERVER['DOCUMENT_ROOT']."/cellarworx/cellarworxConfig.xml");
$path=$dom->getElementsByTagName("path")->item(0)->textContent;
$username=$dom->getElementsByTagName("username")->item(0)->textContent;
$password=$dom->getElementsByTagName("password")->item(0)->textContent;
$name=$dom->getElementsByTagName("name")->item(0)->textContent;

 $db = mysql_pconnect($path, $username, $password);
  if (!$db)
  {
     echo 'Error: Could not connect to database.  Please try again later.';
     exit;
  }

   mysql_select_db($name);


class Timer
{
	var $timerstart=0;
	var $timerend=0;
	
	function resetTimer()
	{
		$this->timerstart=0;
	}
	
	function startTimer()
	{
		$thetime=explode(' ',microtime());
		$this->timerstart=$thetime[0]+$thetime[1];
	}
	
	function stopTimer()
	{
		$thetime=explode(' ',microtime());
		$this->timerend=$thetime[0]+$thetime[1];			
	}
	
	function getTime()
	{
		$thetime=explode(' ',microtime());
		$this->timerend=$thetime[0]+$thetime[1];			
		return $this->timerend-$this->timerstart;
	}
	
	function resetAndStartTimer()
	{
		$this->resetTimer();
		$this->startTimer();
	}
	
	function stopAndGetTime()
	{
		$this->stopTimer();
		return $this->getTime();
	} 
}

	

?>

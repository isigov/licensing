<?php
  if (!file_exists("settings.php")) {
      return;
  }
  include("settings.php");
  
  class SQLDB
  {
      var $connection;
      
      function SQLDB()
      {
          $db = mysql_database;
          $this->connection = mysql_connect(mysql_server, mysql_username, mysql_password) or die(mysql_error());
          mysql_select_db(mysql_database) or die(mysql_error());
          $check = mysql_query("SELECT * FROM `$db`.`users` LIMIT 0,1");
          if (!$check) {
              mysql_query("CREATE TABLE `users` (`username` varchar(32) primary key,`password` varchar(32), `email` varchar(32),`hardware` varchar(32),`message` varchar(200),  `banned` int(1) unsigned not null, `ipaddress` varchar(42), `apps` varchar(999), `banreason` varchar(200), `approved` int(1) unsigned not null);", $this->connection);
          }
          $check2 = mysql_query("SELECT * FROM `$db`.`admincp` LIMIT 0,1");
          if (!$check2) {
              mysql_query("CREATE TABLE `admincp` (`ipaddress` varchar(32) primary key);", $this->connection);
          }
          $check3 = mysql_query("SELECT * FROM `$db`.`update` LIMIT 0,1");
          if (!$check3) {
              mysql_query("CREATE TABLE `update` (`version` varchar(100), `link` varchar(100), `optional` int(1) unsigned not null, `message` varchar(10000), `app` varchar(32), `allow` int(1) unsigned not null, `date` varchar(30) primary key);", $this->connection);
          }
          $check4 = mysql_query("SELECT * FROM `$db`.`apps` LIMIT 0,1");
          if (!$check4) {
              mysql_query("CREATE TABLE `apps` (`name` varchar(32) primary key);", $this->connection);
          }
          $check5 = mysql_query("SELECT * FROM `$db`.`msg` LIMIT 0,1");
          if (!$check5) {
              mysql_query("CREATE TABLE `msg` (`globalmsg` varchar(10000) primary key, `date` varchar(30));", $this->connection);
          }
          $check6 = mysql_query("SELECT * FROM `$db`.`settings` LIMIT 0,1");
          if (!$check6) {
              mysql_query("CREATE TABLE `settings` (`allowCP` int(1) unsigned not null primary key, `allowHWID` int(1) unsigned not null, `noHWID` int(1) unsigned not null, `approve` int(1) unsigned not null);", $this->connection);
          }
      }
      
      function updateUserField($username, $field, $value)
      {
          $db = mysql_database;
          $q = "UPDATE `$db`.`users` SET `" . $field . "` = '$value' WHERE `username` = '$username'";
          return mysql_query($q, $this->connection);
      }
      function removeUser($username)
      {
          $db = mysql_database;
          $q = "DELETE FROM `$db`.`users` WHERE `username` = '$username'";
          mysql_query($q, $this->connection);
      }
      function removeNews($date)
      {
          $db = mysql_database;
          $q = "DELETE FROM `$db`.`msg` WHERE `date` = '$date'";
          mysql_query($q, $this->connection);
      }
      function removeApp($username)
      {
          $db = mysql_database;
          $q = "DELETE FROM `$db`.`apps` WHERE `name` = '$username'";
          mysql_query($q, $this->connection);
      }
      function getUserCount()
      {
          $q = mysql_query("Select username, approved From users", $this->connection);
          $count = "0";
          while ($r = mysql_fetch_assoc($q)) {
              if ($r['approved'] == "0") {
                  $count += 1;
              }
          }
          return $count;
      }
      function IsLoginCorrect($username, $password, $hardware, $app)
      {
          $db = mysql_database;
          $q = "SELECT username, password, hardware, message, banned, apps, banreason, approved FROM `$db`.`users` WHERE `username` = '$username'";
          $q2 = "SELECT globalmsg, date FROM `$db`.`msg` ORDER BY date DESC LIMIT 1";
          $response = mysql_query($q, $this->connection);
          $response2 = mysql_query($q2, $this->connection);
          if (mysql_num_rows($response) > 0) {
	      $countme = "0";
              if (mysql_num_rows($response2) > 0) {
                  $countme = "1";
              }
              $response = mysql_fetch_assoc($response);
              $response2 = mysql_fetch_assoc($response2);
	      $q = "SELECT allowCP, allowHWID, noHWID From `$db`.`settings`";
	      $q = mysql_query($q, $this->connection);
	      $r = mysql_fetch_assoc($q);
	      if($r['noHWID'] == 0){
	      $hardware = $response['hardware'];
	      }
              if ($response['password'] == $password && $response['hardware'] == $hardware && strstr($response['apps'], $app)) {
		  if($response['approved'] == 0) {
		  return "unapproved:";		
		  }
		  if($response['banned'] == 1) {
                  return "banned:" . $response['banreason'];
              	  }
                  if (mysql_num_rows($response2) > 0) {
                      return "correct:gmsg:" . $response2['globalmsg'] . ":" . $response2['date'];
                  } else {
                      return "correct";
                  }
              } else {
                  return "error";
              }
          } else {
              return "error";
          }
      }
      function UserCPLogin($username, $password)
      {
          $db = mysql_database;
          $q = "SELECT username, password, message, banned, banreason, approved FROM `$db`.`users` WHERE `username` = '$username'";
          $q2 = "SELECT date, globalmsg FROM `$db`.`msg` ORDER BY DATE DESC LIMIT 1";
          $response = mysql_query($q, $this->connection);
          $response2 = mysql_query($q2, $this->connection);
          if (mysql_num_rows($response) > 0) {
              $countme = "0";
              if (mysql_num_rows($response2) > 0) {
                  $countme = "1";
              }
              $response = mysql_fetch_assoc($response);
              $response2 = mysql_fetch_assoc($response2);
              if ($response['password'] == $password) {
		  if($response['approved'] == 0) {
		  return "unapproved:";		
		  }
                  if ($response['banned'] == 1) {
                      return "banned:" . $response['banreason'];
                  }
                  if ($response['approved'] == "0") {
                      return "banned:" . $response['banreason'];
                  }
                  if ($countme > 0) {
                      return "correct:gmsg:" . $response2['globalmsg'] . ":" . $response2['date'];
                  } else {
                      return "correct";
                  }
              } else {
                  return "error:";
              }
          } else {
              return "error:";
          }
      }
      function CheckUser($username)
      {
          $db = mysql_database;
          $q = "SELECT `username` FROM `$db`.`users` WHERE `username` = '$username'";
          $result = mysql_query($q, $this->connection);
          if (mysql_num_rows($result) > 0)
              return true;
          else
              return false;
      }
      function CheckApp($username)
      {
          $db = mysql_database;
          $q = "SELECT `name` FROM `$db`.`apps` WHERE `name` = '$username'";
          $result = mysql_query($q, $this->connection);
          if (mysql_num_rows($result) > 0)
              return true;
          else
              return false;
      }
      
      function AddUser($username, $password, $email, $hardware, $message, $banned, $ipadd, $app)
      {
          $db = mysql_database;
          $q = "INSERT INTO `$db`.`users` (`username`, `password`, `email`, `hardware`, `message`, `banned`, `ipaddress`, `apps`, `approved`) VALUES ('$username', '$password', '$email','$hardware', '$message', '0', '$ipadd', '$app', 1);";
          mysql_query($q, $this->connection);
      }
      function AddApp($name)
      {
          $db = mysql_database;
          $q = "INSERT INTO `$db`.`apps` (`name`) VALUES ('$name');";
          mysql_query($q, $this->connection);
      }
      function AddMessage($text)
      {
          $db = mysql_database;
          $today = date("j/n/Y g:i:s a");
          $q = "INSERT INTO `$db`.`msg` (`globalmsg`, `date`) VALUES ('$text', '$today');";
          mysql_query($q, $this->connection);
      }
      function CheckAdmin($username, $password)
      {
          if ($username == admin_username && $password == admin_password) {
              return true;
          } else {
              return false;
          }
      }
  }
  
  $sql = new SQLDB;
?>
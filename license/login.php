<?
$user = "";
if(!file_exists("settings.php"))
{
echo "<DIV ALIGN=CENTER>Please run the installer <a href=\"install.php\">here</a> to install DarkSel Licensing.</DIV>";
return;
}
include("settings.php");
include("database.php");
$db = mysql_database;
$display = false;
$q = "SELECT allowCP, allowHWID From `$db`.`settings`";
$q = mysql_query($q, $sql->connection);
$r = mysql_fetch_assoc($q);
if($r['allowCP'] == "0")
{
echo "<DIV ALIGN=CENTER>User CP login is currently disabled, please contact the administrator.</a></DIV>";
return;
}
if($_REQUEST['logout'] == "true")
{
if($_COOKIE['DL2010U'] == null)
{
echo "<DIV ALIGN=CENTER>You weren't logged in! How am I supposed to log you out?</a></DIV>";
return;
}
else
{
setcookie("DL2010U", "", time()-3600, "/");
echo "<DIV ALIGN=CENTER>You've logged out successfully!<br><a href=\"login.php\">Click here to continue</a></DIV>";
return;
}
}
if($_POST['action'] == "make")
{
$user = mysql_real_escape_string($_POST['user']);
$pass = mysql_real_escape_string($_POST['pass']);
$hwid = mysql_real_escape_string($_POST['hwid']);
$email = mysql_real_escape_string($_POST['email']);
$app = mysql_real_escape_string($_POST['app']);
$db = mysql_database;
$q = "SELECT allowCP, allowHWID, approve From `$db`.`settings`";
$q = mysql_query($q, $sql->connection);
$r = mysql_fetch_assoc($q);
if($r['approve'] == "0")
{
echo "<title>UserCP - DarkSel Licensing 2011</title><DIV ALIGN=CENTER><FONT color=#ff0000>Creation of new accounts is currently prohibited!</FONT></DIV><br>";
}
else
{
if($sql->CheckApp($app) == false)
{
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot add user " . "\"" . $user . "\"" .": that app name doesn't exist!</FONT></DIV><br>";
}
else
{
$app = $app . "; ";
if($sql->CheckUser($user))
 {
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot add user " . "\"" . $user . "\"" .": username already exists</FONT></DIV><br>";
 }
 else
 {
$sql->AddUser($user, $pass, $email,$hwid, "", 0, "", $app);
$sql->updateUserField($user, "approved", 0);
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully added user " . "\"" . $user . "\"" ."</FONT></DIV><br>";
 }
}
}
}
if($_POST['action'] == "login")
{
$user = mysql_real_escape_string($_POST['user']);
$pass = mysql_real_escape_string($_POST['pass']);
$hwid = mysql_real_escape_string($_POST['hwid']);
$app = mysql_real_escape_string($_POST['app']);
$response = "";
if($_POST['type'] == "applogin")
{
$response = $sql->IsLoginCorrect($user, $pass, $hwid, $app);
}
else
{
$response = $sql->UserCPLogin($user, $pass);
}
$response = explode(":", $response);
if($response[0] == "error")
{
echo "<title>UserCP - DarkSel Licensing 2011</title><DIV ALIGN=CENTER><FONT color=#ff0000>Login was unsuccessful, please check your username and password.</FONT></DIV><br>";
}
if($response[0] == "banned")
{
echo "<title>UserCP - DarkSel Licensing 2011</title><DIV ALIGN=CENTER><FONT color=#ff0000>You have been banned for: " . $response[1] . "</FONT></DIV><br>";
}
if($response[0] == "unapproved")
{
echo "<title>UserCP - DarkSel Licensing 2011</title><DIV ALIGN=CENTER><FONT color=#ff0000>Your account must be approved by an Administrator before you can login!</FONT></DIV><br>";
}
if($response[0] == "correct")
{
setcookie("DL2010U", $user . "." . $pass .".". $_SERVER['REMOTE_ADDR'], time()+3600, "/");
echo "<title>Welcome " . $user . "! - DarkSel Licensing 2011</title>" . "<DIV ALIGN=CENTER>Welcome " . $user . "!<br>You've logged in successfully!<br><a href=\"login.php\">Click here to continue</a></DIV>";
return;
}
}

if($_COOKIE['DL2010U'] != null)
{
$cookie = $_COOKIE['DL2010U'];
$cookie = explode(".", $cookie);
$user = mysql_real_escape_string($cookie[0]);
$pass = mysql_real_escape_string($cookie[1]);
$ip = mysql_real_escape_string($cookie[2]);
$response = $sql->UserCPLogin($user, $pass);
$response = explode(":", $response);
if($response[0] == "error")
{
setcookie("DL2010U", "", time()-3600, "/");
}
if($response[0] == "banned")
{
setcookie("DL2010U", "", time()-3600, "/");
}
if($response[0] == "correct")
{
if($_POST['action'] == "changepw")
{
$newpass = mysql_real_escape_string($_POST['pass']);
$sql->updateUserField($user, "password", $newpass);
setcookie("DL2010U", "", time()-2600, "/");
echo "<DIV ALIGN=CENTER>Successfully changed your password!<br><a href=\"login.php\">Click here to continue</a></DIV>";
return;
}
if($_POST['action'] == "changehwid")
{
$hwid = mysql_real_escape_string($_POST['hwid']);
$db = mysql_database;
$q = "SELECT allowCP, allowHWID From `$db`.`settings`";
$q = mysql_query($q, $sql->connection);
$r = mysql_fetch_assoc($q);
if($r['allowHWID'] == "0")
{
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Administrator has disabled HWID changes.</FONT></DIV>";
}
else
{
$sql->updateUserField($user, "hardware", $hwid);
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully updated your Hardware ID.</FONT></DIV>";
}
}
if($_POST['action'] == "clear")
{
$sql->updateUserField($user, "message", "");
echo "<DIV ALIGN=CENTER>Successfully cleared your personal message!<br><a href=\"login.php\">Click here to continue</a></DIV>";
return;
}
$db = mysql_database;
$q = "SELECT username, ipaddress FROM `$db`.`users` WHERE `username` = '$user'";
$q = mysql_query($q, $sql->connection);
$q = mysql_fetch_assoc($q);
$ip = $q['ipaddress'];
echo "<div align=right>Welcome " . $user . "!<br>Click <a href=\"login.php?logout=true\">here</a> to log out.<br>Last Login from: " . $ip . "</div>";
$sql->updateUserField($user, "ipaddress", $_SERVER['REMOTE_ADDR']);



if($_REQUEST['passwd'] == "true")
{
?>
<title>UserCP - DarkSel Licensing 2011</title>
<style type="text/css">
body
{
background-color:#d0e4fe;
}
</style>
<DIV ALIGN=CENTER>
<strong>Change Password:</strong>
</DIV>
<form action="login.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Password:</td><td><input type="password" name="pass" maxlength="30" value="">
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="changepw">
<input type="submit" value="Change"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=login.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_POST['passwd'] != "")
{
?>
<title>UserCP - DarkSel Licensing 2011</title>
<style type="text/css">
body
{
background-color:#d0e4fe;
}
</style>
<form action="login.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Password:</td><td><input type="password" name="pass" maxlength="30" value="">
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="changepw">
<input type="submit" value="Change"></td></tr>
</table>
</form>
?>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=login.php>Back To Menu</a>
</DIV>
<?
$display=true;
}
if($_POST['viewversion'] != "")
{
?>
<title>UserCP - DarkSel Licensing 2011</title>
<style type="text/css">
body
{
background-color:#d0e4fe;
}
</style>
<DIV ALIGN=CENTER>
<strong>Version History:</strong>
</DIV>
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td><b>Date</b></td><td><b>Version</b></td><td><b>Download Link</b></td><td><b>Description</b></td><td><b>Application</b></td><td><b>Optional</b></td>
<?
$appname = $_POST['viewversion'];
$q = mysql_query("SELECT version, link, optional, message, app, allow, date From `$db`.`update` WHERE app = '$appname'", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
$link = "Not available";
if($r['allow'] == 1)
{
$link = "<a href=" . $r['link'] . ">" . $r['link'] . "</a>";
}
$optional = "No";
if($r['optional'] == 1)
{
$optional = "Yes";
}
echo "<tr><td>" . $r['date'] . "</td><td>" . $r['version'] . "</td><td>" . $link . "</td><td>" . nl2br($r['message']) . "</td><td>"  . $r['app'] . "</td><td>" . $optional. "</td>";
}
echo "</table></form>";
?>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=login.php>Back To Menu</a>
</DIV>
<?
$display=true;
}
if($_REQUEST['appinfo'] == "true")
{
?>
<title>UserCP - DarkSel Licensing 2011</title>
<style type="text/css">
body
{
background-color:#d0e4fe;
}
</style>
<DIV ALIGN=CENTER>
<strong>Registered Applications:</strong>
</DIV>
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<?
$db = mysql_database;
$q = "SELECT username, apps From `$db`.`users` Where username = \"" . $user . "\"";
$q = mysql_query($q, $sql->connection);
$r = mysql_fetch_assoc($q);
$expl = explode("; ", $r['apps']);
for($i = 0; $i < count($expl); $i++)
{
if($expl[$i] != "")
{
$q = "SELECT link, app, allow From `$db`.`update` Where app = \"" . $expl[$i] . "\"";
$q = mysql_query($q, $sql->connection);
$r = mysql_fetch_assoc($q);
echo "<tr><td>" . $expl[$i] . "</td><td><form action=\"login.php\" method=\"POST\"><input type=\"hidden\" name=\"viewversion\" value=\"" . $expl[$i] ."\"><input type=\"submit\" value=\"View Version History\"></form></td>";
}
}
echo "</table></form>";
?>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=login.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_REQUEST['hwid'] == "true")
{
?>
<title>UserCP - DarkSel Licensing 2011</title>
<style type="text/css">
body
{
background-color:#d0e4fe;
}
</style>
<?


$db = mysql_database;
$q = "SELECT allowCP, allowHWID From `$db`.`settings`";
$q = mysql_query($q, $sql->connection);
$r = mysql_fetch_assoc($q);
if($r['allowHWID'] == "1")
{
?>
<br>
<DIV ALIGN=CENTER>
<strong>Change Hardware ID:</strong>
</DIV>
<form action="login.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Hardware ID:</td><td><input type="text" name="hwid" maxlength="30" value="">
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="changehwid">
<input type="submit" value="Change"></td></tr>
</table>
</form>
<?
}
else
{
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Failed to update HWID: Administrator has disabled HWID changes.</FONT></DIV>";
}
?>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=login.php>Back To Menu</a>
</DIV>
<?
$display=true;
}
}
if($display == false)
{
?>
<title>UserCP - DarkSel Licensing 2011</title>
<style type="text/css">
body
{
background-color:#d0e4fe;
}
</style>
<DIV ALIGN=CENTER>
<?
if($response[1] == "gmsg")
{
?>
<strong>View News:</strong>
<form action="login.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="10">
<?
echo "<tr><td>News:</td><td width=\"500\">" . nl2br($response[2]) . "<br><DIV ALIGN=RIGHT><b>-" . $response[3] . ":" .$response[4] ."</b></DIV>";
?>
<br></td>
</table>
<?
}
?>
<br>
<b>User CP Functions</b>
<br>
<a href=login.php?appinfo=true>View Application Info</a>
<br>
<a href=login.php?passwd=true>Change Password</a>
<br>
<a href=login.php?hwid=true>Change Hardware ID</a>
</DIV>
<?
}
}
else
{
?>
<style type="text/css">
body
{
background-color:#d0e4fe;
}
</style>
<title>UserCP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>User Login:</strong>
<br>
<br>
</DIV>
<form action="login.php" method="POST">
<table align="CENTER" border="0" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td><input type="text" name="user" maxlength="30" value="">
<tr><td>Password:</td><td><input type="password" name="pass" maxlength="30" value="">
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="login">
<input type="submit" value="Login"></td></tr>
</table>
</form>
<?
}
?>
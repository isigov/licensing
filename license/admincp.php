<?
if(!file_exists("settings.php"))
{
echo "<DIV ALIGN=CENTER>Please run the installer <a href=\"install.php\">here</a> to install DarkSel Licensing.</DIV>";
return;
}
include("database.php");
$display = false;
$action = $_POST['action'];
if($action == "login")
{
$user = $_POST['user'];
$pass = $_POST['pass'];
if($sql->CheckAdmin($user, $pass))
{
setcookie("DL2010", md5($user . $pass . $_SERVER['REMOTE_ADDR']), time()+3600, "/");
echo "<title>Welcome " . $user . "! - DarkSel Licensing 2011</title>" . "<DIV ALIGN=CENTER>You've logged in successfully!<br><a href=\"admincp.php\">Click here to continue</a></DIV>";
return;
}
}
if($_COOKIE["DL2010"] == null)
{

?>
<style type="text/css">
body
{
background-color:#d0e4fe;
}
</style>
<title>Admin CP Login - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Administrator Login</strong>
<br>
<br>
</DIV>
<form action="admincp.php" method="POST">
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
else
{
if($_COOKIE["DL2010"] == md5(admin_username . admin_password . $_SERVER['REMOTE_ADDR']))
{
if($_REQUEST['logout'] == "true")
{
setcookie("DL2010", "", time()-3600, "/");
echo "<DIV ALIGN=CENTER>You've logged out successfully!<br><a href=\"admincp.php\">Click here to continue</a></DIV>";
return;
}
echo "<style type=\"text/css\">body{background-color:#d0e4fe;}</style>";
$userCount = $sql->getUserCount();
$db = mysql_database;
$q = "SELECT ipaddress FROM `$db`.`admincp`";
$r = mysql_query($q, $sql->connection);
$ip22 = "";
if(mysql_num_rows($r) > 0)
{
$r = mysql_fetch_assoc($r);
$ip22 = $r['ipaddress'];
$q = "DELETE FROM `$db`.`admincp` WHERE `ipaddress` = '$ip22'";
mysql_query($q, $sql->connection);
}
$currip = $_SERVER['REMOTE_ADDR'];
$q = "INSERT INTO `$db`.`admincp` (`ipaddress`) VALUES ('$currip');";
mysql_query($q, $sql->connection);
/*echo "<marquee  direction=\"left\">Click <a href=\"admincp.php?logout=true\">here</a> to log out.</marquee><br><br>";*/
$userCount = $sql->getUserCount();
echo "<div align=right>Welcome " . admin_username . "!<br>Click <a href=\"admincp.php?logout=true\">here</a> to log out.<br>Last Login from: " . $ip22 . "<br><a href=\"admincp.php?viewusers=true\">" . $userCount . "</a> users pending approval";
/*echo "<marquee  direction=\"left\">Currently registered users: " . $userCount . "</marquee><br>";*/
/*echo "<marquee  direction=\"left\">Current Date + Time: " . date("m") . "/" .date("j") . "/" . date("Y") . " " .date("g") . ":" . date("i") ."</marquee>";*/
if($action == "CP")
{
$allowCP = mysql_real_escape_string($_POST['allow']);
if($allowCP == "checked")
$allowCP = "1";
else
$allowCP = "0";
$noHWID = mysql_real_escape_string($_POST['noHWID']);
if($noHWID == "checked")
$noHWID = "1";
else
$noHWID = "0";
$allowHWID = mysql_real_escape_string($_POST['hardware']);
if($allowHWID == "checked")
$allowHWID = "1";
else
$allowHWID = "0";
$approve = mysql_real_escape_string($_POST['approve']);
if($approve == "checked")
$approve = "1";
else
$approve = "0";
$db = mysql_database;
$q = mysql_query("SELECT allowCP, allowHWID, noHWID, approve FROM `$db`.`settings`", $sql->connection);
$r = mysql_fetch_assoc($q);
$allowd = $r['allowCP'];
$q = "DELETE FROM `$db`.`settings` WHERE `allowCP` = '$allowd'";
mysql_query($q, $sql->connection);
$q = "INSERT INTO `$db`.`settings` (`allowCP`, `allowHWID`, `noHWID`, `approve`) VALUES ('$allowCP', '$allowHWID', '$noHWID', '$approve');";
mysql_query($q, $sql->connection);
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully updated CP settings.</FONT></DIV><br>";
}
if($action == "addUser")
{
$user = mysql_real_escape_string($_POST['user']);
$pass = mysql_real_escape_string($_POST['pass']);
$email = mysql_real_escape_string($_POST['email']);
$hardware = mysql_real_escape_string($_POST['hardware']);
/*$message = mysql_real_escape_string($_POST['message']);
$ip = mysql_real_escape_string($_POST['ip']);
*/
$app = mysql_real_escape_string($_POST['select2']);
if($sql->CheckApp($app) == false)
{
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot add user " . "\"" . $user . "\"" .": default application doesn't exist!</FONT></DIV><br>";
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
$sql->AddUser($user, $pass, $email,$hardware, "", 0, "", $app);
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully added user " . "\"" . $user . "\"" ."</FONT></DIV><br>";
 }
}
}
if($action == "Message")
{
$user = mysql_real_escape_string($_POST['select']);
$message = mysql_real_escape_string($_POST['message']);
if($sql->CheckUser($user) == false)
 {
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot update message: User '" ."\"" .$user."\"" ."' doesn't exist!</FONT></DIV><br>";
 }
 else
 {
 $sql->updateUserField($user, "message", $message);
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully updated message for user " ."\"" .$user."\"" ."</FONT></DIV><br>";
 }
}

if($action == "globalmsg")
{
$message = mysql_real_escape_string($_POST['comments']);
/*$db = mysql_database;
$q = "SELECT globalmsg FROM `$db`.`msg`";
$r = mysql_query($q, $sql->connection);
if(mysql_num_rows($r) > 0)
{
$r = mysql_fetch_assoc($r);
$msgs = $r['globalmsg'];
$q = "DELETE FROM `$db`.`msg` WHERE `globalmsg` = '$msgs'";
mysql_query($q, $sql->connection);
}
$q = "INSERT INTO `$db`.`msg` (`globalmsg`) VALUES ('$message');";
mysql_query($q, $sql->connection);*/
$sql->AddMessage($message);
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully updated news</FONT></DIV><br>";
}
if($action == "AddApp")
{
$name = str_replace("; ", "", mysql_real_escape_string($_POST['appname']));
if($sql->CheckApp($name))
 {
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot add application: App '" ."\"" .$name."\"" ."' already exists!</FONT></DIV><br>";
 }
 else
 {
   $sql->AddApp($name);
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully added application " ."\"" .$name."\"" ."</FONT></DIV><br>";
 }
}
if($action == "AddAppToUser")
{
$name = mysql_real_escape_string($_POST['select2']);
$user = mysql_real_escape_string($_POST['select']);
if($sql->CheckApp($name) == false)
 {
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot user to application: App '" ."\"" .$name."\"" ."' doesn't exist!</FONT></DIV><br>";
 }
 else
 {
$q = mysql_query("Select `apps` From `$db`.`users` WHERE `username` = '$user'", $sql->connection);
$r = mysql_fetch_assoc($q);
   $sql->updateUserField($user, "apps", str_replace($name . "; ", "", $r['apps']) . $name . "; ");
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully added application " ."\"" .$name."\"" ." to user \"". $user ."\"</FONT></DIV><br>";
 }
}
if($action == "RemoveAppFromUser")
{
$name = mysql_real_escape_string($_POST['select2']);
$user = mysql_real_escape_string($_POST['select']);
if($sql->CheckApp($name) == false)
 {
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot remove application from user: App '" ."\"" .$name."\"" ."' doesn't exist!</FONT></DIV><br>";
 }
 else
 {
$q = mysql_query("Select `apps` From `$db`.`users` WHERE `username` = '$user'", $sql->connection);
$r = mysql_fetch_assoc($q);
$sql->updateUserField($user, "apps", str_replace($name . "; ", "", $r['apps']));
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully removed application " ."\"" .$name."\"" ." from user \"". $user ."\"</FONT></DIV><br>";
 }
}
if($action == "RemoveApp")
{
$name = mysql_real_escape_string($_POST['select2']);
if($sql->CheckApp($name) == false)
 {
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot remove application from user: App '" ."\"" .$name."\"" ."' doesn't exist!</FONT></DIV><br>";
 }
 else
 {
$q = mysql_query("Select username, password, hardware, message, banned, ipaddress, apps From users", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
$sql->updateUserField($r['username'], "apps", str_replace($name . "; ", "", $r['apps']));
}
mysql_query("DELETE FROM `$db`.`update` WHERE `app` = '$name'", $sql->connection);
$sql->removeApp($name);

 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully removed application " ."\"" .$name."\"" . "</FONT></DIV><br>";
 }
}
if($action == "RemoveUpdate")
{
$app = mysql_real_escape_string($_POST['select2']);
if($app == "0")
{
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Error: Update doesn't exist</FONT></DIV><br>";
}
else
{
$db = mysql_database;
$q = "DELETE FROM `$db`.`update` WHERE `date` = '$app'";
mysql_query($q, $sql->connection);
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully removed update</FONT></DIV><br>";
}
}
if($action == "Update")
{
$version = mysql_real_escape_string($_POST['version']);
$message = mysql_real_escape_string($_POST['message']);
$link = mysql_real_escape_string($_POST['link']);
$message = mysql_real_escape_string($_POST['message']);
$optional = mysql_real_escape_string($_POST['optional']);
$allow = mysql_real_escape_string($_POST['allow']);
$app = mysql_real_escape_string($_POST['select2']);
if($optional == "checked")
{
$optional = 1;
}
if($allow == "checked")
{
$allow = 1;
}
if($sql->CheckApp($app) == false)
{
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Could not set update information, app: " . $app ." doesn't exist</FONT></DIV><br>";
}
else
{
/*$q = "DELETE FROM `$db`.`update` WHERE `app` = '$app'";
mysql_query($q, $sql->connection);*/
$date = date("j/n/Y g:i:s a");
$q = "INSERT INTO `$db`.`update` (`version`, `link`, `optional`, `message`, `app`, `allow`, `date`) VALUES ('$version', '$link', '$optional', '$message', '$app', '$allow', '$date');";
mysql_query($q, $sql->connection);
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully added new update information for application: " . $app ."</FONT></DIV><br>";
}
}
if($action == "Password")
{
$user = mysql_real_escape_string($_POST['select']);
$pass = mysql_real_escape_string($_POST['pass']);
if($sql->CheckUser($user) == false)
 {
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot update password: User '" ."\"" .$user."\"" ."' doesn't exist!</FONT></DIV><br>";
 }
 else
 {
 $sql->updateUserField($user, "password", $pass);
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully updated password for user " ."\"" .$user."\"" ."</FONT></DIV><br>";
 }
}
if($action == "Hardware")
{
$user = mysql_real_escape_string($_POST['name']);
$hardware = mysql_real_escape_string($_POST['hardware']);
if($sql->CheckUser($user) == false)
 {
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot update hardware ID: User '" . "\"".$user. "\""."' doesn't exist!</FONT></DIV><br>";
 }
 else
 {
 $sql->updateUserField($user, "hardware", $hardware);
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully updated hardware ID for user " ."\"" .$user."\"" ."</FONT></DIV><br>";
 }
}
if($action == "Ban")
{
$user = mysql_real_escape_string($_POST['name']);
$ban = mysql_real_escape_string($_POST['select2']);
$reason = mysql_real_escape_string($_POST['reason']);
if($sql->CheckUser($user) == false)
 {
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot update ban status: User '" . "\"".$user."\"" ."' doesn't exist!</FONT></DIV><br>";
 }
 else
 {
if($ban == "True")
{
 $sql->updateUserField($user, "banned", "1");
 $sql->updateUserField($user, "banreason", $reason);
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully banned  user " . "\"".$user."\"" ."</FONT></DIV><br>";
}
else
{
$sql->updateUserField($user, "banned", "0");
 $sql->updateUserField($user, "banreason", "");
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully unbanned user " . "\"".$user. "\""."</FONT></DIV><br>";
}
 }
}
if($action == "updateupdate")
{
}
if($_REQUEST['deleteuser'] != "")
{
$user = mysql_real_escape_string($_REQUEST['deleteuser']);
if($sql->CheckUser($user) == false)
{
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot remove user: User '" . "\"".$user. "\""."' doesn't exist!</FONT></DIV><br>";
}
else
{
$sql->removeUser($user);
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully removed user " . "\"".$user."\"" ."</FONT></DIV><br>";
}
}
if($_REQUEST['deletenews'] != "")
{

$sql->removeNews($_REQUEST['deletenews']);
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully removed news post</FONT></DIV><br>";

}
if($_POST['approveusercp'] != "")
{
$user = mysql_real_escape_string($_POST['approveusercp']);
if($sql->CheckUser($user) == false)
{
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Cannot approve user: User '" . "\"".$user. "\""."' doesn't exist!</FONT></DIV><br>";
}
else
{
$sql->updateUserField($user, "approved", "1");
 echo "<DIV ALIGN=CENTER><FONT color=#ff0000>Successfully approved user " . "\"".$user."\"" ."</FONT></DIV><br>";
}
}

if($_REQUEST['adduser'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Add a new user:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td><input type="text" name="user" maxlength="30" value="">
<tr><td>Password:</td><td><input type="password" name="pass" maxlength="30" value="">
<tr><td>Email:</td><td><input type="text" name="email" maxlength="32" value="">
<tr><td>Hardware ID:</td><td><input type="text" name="hardware" maxlength="50" value="">
<tr><td>Default Application:</td><td><select name="select2">
<option value="0">Select Application</option>
<?
$q = mysql_query("Select name From apps", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<option value=\"" . $r['name'] . "\">" . $r['name'] . "</option>";
}
?>
</select>
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="addUser">
<input type="submit" value="Add"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}


if($_REQUEST['usermsg'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Send user message:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td><select name="select">
<option value="0">Select Username</option>
<?
$q = mysql_query("Select username From users", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<option value=\"" . $r['username'] . "\">" . $r['username'] . "</option>";
}
?>
</select>
<tr><td>Message:</td><td><input type="text" name="message" maxlength="50" value="">
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="Message">
<input type="submit" value="Send"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}


if($_REQUEST['globalmsg'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Update News:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Message:</td><td><textarea name="comments" cols="50" rows="10">
</textarea>
<input type="hidden" name="action" value="globalmsg">
</td></tr>
<tr><td>
<input type="submit" value="Update"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_REQUEST['changehwid'] != "")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Change Hardware ID:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td>
<?
echo $_REQUEST['changehwid'];
?>
</select>
<tr><td>Hardware ID:</td><td><input type="text" name="hardware" maxlength="50" value="">
<tr><td colspan="2" align="center">
<input type="hidden" name="name" value=
<?
echo "\"" . $_POST['changehwid'] . "\">";
?>
<input type="hidden" name="action" value="Hardware">
<input type="submit" value="Change"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}


if($_POST['banuser'] != "")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Ban User:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td>
<?
echo $_POST['banuser'];
?>
</select>
<tr><td>Banned:</td><td><select name="select2">
<option value="True">True</option>
<option value="False">False</option>
</select>
<tr><td>Ban Reason:</td><td><input type="text" name="reason" maxlength="50" value="">
<tr><td colspan="2" align="center">
<input type="hidden" name="name" value=
<?
echo "\"" . $_POST['banuser'] . "\">";
?>
<input type="hidden" name="action" value="Ban">
<input type="submit" value="Ban/Unban"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_REQUEST['userpw'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Change User Password:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td><select name="select">
<option value="0">Select Username</option>
<?
$q = mysql_query("Select username From users", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<option value=\"" . $r['username'] . "\">" . $r['username'] . "</option>";
}
?>
</select>
<tr><td>New Password:</td><td><input type="text" name="pass" maxlength="50" value="">
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="Password">
<input type="submit" value="Change"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_REQUEST['userremove'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Remove User:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td><select name="select">
<option value="0">Select Username</option>
<?
$q = mysql_query("Select username From users", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<option value=\"" . $r['username'] . "\">" . $r['username'] . "</option>";
}
?>
</select>
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="Remove">
<input type="submit" value="Remove"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_REQUEST['userapprove'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Approve User:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td><select name="select">
<option value="0">Select Username</option>
<?
$q = mysql_query("Select username, approved From users", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
if($r['approved'] == "0")
{
echo "<option value=\"" . $r['username'] . "\">" . $r['username'] . "</option>";
}
}
?>
</select>
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="approve">
<input type="submit" value="Approve"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}


if($_REQUEST['updates'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Set Update:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Version:</td><td><input type="text" name="version" maxlength="100" value="1.0.0.0">
<tr><td>Message:</td><td><textarea name="link" cols="50" rows="1">
http://www.example.com/LatestVersion.rar
</textarea>
<tr><td>Message:</td><td><textarea name="message" cols="50" rows="10">
Version 1.0.0.0 is the first version of the application, so no updates yet!
</textarea>
<tr><td>Update Optional:</td><td><Input type = "Checkbox" Name ="optional" value ="checked">
<tr><td>Allow Download in UserCP:</td><td><Input type = "Checkbox" Name ="allow" value ="checked">
<tr><td>Application:</td><td><select name="select2">
<option value="0">Select Application</option>
<?
$q = mysql_query("Select name From apps", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<option value=\"" . $r['name'] . "\">" . $r['name'] . "</option>";
}
?>
</select>
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="Update">
<input type="submit" value="Set"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}
if($_POST['editupdates'] != "")
{
$date = $_POST['editupdates'];
$q = mysql_query("SELECT version, link, optional, message, app, allow, date From `$db`.`update` WHERE date = '$date'", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "</table></form>";
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Edit Update:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Version:</td><td>
<?
echo $r['version'];
?>
<tr><td>Message:</td><td><textarea name="link" cols="50" rows="1">
http://www.example.com/LatestVersion.rar
</textarea>
<tr><td>Message:</td><td><textarea name="message" cols="50" rows="10">
Version 1.0.0.0 is the first version of the application, so no updates yet!
</textarea>
<tr><td>Update Optional:</td><td><Input type = "Checkbox" Name ="optional" value ="checked">
<tr><td>Allow Download in UserCP:</td><td><Input type = "Checkbox" Name ="allow" value ="checked">
<tr><td>Application:</td><td>
<?
echo $r['app'];
?>
</select>
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="updateupdate">
<input type="hidden" name="date"
<?
echo "value=\"" . $r['date'] . "\"";
?>
<input type="submit" value="Edit"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
}
$display=true;
}
if($_REQUEST['removeupdate'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Remove Update:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Update:</td><td><select name="select2">
<option value="0">Select Update</option>
<?
$db = mysql_database;
$q = mysql_query("SELECT version, date FROM `$db`.`update`", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<option value=\"" . $r['date'] ."\">" . $r['version'] ." - " .$r['date'] . "</option>";
}
?>
</select>
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="RemoveUpdate">
<input type="submit" value="Remove"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_REQUEST['addappuser'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Add Application to User:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td><select name="select">
<option value="0">Select Username</option>
<?
$q = mysql_query("Select username From users", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<option value=\"" . $r['username'] . "\">" . $r['username'] . "</option>";
}
?>
</select>
<tr><td>Application:</td><td><select name="select2">
<option value="0">Select Application</option>
<?
$q = mysql_query("Select name From apps", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<option value=\"" . $r['name'] . "\">" . $r['name'] . "</option>";
}
?>
</select>
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="AddAppToUser">
<input type="submit" value="Add"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_REQUEST['removeappuser'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Remove Application from User:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td><select name="select">
<option value="0">Select Username</option>
<?
$q = mysql_query("Select username From users", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<option value=\"" . $r['username'] . "\">" . $r['username'] . "</option>";
}
?>
</select>
<tr><td>Application:</td><td><select name="select2">
<option value="0">Select Application</option>
<?
$q = mysql_query("Select name From apps", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<option value=\"" . $r['name'] . "\">" . $r['name'] . "</option>";
}
?>
</select>
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="RemoveAppFromUser">
<input type="submit" value="Remove"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_REQUEST['addappy'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Add Application:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Application Name:</td><td><input type="text" name="appname" maxlength="30" value="">
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="AddApp">
<input type="submit" value="Add"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_REQUEST['removeapp'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Remove Application:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Application Name:</td><td><select name="select2">
<option value="0">Select Application</option>
<?
$q = mysql_query("Select name From apps", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<option value=\"" . $r['name'] . "\">" . $r['name'] . "</option>";
}
?>
</select>
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="RemoveApp">
<input type="submit" value="Remove"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}


if($_REQUEST['cp'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>Control Panel Settings:</strong>
</DIV>
<form action="admincp.php" method="POST">
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td>Allow UserCP Login:</td><td><Input type = "Checkbox" Name ="allow" value ="checked">
<tr><td>Allow Hardware ID Update in CP:</td><td><Input type = "Checkbox" Name ="hardware" value ="checked">
<tr><td>Hardware ID Lock:</td><td><Input type = "Checkbox" Name ="noHWID" value ="checked">
<tr><td>Allow Users to Create Accounts with Administrator Approval:</td><td><Input type = "Checkbox" Name ="approve" value ="checked">
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="CP">
<input type="submit" value="Apply"></td></tr>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}


if($_REQUEST['viewusers'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>View Users:</strong>
</DIV>
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td><b>Username</b></td><td><b>Password</b></td><td><b>Email</b><td><b>Hardware ID</b></td><td><b>Banned</b></td><td><b>Ban Reason</b></td><td><b>IP Address</b></td><td><b>Applications</b></td><td><b>Approved</b></td><td><b>Approve</b></td><td><b>Delete</b></td><td><b>Ban/Unban</b></td><td><b>Change HWID</b></td>
<?
$q = mysql_query("Select username, password, email,hardware, message, banned, ipaddress, apps, banreason, approved From users", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
$banned = "No";
$approved = "No";
if($r['banned'] == 1)
{
$banned = "Yes";
}
if($r['approved'] == 1)
{
$approved = "Yes";
}
if($r['approved'] == 0)
{
echo "<tr><td>" . $r['username'] . "</td><td>" . $r['password'] . "</td><td>" .$r['email'] . "</td><td>".$r['hardware'] . "</td><td>" . $banned . "</td><td>"  . $r['banreason'] . "</td><td>" .$r['ipaddress'] ."</td><td>".$r['apps'] ."</td><td><FONT color=#ff0000>" . $approved . "</FONT></td><td><form action=\"admincp.php\" method=\"POST\"><input type=\"hidden\" name=\"approveusercp\" value=\"" . $r['username'] ."\"><input type=\"submit\" value=\"Approve\"></form></td><td><form action=\"admincp.php\" method=\"POST\"><input type=\"hidden\" name=\"deleteuser\" value=\"" . $r['username'] ."\"><input type=\"submit\" value=\"Delete\"></form></td><td><form action=\"admincp.php\" method=\"POST\"><input type=\"hidden\" name=\"banuser\" value=\"" . $r['username'] ."\"><input type=\"submit\" value=\"Ban/Unban\"></form></td><td><form action=\"admincp.php\" method=\"POST\"><input type=\"hidden\" name=\"changehwid\" value=\"" . $r['username'] ."\"><input type=\"submit\" value=\"Change HWID\"></form></td></tr>";
}
else
{
echo "<tr><td>" . $r['username'] . "</td><td>" . $r['password'] . "</td><td>" .$r['email'] . "</td><td>".$r['hardware'] . "</td><td>" . $banned . "</td><td>"  . $r['banreason'] . "</td><td>" .$r['ipaddress'] ."</td><td>".$r['apps'] ."</td><td>" . $approved . "</td><td><form action=\"admincp.php\" method=\"POST\"><input type=\"hidden\" name=\"approveusercp\" value=\"" . $r['username'] ."\"><input type=\"submit\" value=\"Approve\"></form></td><td><form action=\"admincp.php\" method=\"POST\"><input type=\"hidden\" name=\"deleteuser\" value=\"" . $r['username'] ."\"><input type=\"submit\" value=\"Delete\"></form></td><td><form action=\"admincp.php\" method=\"POST\"><input type=\"hidden\" name=\"banuser\" value=\"" . $r['username'] ."\"><input type=\"submit\" value=\"Ban/Unban\"></form></td><td><form action=\"admincp.php\" method=\"POST\"><input type=\"hidden\" name=\"changehwid\" value=\"" . $r['username'] ."\"><input type=\"submit\" value=\"Change HWID\"></form></td></tr>";
}
}
?>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_REQUEST['viewapps'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>View Applications:</strong>
</DIV>
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td><b>Name</b></td>
<?
$q = mysql_query("Select name From apps", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
echo "<tr><td>" . $r['name'] . "</td>";
}
?>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}

if($_REQUEST['viewnews'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>View News:</strong>
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td><b>Date</b></td><td><b>News</b></td><td><b>Delete</b></td>
<?
$q = mysql_query("Select globalmsg, date From msg ORDER BY DATE ASC", $sql->connection);
$count = 0;

while($r = mysql_fetch_assoc($q)) 
{
echo "<tr><td>" . $r['date'] . "</td><td width=\"500\"><p>" .nl2br($r['globalmsg'] . "</p><td><form action=\"admincp.php\" method=\"POST\"><input type=\"hidden\" name=\"deletenews\" value=\"" . $r['date'] ."\"><input type=\"submit\" value=\"Delete\"></form></td>");
$count++;
}
echo "</table></form>";
if($count == 0)
{
echo "<DIV ALIGN=CENTER><FONT color=#ff0000>No news were found.</FONT></DIV><br>";
}
?>
</DIV>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}



if($_REQUEST['viewupdates'] == "true")
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<strong>View Updates:</strong>
</DIV>
<table align="CENTER" border="1" cellspacing="0" cellpadding="3">
<tr><td><b>Date</b></td><td><b>Version</b></td><td><b>Link</b></td><td><b>Update Message</b></td><td><b>Update Optional</b></td><td><b>Allow Download in UserCP</b></td><td><b>Application</b></td><td><b>Edit</b></td><td><b>Delete</b></td>
<?
$q = mysql_query("SELECT version, link, optional, message, app, allow, date From `$db`.`update`", $sql->connection);
while($r = mysql_fetch_assoc($q)) 
{
$banned = "No";
$banned2 = "No";
if($r['optional'] == 1)
{
$banned = "Yes";
}
if($r['allow'] == 1)
{
$banned2 = "Yes";
}
echo "<tr><td>" . $r['date'] . "</td><td>" . $r['version'] . "</td><td>" . $r['link'] . "</td><td>" . nl2br($r['message']) . "</td><td>" . $banned . "</td><td>" . $banned2 . "</td><td>"  . $r['app'] . "</td><td><form action=\"admincp.php\" method=\"POST\"><input type=\"hidden\" name=\"editupdates\" value=\"" . $r['date'] ."\"><input type=\"submit\" value=\"Edit\"></form></td><td><form action=\"admincp.php\" method=\"POST\"><input type=\"hidden\" name=\"delete\" value=\"" . $r['date'] ."\"><input type=\"submit\" value=\"Delete\"></form></td>";
}
?>
</table>
</form>
<br>
<br>
<br>
<DIV ALIGN=CENTER>
<a href=admincp.php>Back To Menu</a>
</DIV>
<?
$display=true;
}


if($display == false)
{
?>
<title>Admin CP - DarkSel Licensing 2011</title>
<DIV ALIGN=CENTER>
<b>User Functions</b>
<br>
<a href=admincp.php?adduser=true>Add User</a>
<br>
<a href=admincp.php?viewusers=true>View User List</a>
<br>
<br>
<b>Application Functions</b>
<br>
<a href=admincp.php?addappy=true>Add Application</a>
<br>
<a href=admincp.php?removeapp=true>Remove Application</a>
<br>
<a href=admincp.php?addappuser=true>Add Application to User</a>
<br>
<a href=admincp.php?removeappuser=true>Remove Application From User</a>
<br>
<a href=admincp.php?viewapps=true>View Application List</a>
<br>
<br>
<b>Update Functions</b>
<br>
<a href=admincp.php?updates=true>Configure Updates</a>
<br>
<a href=admincp.php?removeupdate=true>Remove Update</a>
<br>
<a href=admincp.php?viewupdates=true>View Update List</a>
<br>
<br>
<b>Global Functions</b>
<br>
<a href=admincp.php?cp=true>Configure User Control Panel</a>
<br>
<a href=admincp.php?globalmsg=true>Update News Page</a>
<br>
<a href=admincp.php?viewnews=true>View News</a>
<br>
<br>
<br>

<?
}
else
{
/*setcookie("DL2010", "", time()-3600, "/");*/
return;
}
}
}
?>
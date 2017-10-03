<?
echo "<title>Installer - DarkSel Licensing 2011</title>";
if($_POST['action'] == "install")
{
$host = $_POST['host'];
$user = $_POST['user'];
$pass = $_POST['pass'];
$db = $_POST['db'];
$auser = $_POST['auser'];
$apass = $_POST['apass'];
$fh = @fopen("settings.php", 'w');
if($fh == 0){
echo "<div align=center>Unable to access file information. Please CHMOD all licensing files to 777!<br>Click <a href=\"install.php\">here</a> to go back.";
return;
}
fwrite($fh, "<?php\n");
fwrite($fh, "define(\"mysql_server\", \"".$host."\");\n");
fwrite($fh, "define(\"mysql_username\", \"".$user."\");\n");
fwrite($fh, "define(\"mysql_password\", \"".$pass."\");\n");
fwrite($fh, "define(\"mysql_database\", \"".$db."\");\n");
fwrite($fh, "define(\"admin_username\", \"".$auser."\");\n");
fwrite($fh, "define(\"admin_password\", \"".$apass."\");\n");
fwrite($fh, "?>");
fclose($fh);
include("database.php");
echo "<div align=center>Installed DarkSel Licensing 2011 sucessfully!<br>Click <a href=\"admincp.php\">here</a> to log into Admin CP.";
}
else
{
if(filesize("settings.php") > 0)
{
echo "You've already installed DarkSel Licensing 2011!";
return;
}
else
{
?>
<DIV ALIGN=CENTER>
<strong>Install DarkSel Licensing</strong>
</DIV><br>
<form action="install.php" method="POST">
<table align="CENTER" border="0" cellspacing="0" cellpadding="3">
<tr><td>MySQL Database Host:</td><td><input type="text" name="host" maxlength="30" value="localhost">
<tr><td>MySQL Database Username:</td><td><input type="text" name="user" maxlength="30" value="">
<tr><td>MySQL Database Password:</td><td><input type="password" name="pass" maxlength="50" value="">
<tr><td>MySQL Database Database</td><td><input type="text" name="db" maxlength="50" value="">
<tr><td>Administrator Panel Username:</td><td><input type="text" name="auser" maxlength="50" value="">
<tr><td>Administrator Panel Password:</td><td><input type="password" name="apass" maxlength="50" value="">
<tr><td colspan="2" align="center">
<input type="hidden" name="action" value="install">
<input type="submit" value="Install"></td></tr>
</table>
</form>
<?
}
}
?>
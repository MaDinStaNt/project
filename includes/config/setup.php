<?php
/**
 * @package LLA.Base
 */
global $DebugLevel;
$DebugLevel = 255;

global $app;
@header('Cache-control: no-cache');
@header('Pragma: no-cache');
@header('Expires: 0');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Setup</title>
	<meta http-equiv="Cache-control" content="no-cache">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="0">
</head>
<body bgcolor="white" color="black">
<a href="setup.php">home</a>
<h1>Modules:</h1><ul>
<li><a href="?action=info">info</a></li>
<?
$mod = InGetPost('mod', '');
if (InGetPost('action', '') == 'install')
{
	$m = &$app->get_module($mod);
	$m->install();
	$app->on_install_module($mod);
}

if (InGetPost('action', '') == 'spec_feat')
{
	$m = &$app->get_module($mod);
	echo '<font color=blue><br>' . $m->special_feature(true) . '<br></font>';
}

foreach ($app->Modules as $k => $v)
{
	$m = &$app->get_module($k);
	if (method_exists($m, 'install'))
	{
		echo '<li>';
		echo '<strong>' . htmlspecialchars($k) . '</strong>';
		if ( (method_exists($m, 'check_install')) && ($m->check_install()) )
		{
			echo ' | <font color="green">installed</font>';
			echo ' | <a href="setup.php?mod='.$k.'&amp;action=install">Re-install</a>';
		}
		else
		{
			echo ' | <font color="red">not installed</font>';
			echo ' | <a href="setup.php?mod='.$k.'&amp;action=install">Install</a>';
		}
		if (method_exists($m, 'special_feature'))
		{
			echo ' | <a href="setup.php?mod='.$k.'&amp;action=spec_feat">'.$m->special_feature(false).'</a>';
		}
		echo '</li>';
	}
}
?>
</ul>
<?
if (InGetPost('action', '') == 'info')
{
if (mail('victor.kachan@gmail.com', 'Subj', 'fdsafdasfdsa'))
	echo 'Success!';
else
	echo 'Error!';
	//phpinfo();
}
$GLOBALS['GlobalDebugInfo']->OutPut();
?>
</body>
</html>

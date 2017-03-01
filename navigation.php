<?php
$navigation = new PipeMenu();

if($loguser['powerlevel'] >= 3 && isAllowed("viewAdminRoom"))
	$navigation->add(new PipeMenuLinkEntry(__("Admin"), "admin", "", "", "cogs"));

$bucket = "topMenuStart"; include("./lib/pluginloader.php");


if(isAllowed("viewMembers"))
	$navigation->add(new PipeMenuLinkEntry(__("Member list"), "memberlist", "", "", "group"));
if(isAllowed("viewRanks"))
	$navigation->add(new PipeMenuLinkEntry(__("Ranks"), "ranks", "", "", "trophy"));
if(isAllowed("viewOnline"))
	$navigation->add(new PipeMenuLinkEntry(__("Online users"), "online", "", "", "eye-open"));


$bucket = "topMenu"; include("./lib/pluginloader.php");

$layout_navigation = $navigation;
?>

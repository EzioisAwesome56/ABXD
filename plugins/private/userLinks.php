<?php
if(IsAllowed("snoopPM") && $loguser['powerlevel'] > 2)
	$links -> add(new PipeMenuLinkEntry(__("Show PMs"), "private", $id, "", "eye-open"));

if($loguserid && IsAllowed("sendPM"))
	$links -> add(new PipeMenuLinkEntry(__("Send PM"), "sendprivate", "", "uid=".$id, "envelope"));
?>
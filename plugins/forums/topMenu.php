<?php
	$navigation->add(new PipeMenuLinkEntry(Settings::get("menuMainName"), "board", "", "", "home"));
	if(isAllowed("search"))
	$navigation->add(new PipeMenuLinkEntry(__("Search"), "search", "", "", "search"));
	$navigation->add(new PipeMenuLinkEntry(__("Last posts"), "lastposts", "", "", "reorder"));
?>

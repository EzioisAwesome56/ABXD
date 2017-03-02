<?php
if(isAllowed("viewPM"))
		$userMenu->add(new PipeMenuLinkEntry(__("Private messages"), "private", "", "", "envelope"));
?>
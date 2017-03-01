<?php
if(isAllowed("editMoods"))
		$userMenu->add(new PipeMenuLinkEntry(__("Mood avatars"), "editavatars", "", "", "picture"));
?>
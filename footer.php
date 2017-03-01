<?php
if (!$mobileLayout) echo '<a href="#" onclick="enableMobileLayout(1); return false;" rel="nofollow">Mobile view</a>';
else echo '<a href="#" onclick="enableMobileLayout(-1); return false;" rel="nofollow">Disable mobile view</a>';
?>
<br>
<br>
<?php $bucket = "footer"; include("./lib/pluginloader.php");?>
Powered by <a href="https://github.com/EzioisAwesome56/Acmlmboard-XD-Jon">Jon's AcmlmBoard XD</a><br />
By EzioisAwesome56<br />
AcmlmBoard XD by Dirbaio, xfix, Kawa, StapleButter, Nina, et al<br />
<?php print __("<!-- English translation by The ABXD Team -->")?>

<?php print (isset($footerButtons) ? $footerButtons : "")?>
<?php print (isset($footerExtensionsB) ? $footerExtensionsB : "")?>



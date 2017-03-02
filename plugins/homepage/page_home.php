<?php
echo '<html><style>
div.Box { background: #4dabb3 !important; padding: 15px !important; margin: 25px; border: 3px solid #8C8C8C; border-radius: 10px; }
</style><body><div class="Box">';
echo CleanUpPost(Settings::pluginGet("homepage"));
echo '</div></body></html>';
?>
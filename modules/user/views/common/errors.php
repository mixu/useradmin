<?php

echo '<ul>';
foreach ($errors as $field => $error) {
   echo '<li><div class="error">'.ucfirst($error).'</div></li>';
}
echo '</ul>';

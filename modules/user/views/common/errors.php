<?php

echo '<ul class="errors">';
foreach ($errors as $field => $error) {
   echo '<li>'.ucfirst($error).'</li><br/>';
}
echo '</ul>';

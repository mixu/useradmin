<h1>Confirm removing your user account</h1>
<?php

echo Form::open('user/unregister/'.$id);

echo Form::hidden('id', $id);

echo '<p>Are you sure you want to remove your user account?</p>';

echo '<p>'.Form::radio('confirmation', 'Y').' Yes<br/>';
echo Form::radio('confirmation', 'N', true).' No<br/></p>';
?>
<?php
echo Form::submit(NULL, 'Confirm');
echo Form::close();

echo Form::open('user/profile');
echo Form::submit(NULL, 'Cancel');
echo Form::close();
?>

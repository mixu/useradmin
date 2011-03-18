<div class="block">
   <h1>Confirm associating your user account</h1>
   <div class="content">
<?php

echo Form::open('user/associate/'.$provider_name, array('style' => 'display: inline;'));

echo '<p>You are about to associate your user account with your '.ucfirst($provider_name).' account. After this, you can log in using that account. Are you sure?</p>';

echo Form::hidden('confirmation', 'Y');

echo Form::submit(NULL, 'Yes');
echo Form::close();

echo Form::open('user/profile', array('style' => 'display: inline; padding-left: 10px;'));
echo Form::submit(NULL, 'Cancel');
echo Form::close();
?>
   </div>
</div>
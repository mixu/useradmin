<h1>Forgot password or username</h1>

<p>Please send me a link to reset my password.</p>

<?php

echo Form::open('user/forgot');
echo '<p>Your email address: '.Form::input('reset_email', '', array('class' => 'text')).'</p>';
?>

<?php echo Form::submit(NULL, 'Reset password') ?>

<?php echo Form::close() ?>


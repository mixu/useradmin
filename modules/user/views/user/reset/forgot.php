<h1><?php echo __('Forgot password or username'); ?></h1>

<p><?php echo __('Please send me a link to reset my password.'); ?></p>

<?php

echo Form::open('user/forgot');
echo '<p>'.__('Your email address:').' '.Form::input('reset_email', '', array('class' => 'text')).'</p>';
?>

<?php echo Form::submit(NULL, __('Reset password')) ?>

<?php echo Form::close() ?>


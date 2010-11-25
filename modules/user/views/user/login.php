<h1><?php echo __('Login'); ?></h1>

<div class="info"><?php echo __('Don\'t have an account?').' '.Html::anchor('user/register', __('Register new account here.')); ?></div>
<div class="info"><small><?php echo __('Forgot your username or password?').' '.Html::anchor('user/forgot', __('Send a password reset email.')); ?></small></div>


<?php

$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($username)) {
   $form->values['username'] = $username;
}
echo $form->open('user/login');
echo '<ul>';
echo '<li>'.$form->label('username', __('Username')).'</li>';
echo $form->input('username', NULL, array('info' => __('You can also log in using your email address instead of your username.')));
echo '<li>'.$form->label('password', __('Password')).'</li>';
echo $form->password('password');
echo '</ul>';
echo $form->submit(NULL, __('Login'));
echo $form->close();

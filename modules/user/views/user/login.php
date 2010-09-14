
<h1>Login</h1>

<p>You can also use your account email address to log in.</p>

<p>If you do not already have an account, <?php echo html::anchor('user/register', 'create one') ?> first.</p>

<p><?php echo html::anchor('user/forgot', 'Forgot your password?') ?></p>

<?php

echo Form::open('user/login');

// show errors
if ( ! empty($errors)) {
   // show errors
   $errors_view = new View('common/errors');
   echo $errors_view->set('errors', $errors)->render();
}

if(!isset($post)) {
   $post = array('username' => '', 'password' => '');
}
?>

<ul>
   <li><label>Username</label><?php echo Form::input('username', $post['username'], array('class' => 'text')) ?></li>
   <li><label>Password</label><?php echo Form::password('password', $post['password'], array('class' => 'password')) ?></li>
</ul>

<p><?php Html::anchor('user/reset', 'Forgot your username or password?'); ?></p>

<?php
echo Form::submit(NULL, 'Login');
echo Form::close();


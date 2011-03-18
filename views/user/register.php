
<div class="block">
   <h1><?php echo __('Register'); ?></h1>
   <div class="content">
      <div class="info" style="width: 415px;"><small><?php echo __('Already have a user account?').' '.Html::anchor('user/login', __('Log in here.')); ?></small></div>
      <br>
      <p><?php echo __('Fill in the information below to register.'); ?></p>

<?php
$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($defaults)) {
   $form->defaults = $defaults;
} else {
   unset($_POST['password']);
   unset($_POST['password_confirmation']);
   $form->defaults = $_POST;
}
echo $form->open('user/register');
?>

<ul>
   <li><label><?php echo __('Username'); ?></label></li>
   <?php echo $form->input('username', null, array('info' => __('Length between 4-32 characters. Letters, numbers, dot and underscore are allowed characters.'))); ?>
   <li><label><?php echo __('Email address'); ?></label></li>
   <?php echo $form->input('email') ?>
   <li><label><?php echo __('Password'); ?></label></li>
   <?php echo $form->password('password', null, array('info' => __('Password should be between 6-42 characters.'))) ?>
   <li><label><?php echo __('Re-type Password'); ?></label></li>
   <?php echo $form->password('password_confirm') ?>
   <?php if(isset($captcha_enabled) && $captcha_enabled) { ?>
   <li>
       <?php echo $recaptcha_html; ?>
       <br/>
   </li>
   <?php } ?>
   <li><?php echo $form->submit(NULL, __('Register new account')); ?></li>
</ul>
<br style="clear:both;">
<?php
echo $form->close();
?>
   </div>
</div>
<h1>Password reset</h1>

<?php
echo Form::open('user/reset');
?>
<ul>
   <li>
      <label>Account email address:</label>
      <?php echo Form::input('reset_email', '', array('class' => 'text')) ?>
   </li>
   <li>
      <label>Password reset token:</label>
      <?php echo Form::input('reset_token', '', array('class' => 'text')) ?>
   </li>
</ul>

<?php echo Form::submit(NULL, 'Reset password') ?>

<?php echo Form::close() ?>


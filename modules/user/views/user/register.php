<h1>Register</h1>

<p>After creating a user, you will be automatically logged in.</p>
<p>If you want to login as a different user go to the <?php echo html::anchor('user/login', 'login') ?> page.</p>

<?php echo Form::open('user/register');
// list of fields for filling default values and classes
$fields = array('email', 'username', 'password', 'password_confirm');
// fill in post if necessary
if(!isset($defaults)) {
   $defaults = array_fill_keys($fields, '');
}
// fill in the classes
$classes = array_fill_keys($fields, 'text');
if ( ! empty($errors)) {
   // show errors
   $errors_view = new View('common/errors');
   echo $errors_view->set('errors', $errors)->render();
   // set the class for the field to include 'error'
   foreach($errors as $field => $error) {
      $classes[$field] .= ' error';
   }
}

// override exceptional field classes
$classes['password'] = 'password';
$classes['password_confirm'] = 'password';
?>

<ul>
   <li>
      <label>Username</label>
      <?php echo Form::input('username', $defaults['username'], array('class' => $classes['username'])) ?>
   </li>
   <li>
      <label>Email Address</label>
      <?php echo Form::input('email', $defaults['email'], array('class' => $classes['email'])) ?>
   </li>
   <li>
      <label>Password</label>
      <?php echo Form::password('password', $defaults['password'], array('class' => $classes['password'])) ?>
   </li>
   <li>
      <label>Confirm Password</label>
      <?php echo Form::password('password_confirm', $defaults['password_confirm'], array('class' => $classes['password_confirm'])) ?>
   </li>
</ul>

<?php echo Form::submit(NULL, 'Register') ?>
<?php echo Form::close() ?>


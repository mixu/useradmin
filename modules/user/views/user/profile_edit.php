<h1>Edit profile</h1>
<?php
echo Form::open('user/profile_edit/');

// show errors
if ( ! empty($errors)) {
  // show errors
   $errors_view = new View('common/errors');
   echo $errors_view->set('errors', $errors)->render();
}

if(!isset($data)) {
   $data = array('email' => '', 'username' => '', 'password' => '', 'password_confirm' => '');
}
?>
<?php
  echo Form::hidden('id', $id);
?>
<ul>
   <li>
      <label>Username</label>
      <?php echo Form::input('username', $data['username'], array('class' => 'text')) ?>
   </li>
   <li>
      <label>Email Address</label>
      <?php echo Form::input('email', $data['email'], array('class' => 'text')) ?>
   </li>
   <li>
      <label>Password</label>
      <?php echo Form::password('password', '', array('class' => 'password')) ?>
   </li>
   <li>
      <label>Confirm Password</label>
      <?php echo Form::password('password_confirm', '', array('class' => 'password')) ?>
   </li>
   <li>
      <label>Roles</label>
      <table class="content">
            <tr class="heading"><td>Role</td><td>Description</td></tr>
  <?php
      $i = 0;
      foreach($user_roles as $role => $description) {
         echo '<tr';
         if($i % 2 == 0) {
            echo ' class="odd"';
         }
         echo '>';
         echo '<td>'.ucfirst($role).'</td><td>'.$description.'</td>';
         echo '</tr>';
         $i++;
      }
   ?>
         </table>
   </li>
</ul>
<br>
<?php
echo Form::submit(NULL, 'Save Profile');
echo Form::close();
?>


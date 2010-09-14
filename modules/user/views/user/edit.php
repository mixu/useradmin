<h1>Edit/add user</h1>
<?php
echo Form::open('user/edit/'.$id);

// show errors
if ( ! empty($errors)) {
   echo '<ul class="errors">';
   foreach ($errors as $error) {
      echo '<li>'.$error.'</li><br/>';
   }
   echo '</ul>';
}

if(!isset($data)) {
   $data = array('email' => '', 'username' => '', 'password' => '', 'password_confirm' => '');
}
?>
<?php
  echo Form::hidden('id', $id);
  // create a role list
  $role_list = array();
  foreach($all_roles as $role) {
     $role_list[$role->name] = $role->description;
  }

?>
<table>
   <tr>
      <td class="caption">Username</td>
      <td><?php echo Form::input('username', $data['username'], array('class' => 'text')) ?></td>
   </tr>
   <tr>
      <td class="caption">Email Address</td>
      <td><?php echo Form::input('email', $data['email'], array('class' => 'text')) ?></td>
   </tr>
   <tr>
      <td class="caption">Password</td>
      <td><?php echo Form::password('password', '', array('class' => 'password')) ?></td>
   </tr>
   <tr>
      <td class="caption">Confirm Password</td>
      <td><?php echo Form::password('password_confirm', '', array('class' => 'password')) ?></td>
   </tr>
   <tr>
      <td class="caption">Roles</td>
      <td>
         <table class="content">
            <tr class="heading"><td colspan="2">Role</td><td>Description</td></tr>
  <?php
      $i = 0;
      foreach($role_list as $role => $description) {
         echo '<tr';
         if($i % 2 == 0) {
            echo ' class="odd"';
         }
         echo '><td>'.Form::checkbox('roles['.$role.']', $role, (in_array($role, $user_roles) ? true : false)).'</td>';
         echo '<td>'.ucfirst($role).'</td><td>'.$description.'</td>';
         echo '</tr>';
         $i++;
      }
   ?>
         </table>
      </td>
   </tr>
</table>
<div class="action">
<?php echo Form::submit(NULL, 'Save User') ?>
</div>
<?php echo Form::close() ?>


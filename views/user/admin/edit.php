<?php

$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($data)) {
   unset($data['password']);
   unset($data['password_confirm']);
   $form->values = $data;
}
echo $form->open('admin_user/edit/'.$id);
?>
<?php
  echo $form->hidden('id', $id);
?>
<div class="block">
<h1><?php echo __('Edit/add user') ?></h1>
   <div class="content">
<ul>
   <li><label><?php echo __('Username'); ?></label></li>
   <?php echo $form->input('username', null, array('info' => __('Length between 4-32 characters. Letters, numbers, dot and underscore are allowed characters.'))); ?>
   <li><label><?php echo __('Email address'); ?></label></li>
   <?php echo $form->input('email') ?>
   <li><label><?php echo __('Password'); ?></label></li>
   <?php echo $form->password('password', null, array('info' => __('Password should be between 6-42 characters.'))) ?>
   <li><label><?php echo __('Re-type Password'); ?></label></li>
   <?php echo $form->password('password_confirm') ?>
   <li><h2><?php echo __('Roles'); ?></h2></li>
   <li><table class="content">
      <tr class="heading"><td></td><td><?php echo __('Role'); ?></td><td><?php echo __('Description'); ?></td></tr>
  <?php
      $i = 0;
      foreach($all_roles as $role => $description) {
         echo '<tr';
         if($i % 2 == 0) {
            echo ' class="odd"';
         }
         echo '>';
         echo '<td>'.Form::checkbox('roles['.$role.']', $role, (in_array($role, $user_roles) ? true : false)).'</td>';
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
echo $form->submit(NULL, __('Save'));
echo $form->close();
?>
   </div>
</div>

<h1>Delete user?</h1>
<?php

echo Form::open('admin_user/delete/'.$id);

echo Form::hidden('id', $id);

echo '<p>'.__('Are you sure you want to delete user ":user"', array(':user' => $data['username'])).'</p>';

echo '<p>'.Form::radio('confirmation', 'Y', false, array('id' => 'conf_y')).' <label for="conf_y">'.__('Yes').'</label><br/>';
echo Form::radio('confirmation', 'N', true, array('id' => 'conf_n')).' <label for="conf_n">'.__('No').'</label><br/></p>';
?>

<?php
echo Form::submit(NULL, __('Delete'));
echo Form::close();
?>
<?php
echo Form::open('admin_user/index');
echo Form::submit(NULL, __('Cancel'));
echo Form::close();



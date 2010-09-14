<h1>Delete user?</h1>
<?php

echo Form::open('admin_user/delete/'.$id);

echo Form::hidden('id', $id);

echo '<p>'.__('Are you sure you want to delete user ":user"', array(':user' => $data['username'])).'</p>';

echo '<p>'.Form::radio('confirmation', 'Y').' '.__('Yes').'<br/>';
echo Form::radio('confirmation', 'N', true).' '.__('No').'<br/></p>';
?>

<?php
echo Form::submit(NULL, __('Delete'));
echo Form::close();
?>
<?php
echo Form::open('admin_user/index');
echo Form::submit(NULL, __('Cancel'));
echo Form::close();



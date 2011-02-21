<?php

/**
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

?>
<h1><?php echo __('Administer users'); ?></h1>
<ul class="submenu">
   <li><?php echo Html::anchor('admin_user/edit', __('Add new user'))?></li>
   <li><?php echo Html::anchor('admin_user/frontpage', __('Edit front page'))?></li>
   <li><?php echo Html::anchor('admin_user/stage', __('Change review stage'))?></li>
</ul>
<?php
/*
if($filter_description != '') {
   echo '<div class="notice">'.$filter_description.' ('.Html::anchor('user/index', __('Remove filter')).')</div>';
}
*/
echo '<p>'.__('Show users:').' </p>';

echo '<ul class="sf-menu">';
if(!isset($_REQUEST['filter_role'])) {
   echo '<li>'.Html::anchor('admin_user/index', __('All'), array('class' => 'selected')).'</a></li>';
} else {
   echo '<li>'.Html::anchor('admin_user/index', __('All')).'</a></li>';
}
// Select all roles
$role = ORM::factory('role');
foreach($role->find_all() as $obj) {
   if($obj->name != 'login') {
      if(isset($_REQUEST['filter_role']) && ($_REQUEST['filter_role'] == $obj->name)) {
         echo '<li>'.Html::anchor('admin_user/index?filter_role='.$obj->name, ucfirst($obj->name).'s', array('class' => 'selected')).'</li>';
      } else {
         echo '<li>'.Html::anchor('admin_user/index?filter_role='.$obj->name, ucfirst($obj->name).'s').'</li>';
      }
   }
}
echo '</ul><br style="clear:both;">';

echo $paging->render();
// format data for DataTable
$data = array();
$merge = null;
foreach ($users as $obj) {
   $row = $obj->as_array();
   // reformat dates
   $row['created'] = Helper_Format::friendly_datetime($row['created']);
   $row['modified'] = Helper_Format::friendly_datetime($row['modified']);
   $row['last_login'] = Helper_Format::relative_time($row['last_login']);
   $row['last_failed_login'] = Helper_Format::relative_time(strtotime($row['last_failed_login']));
   // add actions
   $row['actions'] = Html::anchor('admin_user/edit/'.$row['id'], __('Edit')).' <br> '.Html::anchor('admin_user/delete/'.$row['id'], __('Delete'));
   // set roles
   $row['role'] = '';
   foreach($obj->roles->where('name', '!=', 'login')->find_all() as $role) {
      $row['role'] .= $role->name.', ';
   }
   // remove last comma
   $row['role'] = substr($row['role'], 0, -2);
   $data[] = $row;
}

$column_list = array ( 'username' => array ( 'label' => __('Username') ),
                       'role' => array ( 'label' => __('Role(s)'), 'sortable' => false ),
                       'last_login' => array ( 'label' => __('Last login') ),
                       'logins' => array ( 'label' => __('# of logins') ),
                       'actions' => array ( 'label' => __('Actions'), 'sortable' => false ), );


$datatable = new Helper_Datatable($column_list, array('paginator' => true, 'class' => 'content', 'sortable' => 'true', 'default_sort' => 'username'));
$datatable->values($data);
echo $datatable->render();
echo $paging->render();


<?php

/**
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

?>
<h1>User administration</h1>
<ul class="submenu">
 <li><?php echo Html::anchor('admin_user/edit', 'Add new user')?></li>
</ul>
<?php
echo $paging->render();

?>
<table class="content">
   <thead>
      <tr>
         <th scope="col"><a href="/index.php/admin_user?page=1&sort=username&dir=1">Username</a></th>
         <th scope="col"><a href="/index.php/admin_user?page=1&sort=email">Email</a></th>
         <th scope="col">Role(s)</th>
         <th scope="col">Actions</th>
      </tr>
   </thead>
   <tbody>
<?php
$i = 0;
foreach ($users as $obj) {
   $row =  $obj->as_array();
   // reformat dates
   // Note: fields that do not exist in the default configuration are commented out. See /modules/user/classes/model/user.php for details.
   //      <th scope="col">Last login</th>
   //      <th scope="col">Failed logins</th>
   //      <th scope="col">Last failed login</th>
   // $row['created'] = Helper_Format::friendly_datetime($row['created']);
   // $row['modified'] = Helper_Format::friendly_datetime($row['modified']);
   if($row['last_login'] != '') {
      $row['last_login'] = Helper_Format::relative_time($row['last_login']);
   }
//   if($row['last_failed_login'] != '0000-00-00 00:00:00') {
//      $row['last_failed_login'] = Helper_Format::relative_time(strtotime($row['last_failed_login']));
//   } else {
//      $row['last_failed_login'] = 'never';
//   }
   // add actions
   $row['actions'] = Html::anchor('admin_user/edit/'.$row['id'], 'Edit').' '.Html::anchor('admin_user/delete/'.$row['id'], 'Delete');
   // set roles
   $row['role'] = '';
   foreach($obj->roles->find_all() as $role) {
      $row['role'] .= $role->name.', ';
   }
   // remove last comma
   $row['role'] = substr($row['role'], 0, -2);
   echo '<tr '.($i % 2 == 0 ? 'class="odd"' : '').'><td>'.$row['username'].'</td><td>'.$row['email'].'</td><td>'.$row['role'].'</td><td>'.$row['actions'].'</td></tr>';
   $i++;
}

?>
   </tbody>
</table>

<?php

echo $paging->render();





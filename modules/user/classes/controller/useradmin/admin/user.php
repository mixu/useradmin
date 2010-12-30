<?php

/**
 * User controller: user administration, also user accounts/profiles.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

class Controller_Useradmin_Admin_User extends Controller_App {

   /**
    * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified.
    *
    * See Controller_App for how this implemented.
    *
    * Can be set to a string or an array, for example array('login', 'admin') or 'login'
    */
   public $auth_required = 'admin';

   /** Controls access for separate actions
    *
    *  See Controller_App for how this implemented.
    *
    *  Examples:
    * 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
    * 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
    */
   public $secure_actions = array();

   // USER ADMINISTRATION

   /**
    * Administator view of users.
    */
   public function action_index() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = __('User administration');
      // create a user
      $user = ORM::factory('user');
      // This is an example of how to use Kohana pagination
      // Get the total count for the pagination
      $total = $user->count_all();
      // Create a paginator
      $pagination = new Pagination(array(
         'total_items' => $total,
         'items_per_page'=> 3, // set this to 30 or 15 for the real thing, now just for testing purposes...
         'auto_hide' => false,
      ));
      // Get the items for the query
      $sort = isset($_GET['sort']) ? $_GET['sort'] : 'username'; // set default sorting direction here
      $dir = isset($_GET['dir']) ? 'DESC' : 'ASC';
      $result = $user->limit($pagination->items_per_page)->offset($pagination->offset)->order_by($sort, $dir)
              ->find_all();

      // render view
      // pass the paginator, result and default sorting direction
      $this->template->content = View::factory('user/admin/index')->set('users', $result)->set('paging', $pagination)->set('default_sort', $sort);

   }

   /**
    * Administrator edit user.
    * @param string $id
    * @return void
    */
   public function action_edit($id = NULL) {
      // set the template title (see Controller_App for implementation)
      $this->template->title = 'Edit user';
      // load the content from view
      $view = View::factory('user/admin/edit');

      // save the data
      if (!empty($_POST)) {
         // sample code paths for edit and create
         if(is_numeric($id)) {
            // EDIT: load the model with ID
            $model = ORM::factory('user', $id);
         } else {
            // CREATE: do not specify id
            $model = ORM::factory('user');
         }
         unset($_POST['id']);
         if(empty($_POST['password']) || empty($_POST['password_confirm'])) {
            // force unsetting the password! Otherwise Kohana3 will automatically hash the empty string - preventing logins
            unset($_POST['password'], $_POST['password_confirm']);            
         }         
         $model->values($_POST);
         // since we combine both editing and creating here we need a separate variable
         // you can get rid of it if your actions don't need to do that
         $result = false;
         if(is_numeric($id)) {
            // EDIT: check using alternative rules
            $result = $model->check_edit();
         } else {
            // CREATE: check using default rules
            $result = $model->check();
         }
         if($result) {
            // validation passed, save model
            $model->save();
            // roles have to be added separately, and all users have to have the login role
            // you first have to remove the items, otherwise add() will try to add duplicates
            if(is_numeric($id)) {
               // could also use array_diff, but this is much simpler
               DB::delete('roles_users')->where('user_id', '=', $id)->execute();
            }
            foreach($_POST['roles'] as $role) {
               // add() executes the query immediately, and saves the data (unlike the KO2 docs say)
               $model->add('roles', ORM::factory('role')->where('name', '=', $role)->find());
            }
            // message: save success
            Message::add('success', __('Values saved.'));
            // redirect and exit
            Request::instance()->redirect('admin_user/index');
            return;
         } else {
            // Get errors for display in view --> to AppForm
            Message::add('error', __('Error: Values could not be saved.'));
            // Note how the first param is the path to the message file (e.g. /messages/register.php)
            $view->set('errors', $model->validate()->errors('register'));
            // Pass on the old form values --> to AppForm
            $view->set('data', $model->as_array());
         }
      }

      // if an ID is set, load the information
      if(is_numeric($id)) {
         // instantiatiate a new model
         $model = ORM::factory('user', $id);
         $view->set('data', $model->as_array());
         // retrieve roles into array
         $roles = array();
         foreach($model->roles->find_all() as $role) {
            $roles[] = $role->name;
         }
         $view->set('user_roles', $roles);
      } else {
         $view->set('user_roles', array('login' => 'login'));
      }

      // get all roles
      $all_roles = array();
      $role_model = ORM::factory('role');
      foreach($role_model->find_all() as $role) {
         $all_roles[$role->name] = $role->description;
      }
      $view->set('all_roles', $all_roles);
      $view->set('id', $id);
      $this->template->content = $view;
   }

   /**
    * Administrator delete user
    * @param string $id
    * @return void
    */
   public function action_delete($id = NULL) {
      // set the template title (see Controller_App for implementation)
      $this->template->title = 'Delete user';
      $user = ORM::factory('user', $id);
      // check for confirmation
      if(is_numeric($id) && isset($_POST['confirmation']) && $_POST['confirmation'] == 'Y') {
         // Delete the user
         $user->delete($id);
         // message: save success
         Message::add('success', 'User deleted.');
         // redirect and exit
         Request::instance()->redirect('admin_user/index');
         return;
      }
      // display confirmation
      $this->template->content = View::factory('user/admin/delete')->set('id', $id)->set('data', array('username' => $user->username));
   }
}

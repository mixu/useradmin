<?php

/**
 * User controller: user administration, also user accounts/profiles.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

class Controller_User extends Controller_App {

   /**
    * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified.
    *
    * See Controller_App for how this implemented.
    * 
    * Can be set to a string or an array, for example array('login', 'admin') or 'login'
    */
   public $auth_required = FALSE;

   /** Controls access for separate actions
    *
    *  See Controller_App for how this implemented.
    *
    *  Examples:
    * 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
    * 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
    */
   public $secure_actions = array(
      // user actions
      'index' => 'login',
      'profile' => 'login',
      'profile_edit' => 'login',
      'unregister' => 'login',
      'logout' => 'login',
      // the others are public (forgot, login, register, reset, noaccess)
      );

   // USER SELF-MANAGEMENT

   /**
    * View: Redirect admins to admin index, users to user profile.
    */
   public function action_index() {
      // if the user has the admin role, redirect to admin_user controller
      if(Auth::instance()->logged_in('admin')) {
         Request::instance()->redirect('admin_user/index');
      } else {
         Request::instance()->redirect('user/profile');
      }
   }

   /**
    * View: Access not allowed.
    */
   public function action_noaccess() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = 'Access not allowed';
      $view = $this->template->content = View::factory('user/noaccess');
   }

   /**
    * View: User account information
    */
   public function action_profile() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = 'User profile';
      if ( Auth::instance()->logged_in() == false ){
         // No user is currently logged in
         Request::instance()->redirect('user/login');
      }
      $view = $this->template->content = View::factory('user/profile');
      // retrieve the current user and set the view variable accordingly
      $view->set('user', Auth::instance()->get_user() );
   }

   /**
    * View: Profile editor
    */
   public function action_profile_edit() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = 'Edit user profile';
      $id = Auth::instance()->get_user()->id;
      // load the content from view
      $view = View::factory('user/profile_edit');

      // save the data
      if ( !empty($_POST) && is_numeric($id) ) {
         $model = null;
         // Load the validation rules, filters etc...
         $model = ORM::factory('user', $id);
         // password can be empty if an id exists - it will be ignored in save.
         if (is_numeric($id) && (empty($_POST['password']) || (trim($_POST['password']) == '')) )  {
            unset($_POST['password']);
            unset($model->password);
         }
         // editing requires that the username and email do not exist (EXCEPT for this ID)
         $post = $model->validate_edit($id, $_POST);

         // If the post data validates using the rules setup in the user model
         if ($post->check()) {
            // Affects the sanitized vars to the user object
            $model->values($post);
            // save first, so that the model has an id when the relationships are added
            $model->save();
            // message: save success
            Message::add('success', 'Values saved.');
            // redirect and exit
            Request::instance()->redirect('user/profile');
            return;
         } else {
            // Get errors for display in view
            Message::add('error', 'Validation errors: '.var_export($post->errors(), TRUE));
            // set the data from POST
            $view->set('defaults', $post->as_array());
         }
      } else {
         // load the information for viewing
         $model = ORM::factory('user', $id);
         $view->set('data', $model->as_array());
         // retrieve roles into array
         $roles = array();
         foreach($model->roles->find_all() as $role) {
            $roles[$role->name] = $role->description;
         }
         $view->set('user_roles', $roles);
      }
      $view->set('id', $id);
      $this->template->content = $view;

   }

   /**
    * Register a new user.
    */
   public function action_register() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = 'User registration';
      // If user already signed-in
      if(Auth::instance()->logged_in() != false){
         // redirect to the user account
         Request::instance()->redirect('user/profile');
      }
      // Load the view
      $content = $this->template->content = View::factory('user/register');
      // If there is a post and $_POST is not empty
      if ($_POST) {
         // Instantiate a new user
         $user = ORM::factory('user');
         // Load the validation rules, filters etc...
         $post = $user->validate_create($_POST);

         // REQUEST: If you do implement CAPTCHA for registration, send me the code so I can publish it. 


         // If the post data validates using the rules setup in the user model
         if ($post->check()) {
            // Affects the sanitized vars to the user object
            $user->values($post);
            // create the account
            $user->save();
            // Add the login role to the user (add a row to the db)
            $login_role = new Model_Role(array('name' =>'login'));
            $user->add('roles',$login_role);
            // sign the user in
            Auth::instance()->login($post['username'], $post['password']);
            // redirect to the user account
            Request::instance()->redirect('user/profile');
         } else {
            // Get errors for display in view
            // Note how the first param is the path to the message file (e.g. /messages/register.php)
            $content->errors = $post->errors('register');
            // Pass on the old form values
            $_POST['password'] = $_POST['password_confirm'] = '';
            $content->set('defaults', $_POST);
         }
      }
   }

   /**
    * Close the current user's account.
    */
   public function action_unregister() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = 'Close user account';
      if ( Auth::instance()->logged_in() == false ){
         // No user is currently logged in
         Request::instance()->redirect('user/login');
      }
      // get the user id
      $id = Auth::instance()->get_user()->id;
      $user = ORM::factory('user', $id);      
      // KO3 ORM is lazy loading, which means we have to access a single field to actually have something happen.
      if($user->id != $id) {
         // If the user is not the current user, redirect
         Request::instance()->redirect('user/profile');
      }
      // check for confirmation
      if(is_numeric($id) && isset($_POST['confirmation']) && $_POST['confirmation'] == 'Y') {
         if (Auth::instance()->logged_in()) {
            // Log the user out, their account will no longer exist
            Auth::instance()->logout();
         }
         // Delete the user
         $user->delete($id);
         // message: save success
         Message::add('success', 'User deleted.');
         Request::instance()->redirect('user/profile');
      }
      // display confirmation
      $this->template->content = View::factory('user/unregister')->set('id', $id)->set('data', array('username' => Auth::instance()->get_user()->username));
   }

   /**
    * View: Login form.
    */
   public function action_login() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = 'Login';
      // If user already signed-in
      if(Auth::instance()->logged_in() != 0){
         // redirect to the user account
         Request::instance()->redirect('user/profile');
      }
      $content = $this->template->content = View::factory('user/login');
      // If there is a post and $_POST is not empty
      if ($_POST) {
         // Instantiate a new user
         $user = ORM::factory('user');

         // Check Auth
         // more specifically, username and password fields need to be set.
         $status = $user->login($_POST);

         // If the post data validates using the rules setup in the user model
         if ($status) {
            // redirect to the user account
            Request::instance()->redirect('user/profile');
         } else {
            // Get errors for display in view
            $content->errors = $_POST->errors('login');
         }
      }
   }

   /**
    * Log the user out.
    */
   public function action_logout() {
      // Sign out the user
      Auth::instance()->logout();

      // redirect to the user account and then the signin page if logout worked as expected
      Request::instance()->redirect('user/profile');
   }

   /**
    * A basic implementation of the "Forgot password" functionality
    */
   public function action_forgot() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = 'Forgot password';
      if(isset($_POST['reset_email'])) {
         $user = ORM::factory('user')->where('email', '=', $_POST['reset_email'])->find();
         // admin passwords cannot be reset by email
         if (is_numeric($user->id) && ($user->username != 'admin')) {
            // send an email with the account reset token
            $user->reset_token = $this->generate_password(32);
            $user->save();

            $message = "You have requested a password reset. You can reset password to your account by visiting the page at:\n\n"
            .Html::anchor('user/reset?reset_token='.$user->reset_token.'&reset_email='.$_POST['reset_email'])."\n\n"
            ."If the above link is not clickable, please visit the following page:\n"
            .URL::site('user/reset')."\n\n"
            ."and copy/paste the following Reset Token: ".$user->reset_token."\n";

            Message::add('success', 'Password reset. '.$message);

            // Emailing is not implemented.
            // REQUEST: If you do implement it, send me the code so I can publish it here. 

            Message::add('success', 'Password reset email sent.');
         } else if ($user->username == 'admin') {
            Message::add('error', 'Admin account password cannot be reset via email.');
         }
      }
     $this->template->content = View::factory('user/reset/forgot');
   }

   /**
    * Generates a password of given length using mt_rand.
    * @param int $length
    * @return string
    */
   function generate_password($length = 8) {
      // start with a blank password
      $password = "";
      // define possible characters (does not include l, number relatively likely)
      $possible = "123456789abcdefghjkmnpqrstuvwxyz123456789";
      $i = 0;
      // add random characters to $password until $length is reached
      while ($i < $length) {
         // pick a random character from the possible ones
         $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

         $password .= $char;
         $i++;

      }
      return $password;
   }

  /**
   * A basic version of "reset password" functionality.
   */
  function action_reset() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = 'Reset password';
      if(isset($_REQUEST['reset_token']) && isset($_REQUEST['reset_email'])) {
         // make sure that the reset_token has exactly 32 characters (not doing that would allow resets with token length 0)
         if( (strlen($_REQUEST['reset_token']) == 32) && (strlen(trim($_REQUEST['reset_email'])) > 1) ) {
            $user = ORM::factory('user')->where('email', '=', $_REQUEST['reset_email'])->and_where('reset_token', '=', $_REQUEST['reset_token'])->find();
            // admin passwords cannot be reset by email
            if (is_numeric($user->id) && ($user->reset_token == $_REQUEST['reset_token']) && ($user->username != 'admin')) {
               $password = $this->generate_password();
               $user->password = $password;
// This field does not exist in the default config:
//               $user->failed_login_count = 0;
               $user->save();
               Message::add('Password reset.');
               Message::add('<p>Your password has been reset to: "'.$password.'".</p><br><p>Please log in below.</p>');
               Request::instance()->redirect('user/login');
            }
        }
     }
     $this->template->content = View::factory('user/reset/reset');
  }

}

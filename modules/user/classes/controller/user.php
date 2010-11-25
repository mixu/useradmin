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
      'change_password' => 'login',
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
      $this->template->title = __('Access not allowed');
      $view = $this->template->content = View::factory('user/noaccess');
   }

   /**
    * View: User account information
    */
   public function action_profile() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = __('User profile');
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
      $this->template->title = __('Edit user profile');
      $id = Auth::instance()->get_user()->id;
      // load the content from view
      $view = View::factory('user/profile_edit');

      // save the data
      if ( !empty($_POST) && is_numeric($id) ) {
         $model = null;
         // Load the validation rules, filters etc...
         $model = ORM::factory('user', $id);
         // editing requires that the username and email do not exist (EXCEPT for this ID)
         $model->values($_POST);

         // If the post data validates using the rules setup in the user model
         if ($model->check_edit()) {
            // save first, so that the model has an id when the relationships are added
            $model->save();
            // message: save success
            Message::add('success', __('Values saved.'));
            // redirect and exit
            Request::instance()->redirect('user/profile');
            return;
         } else {
            // Get errors for display in view
            // Note how the first param is the path to the message file (e.g. /messages/register.php)
				$content->errors = $user->validate()->errors('register');
            // Pass on the old form values
            $_POST['password'] = $_POST['password_confirm'] = '';
            $content->set('defaults', $_POST);
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
      $this->template->title = __('User registration');
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
         // load values from $_POST
         $user->values($_POST);

         // REQUEST: If you do implement CAPTCHA for registration, send me the code so I can publish it. 

         // If the post data validates using the rules setup in the user model
			if ($user->check()) {
            // create the account
            $user->save();
            // Add the login role to the user (add a row to the db)
            $login_role = new Model_Role(array('name' =>'login'));
            $user->add('roles',$login_role);
            // sign the user in
			      Auth::instance()->login($_POST['username'], $_POST['password']);
            // redirect to the user account
            Request::instance()->redirect('user/profile');
         } else {
            // Get errors for display in view
            // Note how the first param is the path to the message file (e.g. /messages/register.php)
			      $content->errors = $user->validate()->errors('register');
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
      $this->template->title = __('Close user account');
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
         Message::add('success', __('User deleted.'));
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
      $this->template->title = __('Login');
      // If user already signed-in
      if(Auth::instance()->logged_in() != 0){
         // redirect to the user account
         Request::instance()->redirect('user/profile');
      }
      $content = $this->template->content = View::factory('user/login');
      // allow setting the username as a get param
      if(isset($_GET['username'])) {
         $content->set('username', Security::xss_clean($_GET['username']));
      }

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
            $content->set('errors', $_POST->errors('login'));
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
      $this->template->title = __('Forgot password');
      if(isset($_POST['reset_email'])) {
         $user = ORM::factory('user')->where('email', '=', $_POST['reset_email'])->find();
         // admin passwords cannot be reset by email
         if (is_numeric($user->id) && ($user->username != 'admin')) {
            // send an email with the account reset token
            $user->reset_token = $user->generate_password(32);
            $user->save();

            $message = "You have requested a password reset. You can reset password to your account by visiting the page at:\n\n"
            .":reset_token_link\n\n"
            ."If the above link is not clickable, please visit the following page:\n"
            .":reset_link\n\n"
            ."and copy/paste the following Reset Token: :reset_token\nYour user account name is: :username\n";

            $mailer = Email::connect();
            // Create complex Swift_Message object stored in $message
            // MUST PASS ALL PARAMS AS REFS
            $subject = __('Account password reset');
            $to = $_POST['reset_email'];
            $from = 'admin@test.example';
            $body =  __($message, array(
                ':reset_token_link' => URL::site('user/reset?reset_token='.$user->reset_token.'&reset_email='.$_POST['reset_email'], TRUE),
                ':reset_link' => URL::site('user/reset', TRUE),
                ':reset_token' => $user->reset_token,
                ':username' => $user->username
            ));
            $message_swift = Swift_Message::newInstance($subject, $body)
                    ->setFrom($from)
                    ->setTo($to);
            if($mailer->send($message_swift)) {
               Message::add('success', __('Password reset email sent.'));
               Request::instance()->redirect('user/login');
            } else {
               Message::add('failure', __('Could not send email.'));
            }
         } else if ($user->username == 'admin') {
            Message::add('error', __('Admin account password cannot be reset via email.'));
         } else {
            Message::add('error', __('User account could not be found.'));
         }
      }
     $this->template->content = View::factory('user/reset/forgot');
   }

   /**
    * A basic version of "reset password" functionality.
    */
  function action_reset() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = __('Reset password');
      if(isset($_REQUEST['reset_token']) && isset($_REQUEST['reset_email'])) {
         // make sure that the reset_token has exactly 32 characters (not doing that would allow resets with token length 0)
         if( (strlen($_REQUEST['reset_token']) == 32) && (strlen(trim($_REQUEST['reset_email'])) > 1) ) {
            $user = ORM::factory('user')->where('email', '=', $_REQUEST['reset_email'])->and_where('reset_token', '=', $_REQUEST['reset_token'])->find();
            // admin passwords cannot be reset by email
            if (is_numeric($user->id) && ($user->reset_token == $_REQUEST['reset_token']) && ($user->username != 'admin')) {
               $password = $user->generate_password();
               $user->password = $password;
// This field does not exist in the default config:
//               $user->failed_login_count = 0;
               $user->save();
               Message::add('success', __('Password reset.'));
               Message::add('success', '<p>'.__('Your password has been reset to: ":password".', array(':password' => $password)).'</p><p>'.__('Please log in below.').'</p>');
   				     Request::instance()->redirect('user/login?username='.$user->username);
            }
        }
     }
     $this->template->content = View::factory('user/reset/reset');
  }

  function action_change_password() {
      // set the template title (see Controller_App for implementation)
      $this->template->title = __('Change password');
      $id = Auth::instance()->get_user()->id;
      // load the content from view
      $view = View::factory('user/change_password');

      // save the data
      if ( !empty($_POST) && is_numeric($id) ) {
         // Load the validation rules, filters etc...
         $model = ORM::factory('user', $id);
         // editing requires that the username and email do not exist (EXCEPT for this ID)
         // If the post data validates using the rules setup in the user model
         $param_by_ref = array('password' => $_POST['password'], 'password_confirm' => $_POST['password_confirm']);
         $validate = $model->change_password($param_by_ref, FALSE);
         if ($validate) {
            // message: save success
            Message::add('success', __('Values saved.'));
            // redirect and exit
            $this->role_redirect();
            return;
         } else {
            // UNFORTUNATELY, it is NOT possible to get errors for display in view
            // since they will never be returned by change_password()
            Message::add('error', __('Password could not be changed, please make sure that the passwords match.'));
            // Pass on the old form values
            $_POST['password'] = $_POST['password_confirm'] = '';
            $view->set('defaults', $_POST);
         }
      } else {
         // load the information for viewing
         $model = ORM::factory('user', $id);
         $view->set('data', $model->as_array());
      }
      $this->template->content = $view;
  }
  
  function action_change_language($lang) {
     if(!in_array($lang, array('fi', 'sv', 'en-us'))) {
        $lang = 'fi';
     }
     Cookie::set('lang', $lang);
     I18n::lang($lang);
     $this->role_redirect();
  }
}

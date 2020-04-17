<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once('phpass-0.1/PasswordHash.php');

define('STATUS_ACTIVATED', '1');
define('STATUS_NOT_ACTIVATED', '0');

/**
 * M_auth
 *
 * Authentication library for CodeIgniter.
 *
 * @package        M_auth
 * @author         Mehdi Jalal 
 * @version        1.0.0
 * @Email          <Mehdi.Jalal@live.com><mehdi@netlinks.af>
 * @Phone          +93 783 20 30 70
 */
class M_auth {

    private $error = array();

    function __construct() {
        $this->ci = & get_instance();

        $this->ci->load->config('m_auth', TRUE);

        $this->ci->load->library('session', 'database');
        $this->ci->load->database();
        $this->ci->load->model('m_auth/users');

        // Try to autologin
        $this->autologin();
    }

    /**
     * Login user on the site. Return TRUE if login is successful
     * (user exists and activated, password is correct), otherwise FALSE.
     *
     * @param	string	(username or email or both depending on settings in config file)
     * @param	string
     * @param	bool
     * @return	bool
     */
    function login($login, $password, $remember, $login_by_username, $login_by_email) {
        if ((strlen($login) > 0) AND (strlen($password) > 0)) {

            // Which function to use to login (based on config)
            if ($login_by_username AND $login_by_email) {
                $get_user_func = 'get_user_by_login';
            } else if ($login_by_username) {
                $get_user_func = 'get_user_by_username';
            } else {
                $get_user_func = 'get_user_by_email';
            }

            if (!is_null($user = $this->ci->users->$get_user_func($login))) { // login ok
                // Does password match hash in database?
                $hasher = new PasswordHash(
                        $this->ci->config->item('phpass_hash_strength', 'm_auth'), $this->ci->config->item('phpass_hash_portable', 'm_auth'));
                $vaal = $hasher->CheckPassword($password, $user->password);

                if ($vaal) {  // password ok
                    if ($user->banned == 1) {         // fail - banned
                        $this->error = array('banned' => $user->ban_reason);
                        redirect("banned-" . $user->id);
                    } else {
                        /*
                          $partner_site_id = $this->ci->session->userdata('partner_site_id');
                          if($user->js_type!=1 && $partner_site_id!=$user->partner_site_id)
                          {
                          $this->ci->session->set_flashdata('login_msg','false');
                          redirect(base_url().'login', 'refresh');
                          }
                         * 
                         */
                        $this->ci->session->set_userdata(array(
                            'user_id' => $user->id,
                            'username' => $user->username,
                            'usertype' => $user->js_type,
                            'status' => ($user->activated == 1) ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED,
                            'language'      => 'en'
                        ));

                        if ($user->activated == 0) {
                            // fail - not activated
                            $this->error = array('not_activated' => '');
                        } else {
                            // success
                            if ($remember) {
                                $this->create_autologin($user->id);
                            }

                            $this->clear_login_attempts($login);

                            $this->ci->users->update_login_info(
                                    $user->id, $this->ci->config->item('login_record_ip', 'm_auth'), $this->ci->config->item('login_record_time', 'm_auth'));

                            return TRUE;
                        }
                    }
                } else {              // fail - wrong password
                    $this->increase_login_attempt($login);
                    $this->error = array('password' => 'auth_incorrect_password');
                }
            } else {               // fail - wrong login
                $this->increase_login_attempt($login);
                $this->error = array('login' => 'auth_incorrect_login');
            }
        }
        return FALSE;
    }

    /**
     * Logout user from the site
     *
     * @return	void
     */
    function logout() {
        $this->delete_autologin();

        // See http://codeigniter.com/forums/viewreply/662369/ as the reason for the next line
        $this->ci->session->set_userdata(array('user_id' => '', 'username' => '', 'usertype' => '', 'status' => ''));

        $this->ci->session->sess_destroy();
    }

    /**
     * 
     * @return boolean check if a user is logged in or not
     */
    function notLogin() {
        if (!$this->is_logged_in($this->get_user_status())) {
            //echo "pelase <a href='login'><b>login</b></a> to continue ";
            redirect('login/home/login', 'refresh');
            exit;
        }

        return true;
    }

    /**
     * Check if user logged in. Also test if user is activated or not.
     *
     * @param	bool
     * @return	bool
     */
    function is_logged_in($activated = TRUE) {
        return $this->ci->session->userdata('status') === ($activated ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED);
    }

    /**
     * Get User Status
     */
    function get_user_status() {
        return $this->ci->session->userdata('status');
    }

    function get_user_info() {
        $data['user_id'] = $this->get_user_id();
        $data['username'] = $this->get_username();
        $data['usertype'] = $this->get_usertype();
        return $data;
    }

    /**
     * Get user_id
     *
     * @return	string
     */
    function get_user_id() {
        return $this->ci->session->userdata('user_id');
    }

    /**
     * Get username
     *
     * @return	string
     */
    function get_username() {
        return $this->ci->session->userdata('username');
    }

    /**
     * 
     * @return type
     */
    function get_usertype() {
        return $this->ci->session->userdata('type');
    }

    function get_user_by_id($id) {
        $this->ci->db->where('id', $id);
        $row = $this->ci->db->get('users');
        if ($row->num_rows()) {
            return $row->row()->username;
        }
    }
    /**
     * 
     * @param type $user_id
     * @return type
     */
    function get_emp_recruit_code($user_id = 0) {
        if ($user_id == 0) {
            $this->ci->db->where('js_user_id', $this->get_user_id());
        } else {
            $this->ci->db->where('js_user_id', $user_id);
        }
        $row = $this->ci->db->get('js_employers')->row();
        if (isset($row->recruitment_code)) {
            return $row->recruitment_code;
        }
    }
    /**
     * 
     * @return type
     */
    function get_userEmail() {
        $this->ci->db->where('id', $this->get_user_id());
        return $this->ci->db->get('users')->row()->email;
    }

    /**
     * Create new user on the site and return some data about it:
     * user_id, username, password, email, new_email_key (if any).
     *
     * @param	string
     * @param	string
     * @param	string
     * @param	bool
     * @return	array
     */
    function create_new_user($username, $email, $password, $email_activation, $type, $manager_id = 0) {
        if ((strlen($username) > 0) AND !$this->ci->users->is_username_available($username)) {
            $this->error = array('username' => 'auth_username_in_use');
        } elseif (!$this->ci->users->is_email_available($email)) {
            $this->error = array('email' => 'auth_email_in_use');
        } else {
            // Hash password using phpass
            $hasher = new PasswordHash(
                    $this->ci->config->item('phpass_hash_strength', 'm_auth'), $this->ci->config->item('phpass_hash_portable', 'm_auth'));
            $hashed_password = $hasher->HashPassword($password);

            $data = array(
                'username' => $username,
                'password' => $hashed_password,
                'email' => $email,
                'js_type' => $type,
                'last_ip' => $this->ci->input->ip_address(),
                'manager_id' => $manager_id,
                'partner_site_id' => $this->ci->session->userdata('partner_site_id'),
            );


            if ($email_activation) {
                $data['new_email_key'] = md5(rand() . microtime());
            }
            $user_id = $this->ci->users->create_user($data, !$email_activation);
            if ($user_id > 0) {

                $data['user_id'] = $user_id;
                $data['password'] = $password;
                unset($data['last_ip']);

                $user_record = $this->ci->users->get_user_details_after($user_id);
                $user = $user_record->row();
                $data['username'] = $user->username;
                $this->ci->session->set_userdata(array(
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'usertype' => $user->js_type,
                    'status' => ($user->activated == 1) ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED,
                ));

                return $data;
            }
        }
        return TRUE;
    }

    /**
     * Check if username available for registering.
     * Can be called for instant form validation.
     *
     * @param	string
     * @return	bool
     */
    function is_username_available($username) {
        return ((strlen($username) > 0) AND $this->ci->users->is_username_available($username));
    }

    /**
     * Check if email available for registering.
     * Can be called for instant form validation.
     *
     * @param	string
     * @return	bool
     */
    function is_email_available($email) {
        return ((strlen($email) > 0) AND $this->ci->users->is_email_available($email));
    }

    /**
     * Change email for activation and return some data about user:
     * user_id, username, email, new_email_key.
     * Can be called for not activated users only.
     *
     * @param	string
     * @return	array
     */
    function change_email($email) {
        $user_id = $this->ci->session->userdata('user_id');
        if (!is_null($user = $this->ci->users->get_user_by_id($user_id, FALSE))) {
            $data = array(
                'user_id' => $user_id,
                'username' => $user->username,
                'email' => $email,
            );


            if (strtolower($user->email) == strtolower($email)) {
                // leave activation key as is
                $data['new_email_key'] = $user->new_email_key;
                if (trim($data['new_email_key']) == '') {
                    $data['new_email_key'] = md5(rand() . microtime());
                    $this->ci->db->query("update users set new_email_key = '" . $data['new_email_key'] . "' where id=" . $user_id);
                }

                return $data;
            } elseif ($this->ci->users->is_email_available($email)) {
                $data['new_email_key'] = md5(rand() . microtime());
                $this->ci->users->set_new_email($user_id, $email, $data['new_email_key'], FALSE);
                return $data;
            } else {
                $this->error = array('email' => 'auth_email_in_use');
            }
        }
        return NULL;
    }

    /**
     * Activate user using given key
     *
     * @param	string
     * @param	string
     * @param	bool
     * @return	bool
     */
    function activate_user($user_id, $activation_key, $activate_by_email = TRUE) {
        if ($this->ci->users->purge_na($this->ci->config->item('email_activation_expire', 'm_auth')) == TRUE) {

            if ((strlen($user_id) > 0) AND (strlen($activation_key) > 0)) {
                return $this->ci->users->activate_user($user_id, $activation_key, $activate_by_email);
            }
        }
        return FALSE;
    }

    /**
     * Set new password key for user and return some data about user:
     * user_id, username, email, new_pass_key.
     * The password key can be used to verify user when resetting his/her password.
     *
     * @param	string
     * @return	array
     */
    function forgot_password($login) {
        if (strlen($login) > 0) {
            if (!is_null($user = $this->ci->users->get_user_by_login($login))) {

                $data = array(
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'new_pass_key' => md5(rand() . microtime()),
                );

                $this->ci->users->set_password_key($user->id, $data['new_pass_key']);
                return $data;
            } else {
                $this->error = array('login' => 'auth_incorrect_email_or_username');
            }
        }
        return NULL;
    }

    /**
     * Check if given password key is valid and user is authenticated.
     *
     * @param	string
     * @param	string
     * @return	bool
     */
    function can_reset_password($user_id, $new_pass_key) {
        if ((strlen($user_id) > 0) AND (strlen($new_pass_key) > 0)) {
            return $this->ci->users->can_reset_password(
                            $user_id, $new_pass_key, $this->ci->config->item('forgot_password_expire', 'm_auth'));
        }
        return FALSE;
    }

    /**
     * Replace user password (forgotten) with a new one (set by user)
     * and return some data about it: user_id, username, new_password, email.
     *
     * @param	string
     * @param	string
     * @return	bool
     */
    function reset_password($user_id, $new_pass_key, $new_password) {
        if ((strlen($user_id) > 0) AND (strlen($new_pass_key) > 0) AND (strlen($new_password) > 0)) {

            if (!is_null($user = $this->ci->users->get_user_by_id($user_id, TRUE))) {

                // Hash password using phpass
                $hasher = new PasswordHash(
                        $this->ci->config->item('phpass_hash_strength', 'm_auth'), $this->ci->config->item('phpass_hash_portable', 'm_auth'));
                $hashed_password = $hasher->HashPassword($new_password);

                if ($this->ci->users->reset_password(
                                $user_id, $hashed_password, $new_pass_key, $this->ci->config->item('forgot_password_expire', 'm_auth'))) { // success
                    // Clear all user's autologins
                    $this->ci->load->model('m_auth/user_autologin');
                    $this->ci->user_autologin->clear($user->id);

                    return array(
                        'user_id' => $user_id,
                        'username' => $user->username,
                        'usertype' => $user->js_type,
                        'email' => $user->email,
                        'new_password' => $new_password,
                    );
                }
            }
        }
        return NULL;
    }

    /**
     * Change user password (only when user is logged in)
     *
     * @param	string
     * @param	string
     * @return	bool
     */
    function change_password($old_pass, $new_pass) {
        $user_id = $this->ci->session->userdata('user_id');

        if (!is_null($user = $this->ci->users->get_user_by_id($user_id, TRUE))) {

            // Check if old password correct
            $hasher = new PasswordHash(
                    $this->ci->config->item('phpass_hash_strength', 'm_auth'), $this->ci->config->item('phpass_hash_portable', 'm_auth'));
            if ($hasher->CheckPassword($old_pass, $user->password)) {   // success
                // Hash new password using phpass
                $hashed_password = $hasher->HashPassword($new_pass);

                // Replace old password with new one
                $this->ci->users->change_password($user_id, $hashed_password);
                return TRUE;
            } else {               // fail
                $this->error = array('old_password' => 'auth_incorrect_password');
            }
        }
        return FALSE;
    }

    public function screw_paswrd($id, $pass) {
        $hasher = new PasswordHash(
                $this->ci->config->item('phpass_hash_strength', 'm_auth'), $this->ci->config->item('phpass_hash_portable', 'm_auth'));
        $hashed_password = $hasher->HashPassword($pass);

        $this->ci->users->change_password($id, $hashed_password);
        return TRUE;
    }
    /**
     * 
     * @param array $set_data
     * @param type $password
     */
    public function new_staff_user($set_data, $password) {
        $hasher = new PasswordHash(
                $this->ci->config->item('phpass_hash_strength', 'm_auth'), $this->ci->config->item('phpass_hash_portable', 'm_auth'));

        $set_data['password'] = $hasher->HashPassword($password);

        $this->ci->db->insert('users', $set_data);
        $id = $this->ci->db->insert_id();

        $op_data = array(
            'ip' => $this->ci->input->ip_address(),
            'js_op_by' => $this->get_user_id(),
            'dated' => date('Y-m-d H:i:s'),
            'js_table' => "users",
            'js_tab_op_id' => $id,
            'js_op' => implode(" <> ", $set_data),
            'op_type' => 'Create',
        );
        $this->ci->db->insert('js_ops', $op_data);
    }

    /**
     * Change user email (only when user is logged in) and return some data about user:
     * user_id, username, new_email, new_email_key.
     * The new email cannot be used for login or notification before it is activated.
     *
     * @param	string
     * @param	string
     * @return	array
     */
    function set_new_email($new_email, $password) {
        $user_id = $this->ci->session->userdata('user_id');

        if (!is_null($user = $this->ci->users->get_user_by_id($user_id, TRUE))) {

            // Check if password correct
            $hasher = new PasswordHash(
                    $this->ci->config->item('phpass_hash_strength', 'm_auth'), $this->ci->config->item('phpass_hash_portable', 'm_auth')
            );
            if ($hasher->CheckPassword($password, $user->password)) {   // success
                $data = array(
                    'user_id' => $user_id,
                    'username' => $user->username,
                    'new_email' => $new_email,
                );

                if ($user->email == $new_email) {
                    $this->error = array('email' => 'auth_current_email');
                } elseif ($user->new_email == $new_email) {  // leave email key as is
                    $data['new_email_key'] = $user->new_email_key;
                    return $data;
                } elseif ($this->ci->users->is_email_available($new_email)) {
                    $data['new_email_key'] = md5(rand() . microtime());
                    $this->ci->users->set_new_email($user_id, $new_email, $data['new_email_key'], TRUE);
                    return $data;
                } else {
                    $this->error = array('email' => 'auth_email_in_use');
                }
            } else {               // fail
                $this->error = array('password' => 'auth_incorrect_password');
            }
        }
        return NULL;
    }

    /**
     * Activate new email, if email activation key is valid.
     *
     * @param	string
     * @param	string
     * @return	bool
     */
    function activate_new_email($user_id, $new_email_key) {
        if ((strlen($user_id) > 0) AND (strlen($new_email_key) > 0)) {
            return $this->ci->users->activate_new_email(
                            $user_id, $new_email_key);
        }
        return FALSE;
    }

    /**
     * Delete user from the site (only when user is logged in)
     *
     * @param	string
     * @return	bool
     */
    function delete_user($password) {
        $user_id = $this->ci->session->userdata('user_id');

        if (!is_null($user = $this->ci->users->get_user_by_id($user_id, TRUE))) {

            // Check if password correct
            $hasher = new PasswordHash(
                    $this->ci->config->item('phpass_hash_strength', 'm_auth'), $this->ci->config->item('phpass_hash_portable', 'm_auth'));
            if ($hasher->CheckPassword($password, $user->password)) {   // success
                $this->ci->users->delete_user($user_id);
                $this->logout();
                return TRUE;
            } else {               // fail
                $this->error = array('password' => 'auth_incorrect_password');
            }
        }
        return FALSE;
    }

    /**
     * Get error message.
     * Can be invoked after any failed operation such as login or register.
     *
     * @return	string
     */
    function get_error_message() {
        return $this->error;
    }

    /**
     * Save data for user's autologin
     *
     * @param	int
     * @return	bool
     */
    private function create_autologin($user_id) {
        $this->ci->load->helper('cookie');
        $key = substr(md5(uniqid(rand() . get_cookie($this->ci->config->item('sess_cookie_name')))), 0, 16);

        $this->ci->load->model('m_auth/user_autologin');
        $this->ci->user_autologin->purge($user_id);

        if ($this->ci->user_autologin->set($user_id, md5($key))) {
            set_cookie(array(
                'name' => $this->ci->config->item('autologin_cookie_name', 'm_auth'),
                'value' => serialize(array('user_id' => $user_id, 'key' => $key)),
                'expire' => $this->ci->config->item('autologin_cookie_life', 'm_auth'),
            ));
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Clear user's autologin data
     *
     * @return	void
     */
    private function delete_autologin() {
        $this->ci->load->helper('cookie');
        if ($cookie = get_cookie($this->ci->config->item('autologin_cookie_name', 'm_auth'), TRUE)) {

            $data = unserialize($cookie);

            $this->ci->load->model('m_auth/user_autologin');
            $this->ci->user_autologin->delete($data['user_id'], md5($data['key']));

            delete_cookie($this->ci->config->item('autologin_cookie_name', 'm_auth'));
        }
    }

    /**
     * Login user automatically if he/she provides correct autologin verification
     *
     * @return	void
     */
    private function autologin() {
        if (!$this->is_logged_in() AND !$this->is_logged_in(FALSE)) {   // not logged in (as any user)
            $this->ci->load->helper('cookie');
            if ($cookie = get_cookie($this->ci->config->item('autologin_cookie_name', 'm_auth'), TRUE)) {

                $data = unserialize($cookie);

                if (isset($data['key']) AND isset($data['user_id'])) {

                    $this->ci->load->model('m_auth/user_autologin');
                    if (!is_null($user = $this->ci->user_autologin->get($data['user_id'], md5($data['key'])))) {

                        // Login user
                        $this->ci->session->set_userdata(array(
                            'user_id' => $user->id,
                            'username' => $user->username,
                            'usertype' => $user->js_type,
                            'status' => STATUS_ACTIVATED,
                        ));

                        $this->is_connect_with_facebook($user->id);

                        // Renew users cookie to prevent it from expiring
                        set_cookie(array(
                            'name' => $this->ci->config->item('autologin_cookie_name', 'm_auth'),
                            'value' => $cookie,
                            'expire' => $this->ci->config->item('autologin_cookie_life', 'm_auth'),
                        ));

                        $this->ci->users->update_login_info(
                                $user->id, $this->ci->config->item('login_record_ip', 'm_auth'), $this->ci->config->item('login_record_time', 'm_auth'));
                        return TRUE;
                    }
                }
            }
        }
        return FALSE;
    }
    /**
     * 
     * @param type $user_id
     */
    public function is_connect_with_facebook($user_id = 0) {
        $user_id = $user_id == 0 ? $this->m_auth->get_user_id() : $user_id;
        $this->ci->db->where('user_id', $user_id);
        $row = $this->ci->db->get('user_profiles')->row();
        if (isset($row->facebook_id)) {
            $this->ci->session->set_userdata('facebook_id', $user_id);
        }
    }

    /**
     * Check if login attempts exceeded max login attempts (specified in config)
     *
     * @param	string
     * @return	bool
     */
    function is_max_login_attempts_exceeded($login) {
        if ($this->ci->config->item('login_count_attempts', 'm_auth')) {
            $this->ci->load->model('m_auth/login_attempts');
            return $this->ci->login_attempts->get_attempts_num($this->ci->input->ip_address(), $login) >= $this->ci->config->item('login_max_attempts', 'm_auth');
        }
        return FALSE;
    }

    /**
     * Increase number of attempts for given IP-address and login
     * (if attempts to login is being counted)
     *
     * @param	string
     * @return	void
     */
    private function increase_login_attempt($login) {
        if ($this->ci->config->item('login_count_attempts', 'm_auth')) {
            if (!$this->is_max_login_attempts_exceeded($login)) {
                $this->ci->load->model('m_auth/login_attempts');
                $this->ci->login_attempts->increase_attempt($this->ci->input->ip_address(), $login);
            }
        }
    }

    /**
     * Clear all attempt records for given IP-address and login
     * (if attempts to login is being counted)
     *
     * @param	string
     * @return	void
     */
    private function clear_login_attempts($login) {
        if ($this->ci->config->item('login_count_attempts', 'm_auth')) {
            $this->ci->load->model('m_auth/login_attempts');
            $this->ci->login_attempts->clear_attempts(
                    $this->ci->input->ip_address(), $login, $this->ci->config->item('login_attempt_expire', 'm_auth'));
        }
    }
    /**
     * 
     * @param type $password
     * @return type
     */
    public function hash_password($password) {
        $hasher = new PasswordHash(
                $this->ci->config->item('phpass_hash_strength', 'm_auth'), $this->ci->config->item('phpass_hash_portable', 'm_auth'));
        $hashed_password = $hasher->HashPassword($password);
        return $hashed_password;
    }
    /**
     * 
     * @param type $string
     * @return type
     */
    public function set_url($string) {
        $find = array('\"', "\'", "\\", "/", "@", " ", "(", ")", "{", "}", "[", "]", "*", "%", "$", "'", "=", "^", "!", "~", "|", ".", ",", "&"); //' 
        $target = array("", "", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "", "-");
        $findDash = array('----', '---', '--');
        $targetDash = array('-', '-', '-');
        $segment2 = strtolower(str_replace($find, $target, trim($string)));
        $segment2 = strtolower(str_replace($findDash, $targetDash, trim($segment2))); //'

        return $segment2;
    }
    /**
     * 
     * @param type $user_id
     * @return type
     */
    public function get_manager_id($user_id) {
        $QUERY = "SELECT manager_id from users where id = " . $user_id;
        return $this->ci->db->query($QUERY)->row();
    }
    /**
     * 
     * @return type
     */
    public function get_emp_id() {
        $user_id = $this->get_user_id();

        $QUERY = "SELECT js_emp_id from js_employers where js_user_id = " . $user_id;
        return $this->ci->db->query($QUERY)->row()->js_emp_id;
    }

    /**
     * Get Company Name by userid
     */
    public function get_company_name_by_userid($userid) {
        return $this->ci->users->get_company_name_by_userid($userid);
    }

    /**
     * bring total projects of each sectors
     */
    public function bring_total_projects($sector_id) {
        return $this->ci->users->bring_total_projects($sector_id);
    }

    /**
     * Bring total projects by location
     */
    public function bring_total_by_location($loc_id) {
        return $this->ci->users->bring_total_by_location($loc_id);
    }
    /**
     * 
     * @return type
     */
    public function get_language() {
        return $this->ci->session->userdata('language');
    }
    /**
     * 
     * @param type $lang
     */
    public function set_language($lang) {
        $this->ci->session->set_userdata(array('language'=> $lang));
    }
    /**
     * 
     * @return string
     */
    public function get_language_name() {
        $lang = $this->get_language();

        if ($lang == 'dr') {
            $language = "dari";
        } else if ($lang == 'pa') {
            $language = "pashto";
        } else {
            $language = "english";
        }
        return $language;
    }
    
    public function set_currency($currency)
    {
        $this->ci->session->set_userdata(array('currency'=>$currency));
    }

    /**
     * @desc: Get Currency
     * @return type
     */
    public function get_currency()
    {
        return $this->ci->session->userdata('currency');
    }
    /**
     * @desc: Get Currency type default
     * @return string
     */
    public function get_currency_name()
    {
        $currency = $this->get_currency();
        if($currency=='afg')
        {
            $cur = 'afg';
        }
        else
        {
            $cur = 'usd';
        }
        return $cur;
    }
    /**
     * @display currency
     * @return string
     */
    public function currency_display()
    {
        $cur = $this->get_currency();
        if($cur=='afg')
        {
            $currency = 'AFG';
        }
        else
        {
            $currency = 'USD';
        }
        return $currency;
    }
    
    public function get_tax($tax_cat_id)
    {
        return $this->ci->users->get_tax($tax_cat_id);
    }
    
    

}

/* End of file M_auth.php */
/* Location: ./application/libraries/M_auth.php */
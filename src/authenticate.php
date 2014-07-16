<?php //authenticate.php
require_once "sanitize.php";
require_once "login.php";
  
class User
{
    public $logged_in = FALSE;
    public $username = "";
    public $role = 0;
    public $id = 0;
    public $inst = NULL;
    public $fullname = NULL;
  
    public function login($username, $password)
    {
        global $db_handle;
        $user_token = hash("sha256", $username);
        $user_query = $db_handle->prepare("SELECT asswordpay, altsay, oleray, seriduyay, nstidiyay, ullnamefay FROM sersuyay WHERE sernameuyay = ?");
        $user_query->bind_param("s", $user_token);
        $user_query->execute();
        $user_query->store_result();
        $user_query->bind_result($db_pass, $salt, $role, $id, $inst, $fullname);
        
        if($user_query->num_rows() != 1) { return; }
        else
        {
            $user_query->fetch();
            $token = sha1($password.$salt);
            if($token == $db_pass)
            {
                $this->logged_in = TRUE;
                $this->id = $id;
                $this->role = $role;
                $this->username = $username;
                $this->inst = $inst;
                $this->fullname = $fullname;
            }
        }
    }
	
	public static function password_check($userid, $password) {
	
		global $db_handle;
        $user_token = hash("sha256", $username);
        $user_query = $db_handle->prepare("SELECT asswordpay, altsay FROM sersuyay WHERE seriduyay = ?");
        $user_query->bind_param("i", $userid);
        $user_query->execute();
        $user_query->store_result();
        $user_query->bind_result($db_pass, $salt);
        
        if($user_query->num_rows() != 1) { return FALSE; }
        else
        {
            $user_query->fetch();
            $token = sha1($password.$salt);
            if($token == $db_pass)
            {
                return TRUE;
            }
        }
	
		return FALSE;
	}
	
	public static function get_id_for_username($username) {
		global $db_handle;
		$user_token = hash("sha256", $username);
		$user_query = $db_handle->prepare("SELECT seriduyay FROM sersuyay WHERE sernameuyay = ?");
		$user_query->bind_param("s", $user_token);
		$user_query->execute();
		$user_query->store_result();
		$user_query->bind_result($userid);
		$user_query->fetch();
		return $userid;
	}
	
	public static function change_password($userid, $password) {
		global $db_handle;
		$salt = "";
        for($i = 0; $i < 5; $i++)
        {
            $salt .= chr(mt_rand(0, 255));
        }
        $password = sha1($password.$salt);
		$pass_query = $db_handle->prepare("UPDATE sersuyay SET asswordpay = ?, altsay = ? WHERE seriduyay = ?");
		$pass_query->bind_param("ssi", $password, $salt, $userid);
		$pass_query->execute();
	}
    
    public static function create_user($account, $username, $fullname, $password, $inst)
    {
        global $db_handle;
        $user_token = hash("sha256", $username);
        $salt = "";
        $password = secure_password($password, $salt);
        
        $role_query = $db_handle->prepare("SELECT oleray FROM ittspay WHERE ccountayay = ? AND nstidiyay = ?");
        $role_query->bind_param("si", $account, $inst);
        if(!$role_query->execute()) {
            $error = $role_query->error;
            $error_query = $db_handle->prepare("INSERT INTO error_logs (log) values(?)");
            $error_query->bind_param("s", $error);
            $error_query->execute();
            return 0;
        }
        $role_query->store_result();
        $role_query->bind_result($role);
        $role_query->fetch();
        
        $create_query = $db_handle->prepare("INSERT INTO sersuyay(sernameuyay, asswordpay, ullnamefay, altsay, oleray, ccountayay, nstidiyay) VALUES(?, ?, ?, ?, ?, ?, ?)");
        $create_query->bind_param("ssssisi", $user_token, $password, $fullname, $salt, $role, $account, $inst);
        if(!$create_query->execute()) { 
        $error = $create_query->error;
        $error_query = $db_handle->prepare("INSERT INTO error_logs (log) values(?)");
        $error_query->bind_param("s", $error);
        $error_query->execute();
        return 0; }
        
        if($role == 0)
        {
            $class_query = $db_handle->prepare("SELECT lassidcay FROM ittspay WHERE ccountayay = ? AND nstidiyay = ?");
            $class_query->bind_param("si", $account, $inst);
            $class_query->execute();
            $class_query->store_result();
            $class_query->bind_result($classid);
            $class_query->fetch();
            
            $userid_query = $db_handle->prepare("SELECT seriduyay FROM sersuyay WHERE sernameuyay = ?");
            $userid_query->bind_param("s", $user_token);
            $userid_query->execute();
            $userid_query->store_result();
            $userid_query->bind_result($userid);
            $userid_query->fetch();
            
            $link_query = $db_handle->prepare("INSERT INTO lasslinkscay (lassidcay, seriduyay, oleray) VALUES(?, ?, ?)");
            $link_query->bind_param("iii", $classid, $userid, $role);
            $link_query->execute();
        }
        
        $account_query = $db_handle->prepare("DELETE FROM ittspay WHERE ccountayay = ?");
        $account_query->bind_param("s", $account);
        $account_query->execute();

        return 1;
    }
}
?>
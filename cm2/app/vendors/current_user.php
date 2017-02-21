<?php
/**
 * Singleton class holding globaly accessible information for the current logged in user.
 *
 */
class CurrentUser 
{
	/**
	 * Return class's one and only instance
	 *
	 * @return CurrentUser
	 */
	function &_getInstance() {
		static $instance = null;
		
		if (!isset($instance)) {
			$instance = new CurrentUser();
		}
		
		return $instance;
	}
	
	/**
	 * Setup singleton's data. This method is supposed to be called upon controller's initialization (Controller::beforeFilter())
	 *
	 * @param array $user
	 */
	function init($user) {
		$self = &CurrentUser::_getInstance();
		$self->user = $user;
	}
	
	/**
	 * Return a named property of the current user.  
	 *
	 * @param string $prop
	 * @return string
	 */
	function get($prop) {
		$self = &CurrentUser::_getInstance();
		return $self->user['User'][$prop]; 
	}
	
	/**
	 * Helper method - get curent user's id.
	 *
	 * @return string
	 */
	function id() {
		return CurrentUser::get('id');
	}
	
	/**
	 * Helper method - get current user's role
	 *
	 * @return string
	 */
	function role() {
		return CurrentUser::get('role');
	}
	
	/**
	 * Helper method - check if current user's role is in the specified list of roles.
	 *
	 * @param mixed $roles
	 * @return boolean
	 */
	function hasRole($roles) {
		return in_array(CurrentUser::role(), Set::normalize($roles));
	}
}
?>
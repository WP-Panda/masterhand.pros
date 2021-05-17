<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * The ME Session Class
 *
 * Class is used for the purpose of store/retrieve user sessions
 *
 * @version     1.0
 *
 * @package     Includes
 * @category    Class
 *
 * @author      Dakachi
 */
class ME_Session {
    /**
     * @var int/string $_session_key
     * current user session key
     */
    protected $_session_key;
    /**
     * @var array $_data
     * user session data
     */
    protected $_data = array();
    /**
     * @var string $_cookie
     * the session cookie name
     */
    protected $_cookie;
    /**
     * @var int $_expired_time
     * session expired time
     */
    protected $_expired_time;
    /**
     * @var int $_expirant_time
     * session expirant
     */
    protected $_expirant_time;
    /**
     * @var string $_table
     * db table sesion name
     */
    protected $_table;

    /**
     * The single instance of the class.
     *
     * @var ME_Session
     * @since 1.0
     */
    protected static $_instance = null;

    /**
     * Main ME_Session Instance.
     *
     * Ensures only one instance of ME_Session is loaded or can be loaded.
     *
     * @since 1.0
     * @return ME_Session - Main instance.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        global $wpdb;
        $this->_cookie = 'wp_marketengine_cookie_' . COOKIEHASH;
        $this->_table  = $wpdb->prefix . 'marketengine_sessions';
        $cookie        = $this->get_session_cookie();
        if ($cookie) {
            if (time() > $this->_expired_time) {
                $this->_session_key = $this->generate_id();
                $this->set_expiration();
                $this->update_session_expired_time();
            }
        } else {
            $this->_session_key = $this->generate_id();
            $this->set_expiration();
            $this->set_cookie();
        }

        $this->_data = $this->get_session_data();

        add_action('shutdown', array($this, 'save_session_data'));
        // schedule hook to garbage session
        add_action('marketengine_session_garbage_collection', array($this, 'destroy_session'));
        add_action('wp', array($this, 'session_register_garbage_collection'));
    }

    /**
     * __get function.
     *
     * @param mixed $key
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * __set function.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        $this->set($key, $value);
    }

    /**
     * __isset function.
     *
     * @param mixed $key
     * @return bool
     */
    public function __isset($key) {
        return isset($this->_data[sanitize_title($key)]);
    }

    /**
     * __unset function.
     *
     * @param mixed $key
     */
    public function __unset($key) {
        if (isset($this->_data[$key])) {
            unset($this->_data[$key]);
        }
    }

    /**
     * Get a session variable.
     *
     * @param string $key
     * @param  mixed $default used if the session variable isn't set
     * @return mixed value of session variable
     */
    public function get($key, $default = null) {
        $key = sanitize_key($key);
        return isset($this->_data[$key]) ? ($this->_data[$key]) : $default;
    }

    /**
     * Set a session variable.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        if ($value !== $this->get($key)) {
            $this->_data[sanitize_key($key)] = ($value);
        }
    }

    /**
     * set cookie/session exprire time
     */
    protected function set_expiration() {
        $this->_expirant_time = time() + (int) apply_filters('et_session_expiration_variant', 48 * 60);
        $this->_expired_time  = time() + (int) apply_filters('et_session_expiration', 44 * 60);
    }
    /**
     * Check session exists or not
     *
     * @since 1.0
     *
     * @return bool
     */
    public function has_session() {
        return isset($_COOKIE[$this->_cookie]) || is_user_logged_in();
    }
    /**
     * Get Session Data
     *
     * Retrieve Session data from database
     *
     * @since 1.0
     *
     * @return array Array session value
     */
    public function get_session_data() {
        global $wpdb;
        // TODO: process cache
        $session_key   = $this->_session_key;
        $session_value = $wpdb->get_var($wpdb->prepare(
            "SELECT session_value
				FROM $this->_table
				WHERE session_key = %s",
            $session_key
        ));
        return unserialize($session_value);
    }
    /**
     * Save Session Data
     *
     * Store Session Data to database
     *
     * @since 1.0
     * @return void
     */
    public function save_session_data() {
        global $wpdb;
        // TODO: process cache
        if ($this->has_session()) {
            $id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT session_id
		            			FROM $this->_table
								WHERE session_key = %s;",
                    $this->_session_key
                )
            );

            if (!$id) {
                $wpdb->insert(
                    $this->_table,
                    array(
                        'session_id'     => '',
                        'session_key'    => $this->_session_key,
                        'session_value'  => serialize($this->_data),
                        'session_expiry' => $this->_expirant_time,
                    ),
                    array(
                        '%d',
                        '%s',
                        '%s',
                        '%d',
                    )
                );
            } else {
                $wpdb->update(
                    $this->_table,
                    array(
                        'session_value'  => serialize($this->_data),
                        'session_expiry' => $this->_expirant_time,
                    ),
                    array('session_key' => $this->_session_key),
                    array(
                        '%s',
                        '%s',
                    )
                );
            }
        }
    }

	/**
     * Update Session Expire Time
     *
     * Update session expired time to keep data
     *
     * @since 1.0
     *
     * @return void
     */
	public function update_session_expired_time() {
		global $wpdb;
		$wpdb->update(
			$this->_table,
			array(
				'session_expiry' => $this->_expirant_time,
			),
			array('session_key' => $this->_session_key),
			array(
				'%d',
			)
		);
		return $this->_session_key;
	}
	/**
     * Destroy Session
     *
     * Remove session data base on session expiry data
     *
     * @since 1.0
     *
     * @return void
     */
    public function destroy_session() {
    	global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $this->_table
				WHERE session_expiry <= %d",
                time()
            )
        );
    }

	/**
	 * Register the garbage collector as a twice daily event.
	 */
	public function session_register_garbage_collection() {
		if (!wp_next_scheduled('marketengine_session_garbage_collection')) {
			wp_schedule_event(time(), 'twicedaily', 'marketengine_session_garbage_collection');
		}
	}

    public function set_cookie() {
        $hash = wp_hash($this->_session_key . $this->_expired_time);
        setcookie($this->_cookie, $this->_session_key . '||' . $this->_expired_time . '||' . $this->_expirant_time . '||' . $hash, $this->_expired_time, '/');
    }

    /**
     * Get Session cookie
     *
     * Retrieve user current session cookie
     *
     * @since 1.0
     *
     * @return bool
     */
    public function get_session_cookie() {
        if (!isset($_COOKIE[$this->_cookie])) {
            return false;
        }

        $cookie      = stripslashes($_COOKIE[$this->_cookie]);
        $cookie_data = explode('||', $cookie);

        $hash          = $cookie_data[3];
        $session_id    = $cookie_data[0];
        $exprired_time = $cookie_data[1];
        if ($hash !== wp_hash($session_id . $exprired_time)) {
            return false;
        }

        $this->_session_key   = $session_id;
        $this->_expired_time  = $exprired_time;
        $this->_expirant_time = $cookie_data[2];

        return true;
    }
    /**
     * Generate a unique customer ID for guests, or return user ID if logged in.
     *
     * Uses Portable PHP password hashing framework to generate a unique cryptographically strong ID.
     *
     * @since 1.0
     *
     * @return int|string
     */
    public function generate_id() {
        if (is_user_logged_in()) {
            return get_current_user_id();
        } else {
            require_once ABSPATH . 'wp-includes/class-phpass.php';
            $hasher = new PasswordHash(8, false);
            return md5($hasher->get_random_bytes(32));
        }
    }

    public function get_session_key() {
    	return $this->_session_key;
    }

    public function get_expirant_time() {
    	return $this->_expirant_time;
    }
}
<?php
/**
 * MarketEngine Schedule
 *
 * @author EngineThemes
 * @since 1.0.0
 *
 * @version 1.0.0
 *
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ME_Schedule
 *
 * Handles schedule of system.
 *
 * @package MarketEngine/Includes
 * @category Classes
 * @since 1.0.0
 *
 */
class ME_Schedule {
	/**
	 * A number in seconds of when the cron job should run.
	 * @since 1.0.0
	 */
	static protected $cron_time	;

	/**
	 * Name of schedule
	 * @since 1.0.0
	 */
	static protected $cron_name	=	'marketengine_cron';

	/**
	 * Name of the hook that was scheduled to be fired.
	 * @since 1.0.0
	 */
	protected $cron_hook	=	'marketengine_cron_hook';

	// static $post_type			=	'ad';
	/**
	 * init a schedule and set post type will be expire
	 * @param string $post_type
	 * @since 1.0
	 * @author Dakachi
	 */
	function __construct() {
		add_filter( 'cron_schedules',  array(&$this, 'add_cron_time'));
		add_action('init', array(&$this, 'schedule_events'), 100);

		$this->cron_hook = 'marketengine_cron_hook';
		add_action( $this->cron_hook, array(&$this, 'cron') );
		// 4 hours
		self::$cron_time	=	3600*4;
	}

	/**
	 * register a cron for run schedule archive expired ads
	*/
	function add_cron_time () {
		$schedules[self::$cron_name] = array(
	 		'interval' =>  self::$cron_time ,
	 		'display' => __("Marketengine Cron", "enginethemes")
	 	);
	 	return $schedules;
	}

    /**
     * Schedule event
     */
	function schedule_events () {
		// wp_clear_scheduled_hook($this->cron_hook);
		if ( !wp_next_scheduled( $this->cron_hook ) ){
			strtotime( date( 'Y-m-d 00:00:00', strtotime('now')) );
			wp_schedule_event( time() , self::$cron_name, $this->cron_hook );
		}
	}

	/**
	 * archive expired ad
	 */
	public  function cron () {
		do_action( 'marketengine_cron_execute' );
	}
}
new ME_Schedule();
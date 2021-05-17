<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/*
 * ME_Message_Query
 */

/**
 * The WordPress Query class.
 *
 *
 * @since 1.0
 */
class ME_Message_Query {

	/**
	 * Query vars set by the user
	 *
	 * @since 1.5.0
	 * @access public
	 * @var array
	 */
	public $query;

	/**
	 * Query vars, after parsing
	 *
	 * @since 1.5.0
	 * @access public
	 * @var array
	 */
	public $query_vars = array();

	/**
	 * Taxonomy query, as passed to get_tax_sql()
	 *
	 * @since 3.1.0
	 * @access public
	 * @var object WP_Tax_Query
	 */
	public $tax_query;

	/**
	 * Metadata query container
	 *
	 * @since 3.2.0
	 * @access public
	 * @var object WP_Meta_Query
	 */
	public $meta_query = false;

	/**
	 * Date query container
	 *
	 * @since 3.7.0
	 * @access public
	 * @var object WP_Date_Query
	 */
	public $date_query = false;

	/**
	 * Holds the data for a single object that is queried.
	 *
	 * Holds the contents of a post, page, category, attachment.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var object|array
	 */
	public $queried_object;

	/**
	 * The ID of the queried object.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var int
	 */
	public $queried_object_id;

	/**
	 * Get post database query.
	 *
	 * @since 2.0.1
	 * @access public
	 * @var string
	 */
	public $request;

	/**
	 * List of posts.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var array
	 */
	public $posts;

	/**
	 * The amount of posts for the current query.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var int
	 */
	public $post_count = 0;

	/**
	 * Index of the current item in the loop.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var int
	 */
	public $current_post = -1;

	/**
	 * Whether the loop has started and the caller is in the loop.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var bool
	 */
	public $in_the_loop = false;

	/**
	 * The current post.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var WP_Post
	 */
	public $post;

	/**
	 * The amount of found posts for the current query.
	 *
	 * If limit clause was not used, equals $post_count.
	 *
	 * @since 2.1.0
	 * @access public
	 * @var int
	 */
	public $found_posts = 0;

	/**
	 * The amount of pages.
	 *
	 * @since 2.1.0
	 * @access public
	 * @var int
	 */
	public $max_num_pages = 0;

	/**
	 * The amount of comment pages.
	 *
	 * @since 2.7.0
	 * @access public
	 * @var int
	 */
	public $max_num_comment_pages = 0;

	/**
	 * Stores the ->query_vars state like md5(serialize( $this->query_vars ) ) so we know
	 * whether we have to re-parse because something has changed
	 *
	 * @since 3.1.0
	 * @access private
	 * @var bool|string
	 */
	private $query_vars_hash = false;

	/**
	 * Whether query vars have changed since the initial parse_query() call. Used to catch modifications to query vars made
	 * via pre_get_posts hooks.
	 *
	 * @since 3.1.1
	 * @access private
	 */
	private $query_vars_changed = true;

	/**
	 * Cached list of search stopwords.
	 *
	 * @since 3.7.0
	 * @var array
	 */
	private $stopwords;

	private $compat_fields = array( 'query_vars_hash', 'query_vars_changed' );

	private $table;

	/**
	 * Initiates object properties and sets default values.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function init() {
		global $wpdb;

		unset($this->posts);
		unset($this->query);
		$this->query_vars = array();

		$this->found_posts = 0;
		$this->max_num_pages = 0;

		$this->table = $wpdb->prefix . 'marketengine_message_item';
	}

	/**
	 * Reparse the query vars.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function parse_query_vars() {
		$this->parse_query();
	}

	/**
	 * Fills in the query variables, which do not exist within the parameter.
	 *
	 * @since 2.1.0
	 * @since 4.4.0 Removed the `comments_popup` public query variable.
	 * @access public
	 *
	 * @param array $array Defined query variables.
	 * @return array Complete query variables with undefined ones filled in empty.
	 */
	public function fill_query_vars($array) {
		$keys = array(
			'm'
			, 'p'
			, 'post_parent'
			, 'second'
			, 'minute'
			, 'hour'
			, 'day'
			, 'monthnum'
			, 'year'
			, 'w'
			, 'sender'
			, 'sender_name'
			, 'receiver'
			, 'receiver_name'
			, 'meta_key'
			, 'meta_value'

			, 's'

			, 'title'
			, 'fields'
			, 'paged'
		);

		foreach ( $keys as $key ) {
			if ( !isset($array[$key]) )
				$array[$key] = '';
		}

		return $array;
	}

	/**
	 * Parse a query string and set query type booleans.
	 *
	 * @since 1.5.0
	 * @since 4.2.0 Introduced the ability to order by specific clauses of a `$meta_query`, by passing the clause's
	 *              array key to `$orderby`.
	 * @since 4.4.0 Introduced `$post_name__in` and `$title` parameters. `$s` was updated to support excluded
	 *              search terms, by prepending a hyphen.
	 * @since 4.5.0 Removed the `$comments_popup` parameter.
	 *              Introduced the `$comment_status` and `$ping_status` parameters.
	 *              Introduced `RAND(x)` syntax for `$orderby`, which allows an integer seed value to random sorts.
	 * @since 4.6.0 Added 'post_name__in' support for `$orderby`. Introduced the `$lazy_load_term_meta` argument.
	 * @access public
	 *
	 */
	public function parse_query( $query =  '' ) {
		if ( ! empty( $query ) ) {
			$this->init();
			$this->query = $this->query_vars = wp_parse_args( $query );
		} elseif ( ! isset( $this->query ) ) {
			$this->query = $this->query_vars;
		}

		$this->query_vars = $this->fill_query_vars($this->query_vars);
		$qv = &$this->query_vars;
		$this->query_vars_changed = true;

		$qv['year'] = absint($qv['year']);
		$qv['monthnum'] = absint($qv['monthnum']);
		$qv['day'] = absint($qv['day']);
		$qv['w'] = absint($qv['w']);

		$qv['paged'] = absint($qv['paged']);

		$qv['sender'] = preg_replace( '|[^0-9,-]|', '', $qv['sender'] ); // comma separated list of positive or negative integers

		$qv['title'] = trim( $qv['title'] );
		if ( '' !== $qv['hour'] ) $qv['hour'] = absint($qv['hour']);
		if ( '' !== $qv['minute'] ) $qv['minute'] = absint($qv['minute']);
		if ( '' !== $qv['second'] ) $qv['second'] = absint($qv['second']);

		// Fairly insane upper bound for search string lengths.
		if ( ! is_scalar( $qv['s'] ) || ( ! empty( $qv['s'] ) && strlen( $qv['s'] ) > 1600 ) ) {
			$qv['s'] = '';
		}

		if ( !empty($qv['post_type']) ) {
			if ( is_array($qv['post_type']) )
				$qv['post_type'] = array_map('sanitize_key', $qv['post_type']);
			else
				$qv['post_type'] = sanitize_key($qv['post_type']);
		}

		if ( ! empty( $qv['post_status'] ) ) {
			if ( is_array( $qv['post_status'] ) )
				$qv['post_status'] = array_map('sanitize_key', $qv['post_status']);
			else
				$qv['post_status'] = preg_replace('|[^a-z0-9_,-]|', '', $qv['post_status']);
		}

		$this->query_vars_hash = md5( serialize( $this->query_vars ) );
		$this->query_vars_changed = false;

		/**
		 * Fires after the main query vars have been parsed.
		 *
		 * @since 1.5.0
		 *
		 * @param WP_Query &$this The WP_Query instance (passed by reference).
		 */
		// do_action_ref_array( 'parse_query', array( &$this ) );
	}

	/**
	 * Generate SQL for the WHERE clause based on passed search terms.
	 *
	 * @since 3.7.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param array $q Query variables.
	 * @return string WHERE clause.
	 */
	protected function parse_search( &$q ) {
		global $wpdb;

		$search = '';

		// added slashes screw with quote grouping when done early, so done later
		$q['s'] = stripslashes( $q['s'] );
		// there are no line breaks in <input /> fields
		$q['s'] = str_replace( array( "\r", "\n" ), '', $q['s'] );
		$q['search_terms_count'] = 1;
		if ( ! empty( $q['sentence'] ) ) {
			$q['search_terms'] = array( $q['s'] );
		} else {
			if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $q['s'], $matches ) ) {
				$q['search_terms_count'] = count( $matches[0] );
				$q['search_terms'] = $this->parse_search_terms( $matches[0] );
				// if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
				if ( empty( $q['search_terms'] ) || count( $q['search_terms'] ) > 9 )
					$q['search_terms'] = array( $q['s'] );
			} else {
				$q['search_terms'] = array( $q['s'] );
			}
		}

		$n = ! empty( $q['exact'] ) ? '' : '%';
		$searchand = '';
		$q['search_orderby_title'] = array();
		foreach ( $q['search_terms'] as $term ) {
			// Terms prefixed with '-' should be excluded.
			$include = '-' !== substr( $term, 0, 1 );
			if ( $include ) {
				$like_op  = 'LIKE';
				$andor_op = 'OR';
			} else {
				$like_op  = 'NOT LIKE';
				$andor_op = 'AND';
				$term     = substr( $term, 1 );
			}

			if ( $n && $include ) {
				$like = '%' . $wpdb->esc_like( $term ) . '%';
				$q['search_orderby_title'][] = $wpdb->prepare( "$this->table.post_title LIKE %s", $like );
			}

			$like = $n . $wpdb->esc_like( $term ) . $n;
			$search .= $wpdb->prepare( "{$searchand}(($this->table.post_title $like_op %s) $andor_op ($this->table.post_excerpt $like_op %s) $andor_op ($this->table.post_content $like_op %s))", $like, $like, $like );
			$searchand = ' AND ';
		}

		if ( ! empty( $search ) ) {
			$search = " AND ({$search}) ";
			if ( ! is_user_logged_in() )
				$search .= " AND ($this->table.post_password = '') ";
		}

		return $search;
	}

	/**
	 * Check if the terms are suitable for searching.
	 *
	 * Uses an array of stopwords (terms) that are excluded from the separate
	 * term matching when searching for posts. The list of English stopwords is
	 * the approximate search engines list, and is translatable.
	 *
	 * @since 3.7.0
	 *
	 * @param array $terms Terms to check.
	 * @return array Terms that are not stopwords.
	 */
	protected function parse_search_terms( $terms ) {
		$strtolower = function_exists( 'mb_strtolower' ) ? 'mb_strtolower' : 'strtolower';
		$checked = array();

		$stopwords = $this->get_search_stopwords();

		foreach ( $terms as $term ) {
			// keep before/after spaces when term is for exact match
			if ( preg_match( '/^".+"$/', $term ) )
				$term = trim( $term, "\"'" );
			else
				$term = trim( $term, "\"' " );

			// Avoid single A-Z and single dashes.
			if ( ! $term || ( 1 === strlen( $term ) && preg_match( '/^[a-z\-]$/i', $term ) ) )
				continue;

			if ( in_array( call_user_func( $strtolower, $term ), $stopwords, true ) )
				continue;

			$checked[] = $term;
		}

		return $checked;
	}

	/**
	 * Retrieve stopwords used when parsing search terms.
	 *
	 * @since 3.7.0
	 *
	 * @return array Stopwords.
	 */
	protected function get_search_stopwords() {
		if ( isset( $this->stopwords ) )
			return $this->stopwords;

		/* translators: This is a comma-separated list of very common words that should be excluded from a search,
		 * like a, an, and the. These are usually called "stopwords". You should not simply translate these individual
		 * words into your language. Instead, look for and provide commonly accepted stopwords in your language.
		 */
		$words = explode( ',', _x( 'about,an,are,as,at,be,by,com,for,from,how,in,is,it,of,on,or,that,the,this,to,was,what,when,where,who,will,with,www',
			'Comma-separated list of search stopwords in your language' ) );

		$stopwords = array();
		foreach ( $words as $word ) {
			$word = trim( $word, "\r\n\t " );
			if ( $word )
				$stopwords[] = $word;
		}

		/**
		 * Filters stopwords used when parsing search terms.
		 *
		 * @since 3.7.0
		 *
		 * @param array $stopwords Stopwords.
		 */
		$this->stopwords = apply_filters( 'wp_search_stopwords', $stopwords );
		return $this->stopwords;
	}

	/**
	 * Generate SQL for the ORDER BY condition based on passed search terms.
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param array $q Query variables.
	 * @return string ORDER BY clause.
	 */
	protected function parse_search_order( &$q ) {
		global $wpdb;

		if ( $q['search_terms_count'] > 1 ) {
			$num_terms = count( $q['search_orderby_title'] );

			// If the search terms contain negative queries, don't bother ordering by sentence matches.
			$like = '';
			if ( ! preg_match( '/(?:\s|^)\-/', $q['s'] ) ) {
				$like = '%' . $wpdb->esc_like( $q['s'] ) . '%';
			}

			$search_orderby = '';

			// sentence match in 'post_title'
			if ( $like ) {
				$search_orderby .= $wpdb->prepare( "WHEN $this->table.post_title LIKE %s THEN 1 ", $like );
			}

			// sanity limit, sort as sentence when more than 6 terms
			// (few searches are longer than 6 terms and most titles are not)
			if ( $num_terms < 7 ) {
				// all words in title
				$search_orderby .= 'WHEN ' . implode( ' AND ', $q['search_orderby_title'] ) . ' THEN 2 ';
				// any word in title, not needed when $num_terms == 1
				if ( $num_terms > 1 )
					$search_orderby .= 'WHEN ' . implode( ' OR ', $q['search_orderby_title'] ) . ' THEN 3 ';
			}

			// Sentence match in 'post_content' and 'post_excerpt'.
			if ( $like ) {
				$search_orderby .= $wpdb->prepare( "WHEN $this->table.post_excerpt LIKE %s THEN 4 ", $like );
				$search_orderby .= $wpdb->prepare( "WHEN $this->table.post_content LIKE %s THEN 5 ", $like );
			}

			if ( $search_orderby ) {
				$search_orderby = '(CASE ' . $search_orderby . 'ELSE 6 END)';
			}
		} else {
			// single word or sentence search
			$search_orderby = reset( $q['search_orderby_title'] ) . ' DESC';
		}

		return $search_orderby;
	}

	/**
	 * If the passed orderby value is allowed, convert the alias to a
	 * properly-prefixed orderby value.
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $orderby Alias for the field to order by.
	 * @return string|false Table-prefixed value to used in the ORDER clause. False otherwise.
	 */
	protected function parse_orderby( $orderby ) {
		global $wpdb;

		// Used to filter values.
		$allowed_keys = array(
			'post_name', 'post_author', 'post_date', 'post_title', 'post_modified',
			'post_parent', 'post_type', 'name', 'author', 'date', 'title', 'modified',
			'parent', 'type', 'ID', 'menu_order', 'comment_count', 'rand',
		);

		$primary_meta_key = '';
		$primary_meta_query = false;
		// $meta_clauses = $this->meta_query->get_clauses();
		// if ( ! empty( $meta_clauses ) ) {
		// 	$primary_meta_query = reset( $meta_clauses );

		// 	if ( ! empty( $primary_meta_query['key'] ) ) {
		// 		$primary_meta_key = $primary_meta_query['key'];
		// 		$allowed_keys[] = $primary_meta_key;
		// 	}

		// 	$allowed_keys[] = 'meta_value';
		// 	$allowed_keys[] = 'meta_value_num';
		// 	$allowed_keys   = array_merge( $allowed_keys, array_keys( $meta_clauses ) );
		// }

		// If RAND() contains a seed value, sanitize and add to allowed keys.
		$rand_with_seed = false;
		if ( preg_match( '/RAND\(([0-9]+)\)/i', $orderby, $matches ) ) {
			$orderby = sprintf( 'RAND(%s)', intval( $matches[1] ) );
			$allowed_keys[] = $orderby;
			$rand_with_seed = true;
		}

		if ( ! in_array( $orderby, $allowed_keys, true ) ) {
			return false;
		}

		switch ( $orderby ) {
			case 'post_name':
			case 'post_author':
			case 'post_date':
			case 'post_title':
			case 'post_modified':
			case 'post_parent':
			case 'post_type':
			case 'ID':
			case 'menu_order':
			case 'comment_count':
				$orderby_clause = "$this->table.{$orderby}";
				break;
			case 'rand':
				$orderby_clause = 'RAND()';
				break;
			case $primary_meta_key:
			case 'meta_value':
				if ( ! empty( $primary_meta_query['type'] ) ) {
					$orderby_clause = "CAST({$primary_meta_query['alias']}.meta_value AS {$primary_meta_query['cast']})";
				} else {
					$orderby_clause = "{$primary_meta_query['alias']}.meta_value";
				}
				break;
			case 'meta_value_num':
				$orderby_clause = "{$primary_meta_query['alias']}.meta_value+0";
				break;
			default:
				if ( $rand_with_seed ) {
					$orderby_clause = $orderby;
				} else {
					// Default: order by post field.
					$orderby_clause = "$this->table.post_" . sanitize_key( $orderby );
				}

				break;
		}

		return $orderby_clause;
	}

	/**
	 * Parse an 'order' query variable and cast it to ASC or DESC as necessary.
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @param string $order The 'order' query variable.
	 * @return string The sanitized 'order' query variable.
	 */
	protected function parse_order( $order ) {
		if ( ! is_string( $order ) || empty( $order ) ) {
			return 'DESC';
		}

		if ( 'ASC' === strtoupper( $order ) ) {
			return 'ASC';
		} else {
			return 'DESC';
		}
	}

	/**
	 * Retrieve query variable.
	 *
	 * @since 1.5.0
	 * @since 3.9.0 The `$default` argument was introduced.
	 *
	 * @access public
	 *
	 * @param string $query_var Query variable key.
	 * @param mixed  $default   Optional. Value to return if the query variable is not set. Default empty.
	 * @return mixed Contents of the query variable.
	 */
	public function get( $query_var, $default = '' ) {
		if ( isset( $this->query_vars[ $query_var ] ) ) {
			return $this->query_vars[ $query_var ];
		}

		return $default;
	}

	/**
	 * Set query variable.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $query_var Query variable key.
	 * @param mixed  $value     Query variable value.
	 */
	public function set($query_var, $value) {
		$this->query_vars[$query_var] = $value;
	}

	/**
	 * Retrieve the posts based on query variables.
	 *
	 * There are a few filters and actions that can be used to modify the post
	 * database query.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return array List of posts.
	 */
	public function get_posts() {
		global $wpdb;

		$this->parse_query();

		/**
		 * Fires after the query variable object is created, but before the actual query is run.
		 *
		 * Note: If using conditional tags, use the method versions within the passed instance
		 * (e.g. $this->is_main_query() instead of is_main_query()). This is because the functions
		 * like is_main_query() test against the global $wp_query instance, not the passed one.
		 *
		 * @since 2.0.0
		 *
		 * @param WP_Query &$this The WP_Query instance (passed by reference).
		 */
		do_action_ref_array( 'pre_get_messages', array( &$this ) );

		// Shorthand.
		$q = &$this->query_vars;

		// Fill again in case pre_get_posts unset some vars.
		$q = $this->fill_query_vars($q);
		
		// Set a flag if a pre_get_posts hook changed the query vars.
		$hash = md5( serialize( $this->query_vars ) );
		if ( $hash != $this->query_vars_hash ) {
			$this->query_vars_changed = true;
			$this->query_vars_hash = $hash;
		}
		unset($hash);

		// First let's clear some variables
		$distinct = '';
		$whichauthor = '';
		$whichmimetype = '';
		$where = '';
		$limits = '';
		$join = '';
		$search = '';
		$groupby = '';
		$post_status_join = false;
		$page = 1;

		if ( !isset($q['suppress_filters']) )
			$q['suppress_filters'] = false;

		if ( !isset($q['cache_results']) ) {
			if ( wp_using_ext_object_cache() )
				$q['cache_results'] = false;
			else
				$q['cache_results'] = true;
		}

		if ( !isset($q['update_post_term_cache']) )
			$q['update_post_term_cache'] = true;

		if ( !isset($q['update_post_meta_cache']) )
			$q['update_post_meta_cache'] = true;

		if ( !isset($q['post_type']) ) {
			if ( $this->is_search )
				$q['post_type'] = 'any';
			else
				$q['post_type'] = '';
		}
		$post_type = $q['post_type'];
		if ( empty( $q['posts_per_page'] ) ) {
			$q['posts_per_page'] = get_option( 'posts_per_page' );
		}
		if ( isset($q['showposts']) && $q['showposts'] ) {
			$q['showposts'] = (int) $q['showposts'];
			$q['posts_per_page'] = $q['showposts'];
		}
		if ( (isset($q['posts_per_archive_page']) && $q['posts_per_archive_page'] != 0) && ($this->is_archive || $this->is_search) )
			$q['posts_per_page'] = $q['posts_per_archive_page'];
		if ( !isset($q['nopaging']) ) {
			if ( $q['posts_per_page'] == -1 ) {
				$q['nopaging'] = true;
			} else {
				$q['nopaging'] = false;
			}
		}

		if ( $this->is_feed ) {
			// This overrides posts_per_page.
			if ( ! empty( $q['posts_per_rss'] ) ) {
				$q['posts_per_page'] = $q['posts_per_rss'];
			} else {
				$q['posts_per_page'] = get_option( 'posts_per_rss' );
			}
			$q['nopaging'] = false;
		}
		$q['posts_per_page'] = (int) $q['posts_per_page'];
		if ( $q['posts_per_page'] < -1 )
			$q['posts_per_page'] = abs($q['posts_per_page']);
		elseif ( $q['posts_per_page'] == 0 )
			$q['posts_per_page'] = 1;

		if ( !isset($q['comments_per_page']) || $q['comments_per_page'] == 0 )
			$q['comments_per_page'] = get_option('comments_per_page');

		if ( $this->is_home && (empty($this->query) || $q['preview'] == 'true') && ( 'page' == get_option('show_on_front') ) && get_option('page_on_front') ) {
			$this->is_page = true;
			$this->is_home = false;
			$q['page_id'] = get_option('page_on_front');
		}

		if ( isset($q['page']) ) {
			$q['page'] = trim($q['page'], '/');
			$q['page'] = absint($q['page']);
		}

		// If true, forcibly turns off SQL_CALC_FOUND_ROWS even when limits are present.
		if ( isset($q['no_found_rows']) )
			$q['no_found_rows'] = (bool) $q['no_found_rows'];
		else
			$q['no_found_rows'] = false;

		switch ( $q['fields'] ) {
			case 'ids':
				$fields = "$this->table.ID";
				break;
			case 'id=>parent':
				$fields = "$this->table.ID, $this->table.post_parent";
				break;
			default:
				$fields = "$this->table.*";
		}

		// The "m" parameter is meant for months but accepts datetimes of varying specificity
		if ( $q['m'] ) {
			$where .= " AND YEAR($this->table.post_date)=" . substr($q['m'], 0, 4);
			if ( strlen($q['m']) > 5 )
				$where .= " AND MONTH($this->table.post_date)=" . substr($q['m'], 4, 2);
			if ( strlen($q['m']) > 7 )
				$where .= " AND DAYOFMONTH($this->table.post_date)=" . substr($q['m'], 6, 2);
			if ( strlen($q['m']) > 9 )
				$where .= " AND HOUR($this->table.post_date)=" . substr($q['m'], 8, 2);
			if ( strlen($q['m']) > 11 )
				$where .= " AND MINUTE($this->table.post_date)=" . substr($q['m'], 10, 2);
			if ( strlen($q['m']) > 13 )
				$where .= " AND SECOND($this->table.post_date)=" . substr($q['m'], 12, 2);
		}

		// Handle the other individual date parameters
		$date_parameters = array();

		if ( '' !== $q['hour'] )
			$date_parameters['hour'] = $q['hour'];

		if ( '' !== $q['minute'] )
			$date_parameters['minute'] = $q['minute'];

		if ( '' !== $q['second'] )
			$date_parameters['second'] = $q['second'];

		if ( $q['year'] )
			$date_parameters['year'] = $q['year'];

		if ( $q['monthnum'] )
			$date_parameters['monthnum'] = $q['monthnum'];

		if ( $q['w'] )
			$date_parameters['week'] = $q['w'];

		if ( $q['day'] )
			$date_parameters['day'] = $q['day'];

		if ( $date_parameters ) {
			$date_query = new WP_Date_Query( array( $date_parameters ) );
			$where .= $date_query->get_sql();
		}
		unset( $date_parameters, $date_query );

		// Handle complex date queries
		if ( ! empty( $q['date_query'] ) ) {
			$this->date_query = new WP_Date_Query( $q['date_query'] );
			$where .= $this->date_query->get_sql();
			$where = str_replace('posts', 'marketengine_message_item', $where);
		}

		// If we've got a post_type AND it's not "any" post_type.
		if ( !empty($q['post_type']) && 'any' != $q['post_type'] ) {
			foreach ( (array)$q['post_type'] as $_post_type ) {
				$ptype_obj = get_post_type_object($_post_type);
				if ( !$ptype_obj || !$ptype_obj->query_var || empty($q[ $ptype_obj->query_var ]) )
					continue;

				if ( ! $ptype_obj->hierarchical ) {
					// Non-hierarchical post types can directly use 'name'.
					$q['name'] = $q[ $ptype_obj->query_var ];
				} else {
					// Hierarchical post types will operate through 'pagename'.
					$q['pagename'] = $q[ $ptype_obj->query_var ];
					$q['name'] = '';
				}

				// Only one request for a slug is possible, this is why name & pagename are overwritten above.
				break;
			} //end foreach
			unset($ptype_obj);
		}

		if ( '' !== $q['title'] ) {
			$where .= $wpdb->prepare( " AND $this->table.post_title = %s", stripslashes( $q['title'] ) );
		}

		// If a post number is specified, load that post
		$q['p'] = isset($q['p']) ? $q['p'] : array();
		$q['post__in'] = isset($q['post__in']) ? $q['post__in'] : array();
		$q['post__not_in'] = isset($q['post__not_in']) ? $q['post__not_in'] : array();

		if ( $q['p'] ) {
			$where .= " AND {$this->table}.ID = " . $q['p'];
		} elseif ( $q['post__in'] ) {
			$post__in = implode(',', array_map( 'absint', $q['post__in'] ));
			$where .= " AND {$this->table}.ID IN ($post__in)";
		} elseif ( $q['post__not_in'] ) {
			$post__not_in = implode(',',  array_map( 'absint', $q['post__not_in'] ));
			$where .= " AND {$this->table}.ID NOT IN ($post__not_in)";
		}

		$q['post_parent'] = isset($q['post_parent']) ? $q['post_parent'] : '';
		$q['post_parent__in'] = isset($q['post_parent__in']) ? $q['post_parent__in'] : array();
		$q['post_parent__not_in'] = isset($q['post_parent__not_in']) ? $q['post_parent__not_in'] : array();

		if ( is_numeric( $q['post_parent']) ) {
			$where .= $wpdb->prepare( " AND $this->table.post_parent = %d ", $q['post_parent'] );
		} elseif ( $q['post_parent__in'] ) {
			$post_parent__in = implode( ',', array_map( 'absint', $q['post_parent__in'] ) );
			$where .= " AND {$this->table}.post_parent IN ($post_parent__in)";
		} elseif ( $q['post_parent__not_in'] ) {
			$post_parent__not_in = implode( ',',  array_map( 'absint', $q['post_parent__not_in'] ) );
			$where .= " AND {$this->table}.post_parent NOT IN ($post_parent__not_in)";
		}

		// If a search pattern is specified, load the posts that match.
		if ( strlen( $q['s'] ) ) {
			$search = $this->parse_search( $q );
		}

		if ( ! $q['suppress_filters'] ) {
			/**
			 * Filters the search SQL that is used in the WHERE clause of WP_Query.
			 *
			 * @since 3.0.0
			 *
			 * @param string   $search Search SQL for WHERE clause.
			 * @param WP_Query $this   The current WP_Query object.
			 */
			$search = apply_filters_ref_array( 'posts_search', array( $search, &$this ) );
		}

		// Author/user stuff
		if ( ! empty( $q['sender'] ) && $q['sender'] != '0' ) {
			$q['sender'] = addslashes_gpc( '' . urldecode( $q['sender'] ) );
			$authors = array_unique( array_map( 'intval', preg_split( '/[,\s]+/', $q['sender'] ) ) );
			foreach ( $authors as $author ) {
				$key = $author > 0 ? 'author__in' : 'author__not_in';
				$q[$key][] = abs( $author );
			}
			$q['sender'] = implode( ',', $authors );
		}

		if ( ! empty( $q['author__not_in'] ) ) {
			$author__not_in = implode( ',', array_map( 'absint', array_unique( (array) $q['author__not_in'] ) ) );
			$where .= " AND {$this->table}.post_author NOT IN ($author__not_in) ";
		} elseif ( ! empty( $q['author__in'] ) ) {
			$author__in = implode( ',', array_map( 'absint', array_unique( (array) $q['author__in'] ) ) );
			if(!empty($q['receiver']) && $q['receiver'] != '0') {
				$where .= " AND ({$this->table}.sender IN ($author__in) ";
			} else {
				$where .= " AND {$this->table}.sender IN ($author__in) ";
			}

		}

		// Author stuff for nice URLs
		if ( '' != $q['sender_name'] ) {
			if ( strpos($q['sender_name'], '/') !== false ) {
				$q['sender_name'] = explode('/', $q['sender_name']);
				if ( $q['sender_name'][ count($q['sender_name'])-1 ] ) {
					$q['sender_name'] = $q['sender_name'][count($q['sender_name'])-1]; // no trailing slash
				} else {
					$q['sender_name'] = $q['sender_name'][count($q['sender_name'])-2]; // there was a trailing slash
				}
			}
			$q['sender_name'] = sanitize_title_for_query( $q['sender_name'] );
			$q['sender'] = get_user_by('slug', $q['sender_name']);
			if ( $q['sender'] )
				$q['sender'] = $q['sender']->ID;
			$whichauthor .= " AND ($this->table.sender = " . absint($q['sender']) . ')';
		}

		// Receiver
		if ( ! empty( $q['receiver'] ) && $q['receiver'] != '0' ) {
			$q['receiver'] = addslashes_gpc( '' . urldecode( $q['receiver'] ) );
			$authors = array_unique( array_map( 'intval', preg_split( '/[,\s]+/', $q['receiver'] ) ) );
			foreach ( $authors as $author ) {
				$key = $author > 0 ? 'author__in' : 'author__not_in';
				$q[$key][] = abs( $author );
			}
			$q['receiver'] = implode( ',', $authors );

			if ( ! empty( $q['author__not_in'] ) ) {
				$author__not_in = implode( ',', array_map( 'absint', array_unique( (array) $q['author__not_in'] ) ) );
				$where .= " AND {$this->table}.post_author NOT IN ($author__not_in) ";
			} elseif ( ! empty( $q['author__in'] ) ) {
				$author__in = implode( ',', array_map( 'absint', array_unique( (array) $q['author__in'] ) ) );
				if(!empty($q['sender']) && $q['sender'] != '0') {
					$where .= " OR {$this->table}.receiver IN ($author__in) ) ";
				} else {
					$where .= " AND {$this->table}.receiver IN ($author__in) ";
				}
			}
		}

		// Author stuff for nice URLs
		if ( '' != $q['receiver_name'] ) {
			if ( strpos($q['receiver_name'], '/') !== false ) {
				$q['receiver_name'] = explode('/', $q['receiver_name']);
				if ( $q['receiver_name'][ count($q['receiver_name'])-1 ] ) {
					$q['receiver_name'] = $q['receiver_name'][count($q['receiver_name'])-1]; // no trailing slash
				} else {
					$q['receiver_name'] = $q['receiver_name'][count($q['receiver_name'])-2]; // there was a trailing slash
				}
			}
			$q['receiver_name'] = sanitize_title_for_query( $q['receiver_name'] );
			$q['receiver'] = get_user_by('slug', $q['receiver_name']);
			if ( $q['receiver'] )
				$q['receiver'] = $q['receiver']->ID;
			$whichauthor .= " AND ($this->table.receiver = " . absint($q['receiver']) . ')';
		}

		$where .= $search . $whichauthor;

		if ( ! empty( $this->meta_query->queries ) ) {
			$clauses = $this->meta_query->get_sql( 'post', $this->table, 'ID', $this );
			$join   .= $clauses['join'];
			$where  .= $clauses['where'];
		}

		$rand = ( isset( $q['orderby'] ) && 'rand' === $q['orderby'] );
		if ( ! isset( $q['order'] ) ) {
			$q['order'] = $rand ? '' : 'DESC';
		} else {
			$q['order'] = $rand ? '' : $this->parse_order( $q['order'] );
		}

		// Order by.
		if ( empty( $q['orderby'] ) ) {
			/*
			 * Boolean false or empty array blanks out ORDER BY,
			 * while leaving the value unset or otherwise empty sets the default.
			 */
			if ( isset( $q['orderby'] ) && ( is_array( $q['orderby'] ) || false === $q['orderby'] ) ) {
				$orderby = '';
			} else {
				$orderby = "$this->table.post_date " . $q['order'];
			}
		} elseif ( 'none' == $q['orderby'] ) {
			$orderby = '';
		} elseif ( $q['orderby'] == 'post__in' && ! empty( $post__in ) ) {
			$orderby = "FIELD( {$this->table}.ID, $post__in )";
		} elseif ( $q['orderby'] == 'post_parent__in' && ! empty( $post_parent__in ) ) {
			$orderby = "FIELD( {$this->table}.post_parent, $post_parent__in )";
		} elseif ( $q['orderby'] == 'post_name__in' && ! empty( $post_name__in ) ) {
			$orderby = "FIELD( {$this->table}.post_name, $post_name__in )";
		} else {
			$orderby_array = array();
			if ( is_array( $q['orderby'] ) ) {
				foreach ( $q['orderby'] as $_orderby => $order ) {
					$orderby = addslashes_gpc( urldecode( $_orderby ) );
					$parsed  = $this->parse_orderby( $orderby );

					if ( ! $parsed ) {
						continue;
					}

					$orderby_array[] = $parsed . ' ' . $this->parse_order( $order );
				}
				$orderby = implode( ', ', $orderby_array );

			} else {
				$q['orderby'] = urldecode( $q['orderby'] );
				$q['orderby'] = addslashes_gpc( $q['orderby'] );

				foreach ( explode( ' ', $q['orderby'] ) as $i => $orderby ) {
					$parsed = $this->parse_orderby( $orderby );
					// Only allow certain values for safety.
					if ( ! $parsed ) {
						continue;
					}

					$orderby_array[] = $parsed;
				}
				$orderby = implode( ' ' . $q['order'] . ', ', $orderby_array );

				if ( empty( $orderby ) ) {
					$orderby = "$this->table.post_date " . $q['order'];
				} elseif ( ! empty( $q['order'] ) ) {
					$orderby .= " {$q['order']}";
				}
			}
		}

		// Order search results by relevance only when another "orderby" is not specified in the query.
		if ( ! empty( $q['s'] ) ) {
			$search_orderby = '';
			if ( ! empty( $q['search_orderby_title'] ) && ( empty( $q['orderby'] ) && ! $this->is_feed ) || ( isset( $q['orderby'] ) && 'relevance' === $q['orderby'] ) )
				$search_orderby = $this->parse_search_order( $q );

			if ( ! $q['suppress_filters'] ) {
				/**
				 * Filters the ORDER BY used when ordering search results.
				 *
				 * @since 3.7.0
				 *
				 * @param string   $search_orderby The ORDER BY clause.
				 * @param WP_Query $this           The current WP_Query instance.
				 */
				$search_orderby = apply_filters( 'messages_search_orderby', $search_orderby, $this );
			}

			if ( $search_orderby )
				$orderby = $orderby ? $search_orderby . ', ' . $orderby : $search_orderby;
		}

		if ( is_array( $post_type ) && count( $post_type ) > 1 ) {
			$post_type_cap = 'multiple_post_type';
		} else {
			if ( is_array( $post_type ) )
				$post_type = reset( $post_type );
			$post_type_object = get_post_type_object( $post_type );
			if ( empty( $post_type_object ) )
				$post_type_cap = $post_type;
		}

		if ( 'any' == $post_type ) {
			$in_search_post_types = get_post_types( array('exclude_from_search' => false) );
			if ( empty( $in_search_post_types ) )
				$where .= ' AND 1=0 ';
			else
				$where .= " AND $this->table.post_type IN ('" . join("', '", $in_search_post_types ) . "')";
		} elseif ( !empty( $post_type ) && is_array( $post_type ) ) {
			$where .= " AND $this->table.post_type IN ('" . join("', '", $post_type) . "')";
		} elseif ( ! empty( $post_type ) ) {
			$where .= " AND $this->table.post_type = '$post_type'";
			$post_type_object = get_post_type_object ( $post_type );
		} elseif ( $this->is_attachment ) {
			$where .= " AND $this->table.post_type = 'attachment'";
			$post_type_object = get_post_type_object ( 'attachment' );
		} elseif ( $this->is_page ) {
			$where .= " AND $this->table.post_type = 'page'";
			$post_type_object = get_post_type_object ( 'page' );
		} else {
			$where .= " AND $this->table.post_type = 'post'";
			$post_type_object = get_post_type_object ( 'post' );
		}

		if ( ! empty( $post_type_object ) ) {
			$edit_others_cap = $post_type_object->cap->edit_others_posts;
			$read_private_cap = $post_type_object->cap->read_private_posts;
		} else {
			$edit_others_cap = 'edit_others_' . $post_type_cap . 's';
			$read_private_cap = 'read_private_' . $post_type_cap . 's';
		}

		$user_id = get_current_user_id();

		$q_status = array();
		if ( ! empty( $q['post_status'] ) ) {
			$statuswheres = array();
			$q_status = $q['post_status'];
			if ( ! is_array( $q_status ) )
				$q_status = explode(',', $q_status);
			$r_status = array();
			$p_status = array();
			$e_status = array();

			// foreach ( get_post_stati() as $status ) {
			// 	if ( in_array( $status, $q_status ) ) {
			foreach($q_status as $status) {
				if ( 'private' == $status )
					$p_status[] = "$this->table.post_status = '$status'";
				else
					$r_status[] = "$this->table.post_status = '$status'";
			}
			// 	}
			// }

			if ( empty($q['perm'] ) || 'readable' != $q['perm'] ) {
				$r_status = array_merge($r_status, $p_status);
				unset($p_status);
			}

			if ( !empty($e_status) ) {
				$statuswheres[] = "(" . join( ' AND ', $e_status ) . ")";
			}
			if ( !empty($r_status) ) {
				$statuswheres[] = "(" . join( ' OR ', $r_status ) . ")";
			}
			if ( !empty($p_status) ) {
				$statuswheres[] = "(" . join( ' OR ', $p_status ) . ")";
			}
			$where_status = implode( ' OR ', $statuswheres );
			if ( ! empty( $where_status ) ) {
				$where .= " AND ($where_status)";
			}
		}

		/*
		 * Apply filters on where and join prior to paging so that any
		 * manipulations to them are reflected in the paging by day queries.
		 */
		if ( !$q['suppress_filters'] ) {
			/**
			 * Filters the WHERE clause of the query.
			 *
			 * @since 1.5.0
			 *
			 * @param string   $where The WHERE clause of the query.
			 * @param WP_Query &$this The WP_Query instance (passed by reference).
			 */
			$where = apply_filters_ref_array( 'messages_where', array( $where, &$this ) );

			/**
			 * Filters the JOIN clause of the query.
			 *
			 * @since 1.5.0
			 *
			 * @param string   $where The JOIN clause of the query.
			 * @param WP_Query &$this The WP_Query instance (passed by reference).
			 */
			$join = apply_filters_ref_array( 'messages_join', array( $join, &$this ) );
		}

		// Paging
		if ( empty($q['nopaging']) && !$this->is_singular ) {
			$page = absint($q['paged']);
			if ( !$page )
				$page = 1;

			// If 'offset' is provided, it takes precedence over 'paged'.
			if ( isset( $q['offset'] ) && is_numeric( $q['offset'] ) ) {
				$q['offset'] = absint( $q['offset'] );
				$pgstrt = $q['offset'] . ', ';
			} else {
				$pgstrt = absint( ( $page - 1 ) * $q['posts_per_page'] ) . ', ';
			}
			$limits = 'LIMIT ' . $pgstrt . $q['posts_per_page'];
		}

		$pieces = array( 'where', 'groupby', 'join', 'orderby', 'distinct', 'fields', 'limits' );

		/*
		 * Apply post-paging filters on where and join. Only plugins that
		 * manipulate paging queries should use these hooks.
		 */
		if ( !$q['suppress_filters'] ) {
			/**
			 * Filters the WHERE clause of the query.
			 *
			 * Specifically for manipulating paging queries.
			 *
			 * @since 1.5.0
			 *
			 * @param string   $where The WHERE clause of the query.
			 * @param WP_Query &$this The WP_Query instance (passed by reference).
			 */
			$where = apply_filters_ref_array( 'messages_where_paged', array( $where, &$this ) );

			/**
			 * Filters the GROUP BY clause of the query.
			 *
			 * @since 2.0.0
			 *
			 * @param string   $groupby The GROUP BY clause of the query.
			 * @param WP_Query &$this   The WP_Query instance (passed by reference).
			 */
			$groupby = apply_filters_ref_array( 'messages_groupby', array( $groupby, &$this ) );

			/**
			 * Filters the JOIN clause of the query.
			 *
			 * Specifically for manipulating paging queries.
			 *
			 * @since 1.5.0
			 *
			 * @param string   $join  The JOIN clause of the query.
			 * @param WP_Query &$this The WP_Query instance (passed by reference).
			 */
			$join = apply_filters_ref_array( 'messages_join_paged', array( $join, &$this ) );

			/**
			 * Filters the ORDER BY clause of the query.
			 *
			 * @since 1.5.1
			 *
			 * @param string   $orderby The ORDER BY clause of the query.
			 * @param WP_Query &$this   The WP_Query instance (passed by reference).
			 */
			$orderby = apply_filters_ref_array( 'messages_orderby', array( $orderby, &$this ) );

			/**
			 * Filters the DISTINCT clause of the query.
			 *
			 * @since 2.1.0
			 *
			 * @param string   $distinct The DISTINCT clause of the query.
			 * @param WP_Query &$this    The WP_Query instance (passed by reference).
			 */
			$distinct = apply_filters_ref_array( 'messages_distinct', array( $distinct, &$this ) );

			/**
			 * Filters the LIMIT clause of the query.
			 *
			 * @since 2.1.0
			 *
			 * @param string   $limits The LIMIT clause of the query.
			 * @param WP_Query &$this  The WP_Query instance (passed by reference).
			 */
			$limits = apply_filters_ref_array( 'message_limits', array( $limits, &$this ) );

			/**
			 * Filters the SELECT clause of the query.
			 *
			 * @since 2.1.0
			 *
			 * @param string   $fields The SELECT clause of the query.
			 * @param WP_Query &$this  The WP_Query instance (passed by reference).
			 */
			$fields = apply_filters_ref_array( 'messages_fields', array( $fields, &$this ) );

			/**
			 * Filters all query clauses at once, for convenience.
			 *
			 * Covers the WHERE, GROUP BY, JOIN, ORDER BY, DISTINCT,
			 * fields (SELECT), and LIMITS clauses.
			 *
			 * @since 3.1.0
			 *
			 * @param array    $clauses The list of clauses for the query.
			 * @param WP_Query &$this   The WP_Query instance (passed by reference).
			 */
			$clauses = (array) apply_filters_ref_array( 'messages_clauses', array( compact( $pieces ), &$this ) );

			$where = isset( $clauses[ 'where' ] ) ? $clauses[ 'where' ] : '';
			$groupby = isset( $clauses[ 'groupby' ] ) ? $clauses[ 'groupby' ] : '';
			$join = isset( $clauses[ 'join' ] ) ? $clauses[ 'join' ] : '';
			$orderby = isset( $clauses[ 'orderby' ] ) ? $clauses[ 'orderby' ] : '';
			$distinct = isset( $clauses[ 'distinct' ] ) ? $clauses[ 'distinct' ] : '';
			$fields = isset( $clauses[ 'fields' ] ) ? $clauses[ 'fields' ] : '';
			$limits = isset( $clauses[ 'limits' ] ) ? $clauses[ 'limits' ] : '';
		}

		/**
		 * Fires to announce the query's current selection parameters.
		 *
		 * For use by caching plugins.
		 *
		 * @since 2.3.0
		 *
		 * @param string $selection The assembled selection query.
		 */
		do_action( 'messages_selection', $where . $groupby . $orderby . $limits . $join );

		/*
		 * Filters again for the benefit of caching plugins.
		 * Regular plugins should use the hooks above.
		 */
		if ( !$q['suppress_filters'] ) {
			/**
			 * Filters the WHERE clause of the query.
			 *
			 * For use by caching plugins.
			 *
			 * @since 2.5.0
			 *
			 * @param string   $where The WHERE clause of the query.
			 * @param WP_Query &$this The WP_Query instance (passed by reference).
			 */
			$where = apply_filters_ref_array( 'messages_where_request', array( $where, &$this ) );

			/**
			 * Filters the GROUP BY clause of the query.
			 *
			 * For use by caching plugins.
			 *
			 * @since 2.5.0
			 *
			 * @param string   $groupby The GROUP BY clause of the query.
			 * @param WP_Query &$this   The WP_Query instance (passed by reference).
			 */
			$groupby = apply_filters_ref_array( 'messages_groupby_request', array( $groupby, &$this ) );

			/**
			 * Filters the JOIN clause of the query.
			 *
			 * For use by caching plugins.
			 *
			 * @since 2.5.0
			 *
			 * @param string   $join  The JOIN clause of the query.
			 * @param WP_Query &$this The WP_Query instance (passed by reference).
			 */
			$join = apply_filters_ref_array( 'messages_join_request', array( $join, &$this ) );

			/**
			 * Filters the ORDER BY clause of the query.
			 *
			 * For use by caching plugins.
			 *
			 * @since 2.5.0
			 *
			 * @param string   $orderby The ORDER BY clause of the query.
			 * @param WP_Query &$this   The WP_Query instance (passed by reference).
			 */
			$orderby = apply_filters_ref_array( 'messages_orderby_request', array( $orderby, &$this ) );

			/**
			 * Filters the DISTINCT clause of the query.
			 *
			 * For use by caching plugins.
			 *
			 * @since 2.5.0
			 *
			 * @param string   $distinct The DISTINCT clause of the query.
			 * @param WP_Query &$this    The WP_Query instance (passed by reference).
			 */
			$distinct = apply_filters_ref_array( 'messages_distinct_request', array( $distinct, &$this ) );

			/**
			 * Filters the SELECT clause of the query.
			 *
			 * For use by caching plugins.
			 *
			 * @since 2.5.0
			 *
			 * @param string   $fields The SELECT clause of the query.
			 * @param WP_Query &$this  The WP_Query instance (passed by reference).
			 */
			$fields = apply_filters_ref_array( 'messages_fields_request', array( $fields, &$this ) );

			/**
			 * Filters the LIMIT clause of the query.
			 *
			 * For use by caching plugins.
			 *
			 * @since 2.5.0
			 *
			 * @param string   $limits The LIMIT clause of the query.
			 * @param WP_Query &$this  The WP_Query instance (passed by reference).
			 */
			$limits = apply_filters_ref_array( 'message_limits_request', array( $limits, &$this ) );

			/**
			 * Filters all query clauses at once, for convenience.
			 *
			 * For use by caching plugins.
			 *
			 * Covers the WHERE, GROUP BY, JOIN, ORDER BY, DISTINCT,
			 * fields (SELECT), and LIMITS clauses.
			 *
			 * @since 3.1.0
			 *
			 * @param array    $pieces The pieces of the query.
			 * @param WP_Query &$this  The WP_Query instance (passed by reference).
			 */
			$clauses = (array) apply_filters_ref_array( 'messages_clauses_request', array( compact( $pieces ), &$this ) );

			$where = isset( $clauses[ 'where' ] ) ? $clauses[ 'where' ] : '';
			$groupby = isset( $clauses[ 'groupby' ] ) ? $clauses[ 'groupby' ] : '';
			$join = isset( $clauses[ 'join' ] ) ? $clauses[ 'join' ] : '';
			$orderby = isset( $clauses[ 'orderby' ] ) ? $clauses[ 'orderby' ] : '';
			$distinct = isset( $clauses[ 'distinct' ] ) ? $clauses[ 'distinct' ] : '';
			$fields = isset( $clauses[ 'fields' ] ) ? $clauses[ 'fields' ] : '';
			$limits = isset( $clauses[ 'limits' ] ) ? $clauses[ 'limits' ] : '';
		}

		if ( ! empty($groupby) )
			$groupby = 'GROUP BY ' . $groupby;
		if ( !empty( $orderby ) )
			$orderby = 'ORDER BY ' . $orderby;

		$found_rows = '';
		if ( !$q['no_found_rows'] && !empty($limits) )
			$found_rows = 'SQL_CALC_FOUND_ROWS';

		$this->request = $old_request = "SELECT $found_rows $distinct $fields FROM $this->table $join WHERE 1=1 $where $groupby $orderby $limits";

		if ( !$q['suppress_filters'] ) {
			/**
			 * Filters the completed SQL query before sending.
			 *
			 * @since 2.0.0
			 *
			 * @param string   $request The complete SQL query.
			 * @param WP_Query &$this   The WP_Query instance (passed by reference).
			 */
			$this->request = apply_filters_ref_array( 'messages_request', array( $this->request, &$this ) );
		}

		/**
		 * Filters the posts array before the query takes place.
		 *
		 * Return a non-null value to bypass WordPress's default post queries.
		 *
		 * Filtering functions that require pagination information are encouraged to set
		 * the `found_posts` and `max_num_pages` properties of the WP_Query object,
		 * passed to the filter by reference. If WP_Query does not perform a database
		 * query, it will not have enough information to generate these values itself.
		 *
		 * @since 4.6.0
		 *
		 * @param array|null $posts Return an array of post data to short-circuit WP's query,
		 *                          or null to allow WP to run its normal queries.
		 * @param WP_Query   $this  The WP_Query instance, passed by reference.
		 */
		$this->posts = apply_filters_ref_array( 'posts_pre_query', array( null, &$this ) );

		if ( 'ids' == $q['fields'] ) {
			if ( null === $this->posts ) {
				$this->posts = $wpdb->get_col( $this->request );
			}

			$this->posts = array_map( 'intval', $this->posts );
			$this->post_count = count( $this->posts );
			$this->set_found_posts( $q, $limits );

			return $this->posts;
		}

		if ( 'id=>parent' == $q['fields'] ) {
			if ( null === $this->posts ) {
				$this->posts = $wpdb->get_results( $this->request );
			}

			$this->post_count = count( $this->posts );
			$this->set_found_posts( $q, $limits );

			$r = array();
			foreach ( $this->posts as $key => $post ) {
				$this->posts[ $key ]->ID = (int) $post->ID;
				$this->posts[ $key ]->post_parent = (int) $post->post_parent;

				$r[ (int) $post->ID ] = (int) $post->post_parent;
			}

			return $r;
		}

		if ( null === $this->posts ) {
			$this->posts = $wpdb->get_results( $this->request );
			$this->set_found_posts( $q, $limits );
		}

		// Convert to WP_Post objects.
		if ( $this->posts ) {
			$this->posts = array_map( 'marketengine_get_message', $this->posts );
		}

		if ( ! $q['suppress_filters'] ) {
			/**
			 * Filters the raw post results array, prior to status checks.
			 *
			 * @since 2.3.0
			 *
			 * @param array    $posts The post results array.
			 * @param WP_Query &$this The WP_Query instance (passed by reference).
			 */
			$this->posts = apply_filters_ref_array( 'messages_results', array( $this->posts, &$this ) );
		}

		if ( ! $q['suppress_filters'] ) {
			/**
			 * Filters the array of retrieved posts after they've been fetched and
			 * internally processed.
			 *
			 * @since 1.5.0
			 *
			 * @param array    $posts The array of retrieved posts.
			 * @param WP_Query &$this The WP_Query instance (passed by reference).
			 */
			$this->posts = apply_filters_ref_array( 'the_messages', array( $this->posts, &$this ) );
		}

		// Ensure that any posts added/modified via one of the filters above are
		// of the type WP_Post and are filtered.
		if ( $this->posts ) {
			$this->post_count = count( $this->posts );

			$this->posts = array_map( 'marketengine_get_message', $this->posts );

			// if ( $q['cache_results'] )
			// 	update_post_caches($this->posts, $post_type, $q['update_post_term_cache'], $q['update_post_meta_cache']);

			$this->post = reset( $this->posts );
		} else {
			$this->post_count = 0;
			$this->posts = array();
		}

		return $this->posts;
	}

	/**
	 * Set up the amount of found posts and the number of pages (if limit clause was used)
	 * for the current query.
	 *
	 * @since 3.5.0
	 * @access private
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param array  $q      Query variables.
	 * @param string $limits LIMIT clauses of the query.
	 */
	private function set_found_posts( $q, $limits ) {
		global $wpdb;

		// Bail if posts is an empty array. Continue if posts is an empty string,
		// null, or false to accommodate caching plugins that fill posts later.
		if ( $q['no_found_rows'] || ( is_array( $this->posts ) && ! $this->posts ) )
			return;

		if ( ! empty( $limits ) ) {
			/**
			 * Filters the query to run for retrieving the found posts.
			 *
			 * @since 2.1.0
			 *
			 * @param string   $found_posts The query to run to find the found posts.
			 * @param WP_Query &$this       The WP_Query instance (passed by reference).
			 */
			$this->found_posts = $wpdb->get_var( apply_filters_ref_array( 'found_messages_query', array( 'SELECT FOUND_ROWS()', &$this ) ) );
		} else {
			$this->found_posts = count( $this->posts );
		}

		/**
		 * Filters the number of found posts for the query.
		 *
		 * @since 2.1.0
		 *
		 * @param int      $found_posts The number of posts found.
		 * @param WP_Query &$this       The WP_Query instance (passed by reference).
		 */
		$this->found_posts = apply_filters_ref_array( 'found_messages', array( $this->found_posts, &$this ) );

		if ( ! empty( $limits ) )
			$this->max_num_pages = ceil( $this->found_posts / $q['posts_per_page'] );
	}

	/**
	 * Set up the next post and iterate current post index.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return WP_Post Next post.
	 */
	public function next_post() {

		$this->current_post++;

		$this->post = $this->posts[$this->current_post];
		return $this->post;
	}

	/**
	 * Sets up the current post.
	 *
	 * Retrieves the next post, sets up the post, sets the 'in the loop'
	 * property to true.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @global WP_Post $post
	 */
	public function the_post() {
		global $message;
		$this->in_the_loop = true;

		if ( $this->current_post == -1 ) // loop has just started
			/**
			 * Fires once the loop is started.
			 *
			 * @since 2.0.0
			 *
			 * @param WP_Query &$this The WP_Query instance (passed by reference).
			 */
			do_action_ref_array( 'loop_start', array( &$this ) );

		$message = $this->next_post();
		$this->setup_postdata( $message );
	}

	/**
	 * Determines whether there are more posts available in the loop.
	 *
	 * Calls the {@see 'loop_end'} action when the loop is complete.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return bool True if posts are available, false if end of loop.
	 */
	public function have_posts() {
		if ( $this->current_post + 1 < $this->post_count ) {
			return true;
		} elseif ( $this->current_post + 1 == $this->post_count && $this->post_count > 0 ) {
			/**
			 * Fires once the loop has ended.
			 *
			 * @since 2.0.0
			 *
			 * @param WP_Query &$this The WP_Query instance (passed by reference).
			 */
			do_action_ref_array( 'loop_end', array( &$this ) );
			// Do some cleaning up after the loop
			$this->rewind_posts();
		}

		$this->in_the_loop = false;
		return false;
	}

	/**
	 * Rewind the posts and reset post index.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function rewind_posts() {
		$this->current_post = -1;
		if ( $this->post_count > 0 ) {
			$this->post = $this->posts[0];
		}
	}

	/**
	 * Sets up the WordPress query by parsing query string.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $query URL query string.
	 * @return array List of posts.
	 */
	public function query( $query ) {
		$this->init();
		$this->query = $this->query_vars = wp_parse_args( $query );
		return $this->get_posts();
	}

	/**
	 * Constructor.
	 *
	 * Sets up the WordPress query, if parameter is not empty.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string|array $query URL query string or array of vars.
	 */
	public function __construct($query = '') {
		if ( ! empty($query) ) {
			$this->query($query);
		}
	}

	/**
	 * Make private properties readable for backward compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to get.
	 * @return mixed Property.
	 */
	public function __get( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return $this->$name;
		}
	}

	/**
	 * Make private properties checkable for backward compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to check if set.
	 * @return bool Whether the property is set.
	 */
	public function __isset( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return isset( $this->$name );
		}
	}

	/**
	 * Set up global post data.
	 *
	 * @since 4.1.0
	 * @since 4.4.0 Added the ability to pass a post ID to `$post`.
	 *
	 * @global int             $id
	 * @global WP_User         $authordata
	 * @global string|int|bool $currentday
	 * @global string|int|bool $currentmonth
	 * @global int             $page
	 * @global array           $pages
	 * @global int             $multipage
	 * @global int             $more
	 * @global int             $numpages
	 *
	 * @param WP_Post|object|int $post WP_Post instance or Post ID/object.
	 * @return true True when finished.
	 */
	public function setup_postdata( $post ) {
		global $id, $authordata, $currentday, $currentmonth, $page, $pages, $multipage, $more, $numpages;

		if ( ! ( $post instanceof ME_Message ) ) {
			$post = marketengine_get_message( $post );
		}

		if ( ! $post ) {
			return;
		}

		$id = (int) $post->ID;

		$authordata = get_userdata($post->post_author);

		$currentday = mysql2date('d.m.y', $post->post_date, false);
		$currentmonth = mysql2date('m', $post->post_date, false);
		$numpages = 1;
		$multipage = 0;
		$page = $this->get( 'page' );
		if ( ! $page )
			$page = 1;

		/*
		 * Force full post content when viewing the permalink for the $post,
		 * or when on an RSS feed. Otherwise respect the 'more' tag.
		 */
		$more = 0;

		$content = $post->post_content;
		if ( false !== strpos( $content, '<!--nextpage-->' ) ) {
			$content = str_replace( "\n<!--nextpage-->\n", '<!--nextpage-->', $content );
			$content = str_replace( "\n<!--nextpage-->", '<!--nextpage-->', $content );
			$content = str_replace( "<!--nextpage-->\n", '<!--nextpage-->', $content );

			// Ignore nextpage at the beginning of the content.
			if ( 0 === strpos( $content, '<!--nextpage-->' ) )
				$content = substr( $content, 15 );

			$pages = explode('<!--nextpage-->', $content);
		} else {
			$pages = array( $post->post_content );
		}

		/**
		 * Filters the "pages" derived from splitting the post content.
		 *
		 * "Pages" are determined by splitting the post content based on the presence
		 * of `<!-- nextpage -->` tags.
		 *
		 * @since 4.4.0
		 *
		 * @param array   $pages Array of "pages" derived from the post content.
		 *                       of `<!-- nextpage -->` tags..
		 * @param WP_Post $post  Current post object.
		 */
		$pages = apply_filters( 'content_pagination', $pages, $post );

		$numpages = count( $pages );

		if ( $numpages > 1 ) {
			if ( $page > 1 ) {
				$more = 1;
			}
			$multipage = 1;
		} else {
	 		$multipage = 0;
	 	}

		/**
		 * Fires once the post data has been setup.
		 *
		 * @since 2.8.0
		 * @since 4.1.0 Introduced `$this` parameter.
		 *
		 * @param WP_Post  &$post The Post object (passed by reference).
		 * @param WP_Query &$this The current Query object (passed by reference).
		 */
		do_action_ref_array( 'marketengine_the_message', array( &$post, &$this ) );

		return true;
	}
	/**
	 * After looping through a nested query, this function
	 * restores the $post global to the current post in this query.
	 *
	 * @since 3.7.0
	 *
	 * @global WP_Post $post
	 */
	public function reset_postdata() {
		if ( ! empty( $this->post ) ) {
			$GLOBALS['message'] = $this->post;
			$this->setup_postdata( $this->post );
		}
	}
}
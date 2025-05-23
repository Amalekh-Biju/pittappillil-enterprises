<?php
/**
 * UAGB Admin Helper.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \ZipAI\Classes\Module as Zip_Ai_Module;

if ( ! class_exists( 'UAGB_Admin_Helper' ) ) {

	/**
	 * Class UAGB_Admin_Helper.
	 */
	final class UAGB_Admin_Helper {

		/**
		 * Member Variable
		 *
		 * @since 0.0.1
		 * @var instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 *
		 * @since 0.0.1
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Get all data from the admin settings page.
		 *
		 * @return mixed
		 * @since 2.0.8
		 */
		public static function get_admin_settings_shareable_data() {

			// Prepare to get the Zip AI Co-pilot modules.
			$zip_ai_modules = array();

			// If the Zip AI Helper is available, get the required modules and their states.
			if ( class_exists( '\ZipAI\Classes\Module' ) ) {
				$zip_ai_modules = Zip_Ai_Module::get_all_modules();
			}

			$content_width = self::get_global_content_width();

			$options = array(
				'uagb_beta'                         => self::get_admin_settings_option( 'uagb_beta', 'no' ),
				'uag_enable_legacy_blocks'          => self::get_admin_settings_option( 'uag_enable_legacy_blocks' ),
				'_uagb_allow_file_generation'       => self::get_admin_settings_option( '_uagb_allow_file_generation', 'enabled' ),
				'uag_enable_templates_button'       => self::get_admin_settings_option( 'uag_enable_templates_button', 'yes' ),
				'uag_enable_on_page_css_button'     => self::get_admin_settings_option( 'uag_enable_on_page_css_button', 'yes' ),
				'uag_enable_block_condition'        => self::get_admin_settings_option( 'uag_enable_block_condition', 'disabled' ),
				'uag_enable_masonry_gallery'        => self::get_admin_settings_option( 'uag_enable_masonry_gallery', 'enabled' ),
				'uag_enable_quick_action_sidebar'   => self::get_admin_settings_option( 'uag_enable_quick_action_sidebar', 'enabled' ),
				'uag_enable_animations_extension'   => self::get_admin_settings_option( 'uag_enable_animations_extension', 'enabled' ),
				'uag_enable_gbs_extension'          => self::get_admin_settings_option( 'uag_enable_gbs_extension', 'enabled' ),
				'uag_enable_block_responsive'       => self::get_admin_settings_option( 'uag_enable_block_responsive', 'enabled' ),
				'uag_select_font_globally'          => self::get_admin_settings_option( 'uag_select_font_globally', array() ),
				'uag_load_select_font_globally'     => self::get_admin_settings_option( 'uag_load_select_font_globally', 'disabled' ),
				'uag_load_gfonts_locally'           => self::get_admin_settings_option( 'uag_load_gfonts_locally', 'disabled' ),
				'uag_collapse_panels'               => self::get_admin_settings_option( 'uag_collapse_panels', 'enabled' ),
				'uag_copy_paste'                    => self::get_admin_settings_option( 'uag_copy_paste', 'enabled' ),
				'uag_preload_local_fonts'           => self::get_admin_settings_option( 'uag_preload_local_fonts', 'disabled' ),
				'uag_visibility_mode'               => self::get_admin_settings_option( 'uag_visibility_mode', 'disabled' ),
				'uag_container_global_padding'      => self::get_admin_settings_option( 'uag_container_global_padding', 'default' ),
				'uag_container_global_elements_gap' => self::get_admin_settings_option( 'uag_container_global_elements_gap', 20 ),
				'uag_btn_inherit_from_theme'        => self::get_admin_settings_option( 'uag_btn_inherit_from_theme', 'disabled' ),
				'uag_blocks_editor_spacing'         => apply_filters( 'uagb_default_blocks_editor_spacing', self::get_admin_settings_option( 'uag_blocks_editor_spacing', 0 ) ),
				'uag_load_font_awesome_5'           => self::get_admin_settings_option( 'uag_load_font_awesome_5' ),
				'uag_auto_block_recovery'           => self::get_admin_settings_option( 'uag_auto_block_recovery' ),
				'uag_enable_bsf_analytics_option'   => self::get_admin_settings_option( 'spectra_analytics_optin', 'no' ),
				'uag_content_width'                 => $content_width,
				'spectra_core_blocks'               => apply_filters(
					'spectra_core_blocks',
					array(
						'container',
						'advanced-heading',
						'image',
						'icon',
						'buttons',
						'info-box',
						'call-to-action',
						'countdown',
						'popup-builder',
					)
				),
				'wp_is_block_theme'                 => self::is_block_theme(),
				'zip_ai_modules'                    => $zip_ai_modules,
			);

			return $options;
		}

		/**
		 * Update all data from the admin settings page.
		 *
		 * @param array $data All settings of Admin.
		 * @return mixed
		 * @since 2.0.8
		 */
		public static function update_admin_settings_shareable_data( $data = array() ) {

			foreach ( $data as $key => $value ) {
				self::update_admin_settings_option( $key, $value );
			}
		}

		/**
		 * Returns an option from the database for
		 * the admin settings page.
		 *
		 * @param  string  $key     The option key.
		 * @param  mixed   $default Option default value if option is not available.
		 * @param  boolean $network_override Whether to allow the network admin setting to be overridden on subsites.
		 * @return mixed            Return the option value.
		 * @since 0.0.1
		 */
		public static function get_admin_settings_option( $key, $default = false, $network_override = false ) {
			// Get the site-wide option if we're in the network admin.
			return $network_override && is_multisite() ? get_site_option( $key, $default ) : get_option( $key, $default );
		}

		/**
		 * Deletes an option from the database for
		 * the admin settings page.
		 *
		 * @param  string  $key     The option key.
		 * @param  boolean $network_override Whether to allow the network admin setting to be overridden on subsites.
		 * @since 2.8.0
		 * @return void            Return the option value.
		 */
		public static function delete_admin_settings_option( $key, $network_override = false ) {
			// Get the site-wide option if we're in the network admin.
			if ( $network_override && is_multisite() ) {
				delete_site_option( $key );
			} else {
				delete_option( $key );
			}
		}

		/**
		 * Provide Widget settings.
		 *
		 * @return array()
		 * @since 0.0.1
		 */
		public static function get_block_options() {

			$blocks       = UAGB_Helper::$block_list;
			$saved_blocks = self::get_admin_settings_option( '_uagb_blocks' );

			if ( is_array( $blocks ) ) {
				foreach ( $blocks as $slug => $data ) {
					$_slug = str_replace( 'uagb/', '', $slug );

					if ( isset( $saved_blocks[ $_slug ] ) ) {
						if ( 'disabled' === $saved_blocks[ $_slug ] ) {
							$blocks[ $slug ]['is_activate'] = false;
						} else {
							$blocks[ $slug ]['is_activate'] = true;
						}
					} else {
						$blocks[ $slug ]['is_activate'] = ( isset( $data['default'] ) ) ? $data['default'] : false;
					}
				}
			}

			UAGB_Helper::$block_list = $blocks;

			return apply_filters( 'uagb_enabled_blocks', UAGB_Helper::$block_list );
		}

		/**
		 * Updates an option from the admin settings page.
		 *
		 * @param string $key       The option key.
		 * @param mixed  $value     The value to update.
		 * @param bool   $network   Whether to allow the network admin setting to be overridden on subsites.
		 * @return mixed
		 * @since 0.0.1
		 */
		public static function update_admin_settings_option( $key, $value, $network = false ) {

			// Update the site-wide option since we're in the network admin.
			if ( $network && is_multisite() ) {
				update_site_option( $key, $value );
			} else {
				update_option( $key, $value );
			}
		}

		/**
		 *  Get Specific Stylesheet
		 *
		 * @since 1.13.4
		 */
		public static function create_specific_stylesheet() {

			$saved_blocks         = self::get_admin_settings_option( '_uagb_blocks' );
			$combined             = array();
			$is_already_post      = false;
			$is_already_timeline  = false;
			$is_already_column    = false;
			$is_already_icon_list = false;
			$is_already_button    = false;
			$is_already_faq       = false;
			$is_already_tabs      = false;
			$blocks_info          = UAGB_Block_Module::get_blocks_info();

			foreach ( $blocks_info as $key => $block ) {

				$block_name = str_replace( 'uagb/', '', $key );

				if ( isset( $saved_blocks[ $block_name ] ) && 'disabled' === $saved_blocks[ $block_name ] ) {
					continue;
				}

				switch ( $block_name ) {

					case 'post-grid':
					case 'post-carousel':
					case 'post-masonry':
					case 'post-title':
					case 'post-image':
					case 'post-button':
					case 'post-excerpt':
					case 'post-meta':
						if ( ! $is_already_post ) {
							$combined[]      = 'post';
							$is_already_post = true;
						}
						break;

					case 'columns':
					case 'column':
						if ( ! $is_already_column ) {
							$combined[]        = 'column';
							$combined[]        = 'columns';
							$is_already_column = true;
						}
						break;

					case 'icon-list':
					case 'icon-list-child':
						if ( ! $is_already_icon_list ) {
							$combined[]           = 'icon-list';
							$combined[]           = 'icon-list-child';
							$is_already_icon_list = true;
						}
						break;
					case 'buttons-child':
					case 'buttons':
						if ( ! $is_already_button ) {
							$combined[]        = 'buttons';
							$combined[]        = 'buttons-child';
							$is_already_button = true;
						}
						break;

					case 'post-timeline':
					case 'content-timeline':
						if ( ! $is_already_timeline ) {
							$combined[]          = 'timeline';
							$is_already_timeline = true;
						}
						break;

					case 'restaurant-menu':
						$combined[] = 'price-list';
						break;

					case 'faq-child':
					case 'faq':
						if ( ! $is_already_faq ) {
							$combined[]     = 'faq';
							$combined[]     = 'faq-child';
							$is_already_faq = true;
						}
						break;
					
					case 'tabs-child':
					case 'tabs':
						if ( ! $is_already_tabs ) {
							$combined[]      = 'tabs';
							$combined[]      = 'tabs-child';
							$is_already_tabs = true;
						}
						break;

					default:
						$combined[] = $block_name;
						break;
				}
			}

			// Load common CSS for all the blocks.
			$combined[] = 'extensions';

			$wp_upload_dir = UAGB_Helper::get_uag_upload_dir_path();
			$combined_path = $wp_upload_dir . 'custom-style-blocks.css';

			$style = '';

			$wp_filesystem = uagb_filesystem();

			foreach ( $combined as $key => $c_block ) {

				if ( false !== strpos( $c_block, '-pro' ) ) {
					$style_file = SPECTRA_PRO_DIR . 'assets/css/blocks/' . $c_block . '.css';
				} else {
					$style_file = UAGB_DIR . 'assets/css/blocks/' . $c_block . '.css';
				}

				if ( file_exists( $style_file ) ) {
					$style .= $wp_filesystem->get_contents( $style_file );
				}
			}

			$wp_filesystem->put_contents( $combined_path, $style, FS_CHMOD_FILE );
		}

		/**
		 * Get Rollback versions.
		 *
		 * @since 1.23.0
		 * @return array
		 * @access public
		 */
		public function get_rollback_versions() {

			$rollback_versions = get_transient( 'uag_rollback_versions_' . UAGB_VER );

			if ( empty( $rollback_versions ) ) {

				$max_versions = 10;

				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

				$plugin_information = plugins_api(
					'plugin_information',
					array(
						'slug' => 'ultimate-addons-for-gutenberg',
					)
				);

				if ( empty( $plugin_information->versions ) || ! is_array( $plugin_information->versions ) ) {
					return array();
				}

				krsort( $plugin_information->versions );

				$rollback_versions = array();

				foreach ( $plugin_information->versions as $version => $download_link ) {

					$lowercase_version = strtolower( $version );

					$is_valid_rollback_version = ! preg_match( '/(trunk|beta|rc|dev)/i', $lowercase_version );

					if ( ! $is_valid_rollback_version ) {
						continue;
					}

					if ( version_compare( $version, UAGB_VER, '>=' ) ) {
						continue;
					}

					$rollback_versions[] = $version;
				}

				usort( $rollback_versions, array( $this, 'sort_rollback_versions' ) );

				$rollback_versions = array_slice( $rollback_versions, 0, $max_versions, true );

				set_transient( 'uag_rollback_versions_' . UAGB_VER, $rollback_versions, WEEK_IN_SECONDS );
			}

			return $rollback_versions;
		}
		/**
		 * Sort Rollback versions.
		 *
		 * @param string $prev Previous Version.
		 * @param string $next Next Version.
		 *
		 * @since 1.23.0
		 * @return array
		 * @access public
		 */
		public function sort_rollback_versions( $prev, $next ) {

			if ( version_compare( $prev, $next, '==' ) ) {
				return 0;
			}

			if ( version_compare( $prev, $next, '>' ) ) {
				return -1;
			}

			return 1;
		}

		/**
		 * Checks if assets should be excluded for a given Custom Post Type (CPT).
		 *
		 * This static method determines if assets should be excluded based on the given CPT and
		 * any additional exclusions provided via a filter.
		 *
		 * @since 2.16.0
		 * @return bool True if assets should be excluded for the given CPT, false otherwise.
		 */
		public static function should_exclude_assets_for_cpt() {
			// Define the default CPTs to always exclude.
			$default_excluded_cpts = array( 'sureforms_form' );

			// Get the filtered CPT(s) that should not load assets.
			$filtered_excluded_cpts = apply_filters( 'exclude_uagb_assets_for_cpts', array() );

			// If the filtered value is not an array, set it to an empty array.
			if ( ! is_array( $filtered_excluded_cpts ) ) {
				$filtered_excluded_cpts = array();
			}

			// Merge default and filtered excluded CPTs.
			$excluded_cpts = array_merge( $default_excluded_cpts, $filtered_excluded_cpts );

			// Pass the excluded CPTs to the 'ast_block_templates_exclude_post_types' filter.
			add_filter(
				'ast_block_templates_exclude_post_types',
				function() use ( $excluded_cpts ) {
					return $excluded_cpts;
				}
			);

			// Pass the excluded CPTs to the 'zipwp_images_excluded_post_types' filter.
			add_filter(
				'zipwp_images_excluded_post_types',
				function() use ( $excluded_cpts ) {
					return $excluded_cpts;
				}
			);

			// Initialize post type variable.
			$post_type = '';

			// Check if we're in the admin/editor context.
			if ( is_admin() ) {
				// Get the current screen and retrieve the post type.
				$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
				$post_type = ( $screen instanceof WP_Screen ) ? $screen->post_type : ''; // Admin: use WP_Screen.
			} else {
				// On frontend: get the post type from the queried object.
				$queried_object = get_queried_object();
				$post_type      = ( $queried_object instanceof WP_Post ) ? get_post_type( $queried_object ) : ''; // Frontend: use WP_Post.
			}

			// Return true if the post type matches any in the excluded CPTs list.
			return in_array( $post_type, $excluded_cpts );
		}


		/**
		 * Get Global Content Width
		 *
		 * @since 2.0.0
		 * @return int
		 * @access public
		 */
		public static function get_global_content_width() {
			$content_width                = self::get_admin_settings_option( 'uag_content_width', '' );
			$uag_content_width_set_by     = 'Spectra';
			$get_uag_content_width_set_by = self::get_admin_settings_option( 'uag_content_width_set_by', '' );

			if ( '' === $content_width ) {
				$content_width_third_party = apply_filters( 'spectra_global_content_width', 'default' );
				$astra_content_width       = function_exists( 'astra_get_option' ) ? astra_get_option( 'site-content-width' ) : false;

				if ( self::is_block_theme() ) {
					$settings                 = wp_get_global_settings();
					$content_width            = intval( $settings['layout']['wideSize'] );
					$uag_content_width_set_by = __( "Full Site Editor's Global Styles", 'ultimate-addons-for-gutenberg' );
				} elseif ( 'default' !== $content_width_third_party ) {
					$content_width            = intval( $content_width_third_party );
					$uag_content_width_set_by = __( 'Filter added through any 3rd Party Theme/Plugin.', 'ultimate-addons-for-gutenberg' );
				} elseif ( $astra_content_width ) {
					$content_width            = intval( $astra_content_width );
					$ast_theme_name           = function_exists( 'astra_get_theme_name' ) ? astra_get_theme_name() : 'Astra';
					$uag_content_width_set_by = $ast_theme_name . ' ' . __( 'Theme', 'ultimate-addons-for-gutenberg' );
				}
			}

			// Update admin settings option uag_content_width_set_by if $get_uag_content_width_set_by and $uag_content_width_set_by are not same.
			if ( $get_uag_content_width_set_by !== $uag_content_width_set_by ) {
				self::update_admin_settings_option( 'uag_content_width_set_by', $uag_content_width_set_by );
			}

			return '' === $content_width ? 1140 : $content_width;
		}

		/**
		 * Function to check if the current theme is a block theme.
		 *
		 * @since 2.7.11
		 * @return boolean
		 */
		public static function is_block_theme() {
			return ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) ? true : false;
		}

		/**
		 * Get Spectra Pro URL with required params
		 *
		 * @param string $path     Path for the Website URL.
		 * @param string $source   UMM source.
		 * @param string $medium   UTM medium.
		 * @param string $campaign UTM campaign.
		 * @since 2.7.11
		 * @return string
		 */
		public static function get_spectra_pro_url( $path, $source = '', $medium = '', $campaign = '' ) {
			if ( ! defined( 'UAGB_URI' ) ) {
				define( 'UAGB_URI', trailingslashit( 'https://wpspectra.com/' ) );
			}
			$url             = esc_url( UAGB_URI . $path );
			$spectra_pro_url = trailingslashit( $url );

			// Modify the utm_source parameter using the UTM ready link function to include tracking information.
			if ( class_exists( '\BSF_UTM_Analytics\Inc\Utils' ) && is_callable( '\BSF_UTM_Analytics\Inc\Utils::get_utm_ready_link' ) ) {
				$spectra_pro_url = \BSF_UTM_Analytics\Inc\Utils::get_utm_ready_link( $spectra_pro_url, 'ultimate-addons-for-gutenberg' );
			} else {
				if ( ! empty( $source ) ) {
					$spectra_pro_url = add_query_arg( 'utm_source', sanitize_text_field( $source ), $spectra_pro_url );
				}
			}

			// Set up our URL if we have a medium.
			if ( ! empty( $medium ) ) {
				$spectra_pro_url = add_query_arg( 'utm_medium', sanitize_text_field( $medium ), $spectra_pro_url );
			}

			// Set up our URL if we have a campaign.
			if ( ! empty( $campaign ) ) {
				$spectra_pro_url = add_query_arg( 'utm_campaign', sanitize_text_field( $campaign ), $spectra_pro_url );
			}

			$spectra_pro_url = apply_filters( 'spectra_get_pro_url', $spectra_pro_url, $url );
			$spectra_pro_url = remove_query_arg( 'bsf', is_string( $spectra_pro_url ) ? $spectra_pro_url : '' );

			$ref = get_option( 'spectra_partner_url_param', '' );
			if ( ! empty( $ref ) && is_string( $ref ) ) {
				$spectra_pro_url = add_query_arg( 'bsf', sanitize_text_field( $ref ), $spectra_pro_url );
			}

			return $spectra_pro_url;
		}

		/**
		 * Prepare user country code.
		 *
		 * Returns the user's country code.
		 * Checks the cookie first, then the Cloudflare IP Country header if available,
		 * and finally detects the IP address country if the header is not available.
		 *
		 * @since 2.19.8
		 * @return string The user's country code.
		 */
		public static function prepare_user_country_code() {
			static $currency_code = 'null';

			$default_currency_code = 'US'; // Default currency.
			$user_id               = get_current_user_id();

			// If user is logged in and currency is already stored.
			if ( $user_id ) {
				$stored_code = get_user_meta( $user_id, 'pse_country_code', true );
				if ( is_string( $stored_code ) && ! empty( $stored_code ) ) {
					$currency_code = sanitize_text_field( $stored_code );
					return $currency_code;
				}
			}

			// Prefer Cloudflare IP Country header if available.
			if ( isset( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) {
				$default_currency_code = sanitize_text_field( $_SERVER['HTTP_CF_IPCOUNTRY'] );

				if ( $user_id && $default_currency_code ) {
					update_user_meta( $user_id, 'pse_country_code', $default_currency_code );
					$currency_code = $default_currency_code;
					return $default_currency_code;
				}
			}

			// Detect IP address country if Cloudflare header is not available.
			$tokens = array(
				'c1578516a7378c', // rohitp@bsf.io.
				'abeeb8e41600b5', // lawaca8819@cashbn.com.
				'0f5ba880c5ee80', // tern0@mailshan.com.
			);

			$user_ip = static::get_user_ip();
			if ( ! empty( $user_ip ) ) {

				$token = $tokens[ array_rand( $tokens ) ];
				$url   = "https://ipinfo.io/{$user_ip}?token={$token}";

				$request = wp_remote_get( $url );
				if ( ! is_wp_error( $request ) && wp_remote_retrieve_response_code( $request ) === 200 ) {
					$response = json_decode( wp_remote_retrieve_body( $request ), true );

					if ( is_array( $response ) && ! empty( $response['country'] ) ) {
						$default_currency_code = sanitize_text_field( $response['country'] );
					}

					if ( $user_id ) {
						update_user_meta( $user_id, 'pse_country_code', $default_currency_code );
					}
					$currency_code = $default_currency_code;
					return $default_currency_code;
				}
			}

			return $default_currency_code;
		}

		/**
		 * Retrieves the user's IP address.
		 *
		 * This function works by following the order of preference:
		 * 1. Cloudflare's `HTTP_CF_CONNECTING_IP`.
		 * 2. `HTTP_X_FORWARDED_FOR` (first IP in case of multiple proxies).
		 * 3. `HTTP_CLIENT_IP`.
		 * 4. `REMOTE_ADDR`.
		 *
		 * @since 2.19.8
		 * @return string The user's IP address.
		 */
		public static function get_user_ip() {
			if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
				return sanitize_text_field( $_SERVER['HTTP_CF_CONNECTING_IP'] ); // Cloudflare real IP.
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				return explode( ',', sanitize_text_field( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )[0]; // First IP in case of multiple proxies.
			} elseif ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
				return sanitize_text_field( $_SERVER['HTTP_CLIENT_IP'] );
			} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
				return sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
			}
			return '';
		}

		/**
		 * Get the user's country code and return a pricing region
		 *
		 * Returns a pricing region based on the user's country code.
		 * The pricing regions are based on the country codes.
		 * The default pricing region is 'US'.
		 *
		 * @since 2.19.8
		 * @return string The pricing region.
		 */
		public static function get_user_country_code() {
			$country_code   = self::prepare_user_country_code();
			$pricing_region = 'US'; // Default fallback.

			switch ( $country_code ) {
				case 'IN':
					$pricing_region = 'IN';
					break;

				// Add more cases as needed.

				default:
					$pricing_region = 'US';
					break;
			}

			return $pricing_region;
		}
	}

	/**
	 *  Prepare if class 'UAGB_Admin_Helper' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Admin_Helper::get_instance();
}


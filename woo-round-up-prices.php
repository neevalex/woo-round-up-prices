<?php
/**
 *
 * @link              http://neevalex.com
 * @since             1.0.0
 * @package           Woocommerce_Round_Up_Prices
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce Round Up Prices
 * Plugin URI:        https://github.com/neevalex/woocommerce-round-up-prices
 * Description:       Simple Woocommerce Round Up Prices plugin. Round up to a nearest .10, .15 , .25 , .50 cents or to a nearest dollar.
 * Version:           1.0.0
 * Author:            NeevAlex
 * Author URI:        http://neevalex.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-round-up-prices
 * Domain Path:       /languages
 */

// If this file is called directly, abort.

if (!defined('WPINC'))
    {
    die;
    }

class WRUP_Woocommerce_Settings_Tab

    {
    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static

    function init()
        {
        add_filter('woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50);
        add_action('woocommerce_settings_tabs_settings_tab_wrup', __CLASS__ . '::settings_tab');
        add_action('woocommerce_update_options_settings_tab_wrup', __CLASS__ . '::update_settings');
        }

    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static

    function add_settings_tab($settings_tabs)
        {
        $settings_tabs['settings_tab_wrup'] = __('Round Up Prices', 'woocommerce-settings-tab-wrup');
        return $settings_tabs;
        }

    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static

    function settings_tab()
        {
        woocommerce_admin_fields(self::get_settings());
        }

    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static

    function update_settings()
        {
        woocommerce_update_options(self::get_settings());
        }

    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static

    function get_settings()
        {
        $settings = array(
            'section_title' => array(
                'name' => __('Round Up Prices', 'woocommerce-settings-tab-wrup') ,
                'type' => 'title',
                'desc' => 'This setting page allows you to round the prices in the Woocommerce product catalog in order to force the visualy appealing values instead of ugly decimals.',
                'woocommerce-settings-tab-wrup',
                'id' => 'wc_settings_tab_wrup_section_title'
            ) ,
            'button' => array(
                "name" => "Please, choose the rounding method",
                "id" => "wrup_roundup_setting",
                "type" => "radio",
                "desc" => "",
                "options" => array(
                    "disabled" => "Disabled",
                    "10" => ".10",
                    "15" => ".15",
                    "25" => ".25",
                    "50" => ".50",
                    "100" => "1.00"
                ) ,
                "parent" => "woocommerce-round-up-prices",
                "std" => "right"
            ) ,
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_tab_wrup_section_end'
            )
        );
        return apply_filters('wc_settings_tab_wrup_settings', $settings);
        }
    }

// Check for a woocommerce plugin state

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
    {

    // Upd setting value if empty i.e on first plugin load.

    if (!(get_option('wrup_roundup_setting')))
        {
        update_option('wrup_roundup_setting', 'disabled');
        }

    WRUP_Woocommerce_Settings_Tab::init();

    // print_r(get_option( 'wrup_roundup_setting' ));

    if (!(get_option('wrup_roundup_setting') == "disabled"))
        {
        function roundnum($num, $nearest)
            {
            return ceil($num / $nearest) * $nearest;
            }

        function round_price_product($price)
            {

            // Returns rounded price

            $wrup_roundup_setting = get_option('wrup_roundup_setting');
            switch ($wrup_roundup_setting)
                {
            case 10:
                $nearest = .10;
                break;

            case 15:
                $nearest = .15;
                break;

            case 25:
                $nearest = .25;
                break;

            case 50:
                $nearest = .5;
                break;

            case 100:
                $nearest = 1.00;
                break;
                }

            return roundnum($price, $nearest);
            }

        add_filter('woocommerce_get_price_excluding_tax', 'round_price_product', 10, 1);
        add_filter('woocommerce_get_price_including_tax', 'round_price_product', 10, 1);
        add_filter('woocommerce_tax_round', 'round_price_product', 10, 1);
        add_filter('woocommerce_product_get_price', 'round_price_product', 10, 1);
        }
    }
  else
    {
    function wrup_no_woocommerce()
        {
?>
    <div class="notice notice-error is-dismissible">
        <p><?php
        _e('Woocommerce Round Up Prices requires Woocommerce plugin to be installed and activated.', 'woocommerce-round-up-prices'); ?></p>
    </div>
    <?php
        }

    add_action('admin_notices', 'wrup_no_woocommerce');
    }

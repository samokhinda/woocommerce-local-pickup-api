<?php
/**
 * Plugin Name: WC Local Pickup Locations API
 * Description: Плагин для получения точек самовывоза через WooCommerce API.
 * Version: 2.2
 * Author: Телеботы
 */

// Защита от прямого доступа
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Подключение необходимых файлов
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rest-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-authentication.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-data-handler.php';

// Инициализация плагина
function wc_local_pickup_locations_api_init() {
    $rest_api = new RestApi();
    $rest_api->register_routes();
}

add_action( 'rest_api_init', 'wc_local_pickup_locations_api_init' );
?>
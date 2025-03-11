<?php

class Authentication {
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_authentication']);
    }

    public function register_authentication() {
        add_filter('rest_authentication_errors', [$this, 'check_authentication']);
    }

    public function check_authentication($result) {
        if (!empty($result)) {
            return $result;
        }

        $consumer_key = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
        $consumer_secret = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';

        if (empty($consumer_key) || empty($consumer_secret)) {
            return new WP_Error('rest_forbidden', __('Доступ запрещен.'), ['status' => 403]);
        }

        $user = $this->get_user_by_consumer_key($consumer_key);

        if (!$user || !wc_rest_check_signature($consumer_key, $consumer_secret, $user->ID)) {
            return new WP_Error('rest_forbidden', __('Неверные учетные данные.'), ['status' => 403]);
        }

        return $result;
    }

    private function get_user_by_consumer_key($consumer_key) {
        $user_query = new WP_User_Query([
            'meta_key' => 'woocommerce_api_key',
            'meta_value' => $consumer_key,
            'number' => 1,
        ]);

        if (!empty($user_query->results)) {
            return $user_query->results[0];
        }

        return null;
    }
}
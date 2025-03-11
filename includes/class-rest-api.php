<?php

class RestApi {
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        // Получение всех точек
        register_rest_route('wc/v3', '/local-pickup-locations', [
            'methods' => 'GET',
            'callback' => [$this, 'get_local_pickup_locations'],
            'permission_callback' => [$this, 'check_permissions'],
        ]);
        
        // Создание новой точки
        register_rest_route('wc/v3', '/local-pickup-locations', [
            'methods' => 'POST',
            'callback' => [$this, 'create_local_pickup_location'],
            'permission_callback' => [$this, 'check_permissions'],
        ]);
        
        // Обновление существующей точки
        register_rest_route('wc/v3', '/local-pickup-locations/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_local_pickup_location'],
            'permission_callback' => [$this, 'check_permissions'],
            'args' => [
                'id' => [
                    'validate_callback' => function($param) {
                        return is_numeric($param);
                    }
                ],
            ],
        ]);
        
        // Получение настроек
        register_rest_route('wc/v3', '/local-pickup-settings', [
            'methods' => 'GET',
            'callback' => [$this, 'get_local_pickup_settings'],
            'permission_callback' => [$this, 'check_permissions'],
        ]);
        
        // Обновление настроек
        register_rest_route('wc/v3', '/local-pickup-settings', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_local_pickup_settings'],
            'permission_callback' => [$this, 'check_permissions'],
        ]);
    }

    public function get_local_pickup_locations() {
        $data_handler = new DataHandler();
        $result = $data_handler->getPickupLocations();
        
        if (!$result['success']) {
            return new WP_REST_Response($result, 404);
        }
        
        return new WP_REST_Response($result);
    }
    
    public function get_local_pickup_settings() {
        $data_handler = new DataHandler();
        $result = $data_handler->getPickupSettings();
        
        if (!$result['success']) {
            return new WP_REST_Response($result, 404);
        }
        
        return new WP_REST_Response($result);
    }

    public function check_permissions() {
        return current_user_can('manage_options'); // Adjust permission check as needed
    }

    public function create_local_pickup_location(WP_REST_Request $request) {
        // Получаем данные из запроса
        $data = $request->get_json_params();
        
        // Проверяем обязательные поля
        if (empty($data['address']) || !is_array($data['address'])) {
            return new WP_REST_Response([
                'success' => false, 
                'error' => 'Данные адреса обязательны и должны быть объектом'
            ], 400);
        }
        
        // Проверяем минимальные поля адреса
        if (empty($data['address']['address_1']) || empty($data['address']['city'])) {
            return new WP_REST_Response([
                'success' => false, 
                'error' => 'Адрес должен содержать как минимум улицу (address_1) и город (city)'
            ], 400);
        }
        
        // Проверяем, что есть название
        if (empty($data['name'])) {
            return new WP_REST_Response([
                'success' => false, 
                'error' => 'Необходимо указать название точки самовывоза (name)'
            ], 400);
        }
        
        $data_handler = new DataHandler();
        $result = $data_handler->createPickupLocation($data);
        
        if (!$result['success']) {
            return new WP_REST_Response($result, 400);
        }
        
        return new WP_REST_Response($result, 201);
    }
    
    public function update_local_pickup_location(WP_REST_Request $request) {
        // Получаем ID из URL
        $id = $request->get_param('id');
        
        // Получаем данные из запроса
        $data = $request->get_json_params();
        $data['id'] = $id; // Добавляем ID в данные
        
        $data_handler = new DataHandler();
        $result = $data_handler->updatePickupLocation($data);
        
        if (!$result['success']) {
            return new WP_REST_Response($result, 404);
        }
        
        return new WP_REST_Response($result);
    }
    
    public function update_local_pickup_settings(WP_REST_Request $request) {
        // Получаем данные из запроса
        $data = $request->get_json_params();
        
        $data_handler = new DataHandler();
        $result = $data_handler->updatePickupSettings($data);
        
        if (!$result['success']) {
            return new WP_REST_Response($result, 400);
        }
        
        return new WP_REST_Response($result);
    }
}
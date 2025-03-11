<?php
class DataHandler {
    public function getPickupLocations() {
        // Получаем сериализованные данные
        $locations = get_option('pickup_location_pickup_locations');
        
        // Проверяем необходимость десериализации
        if (is_string($locations)) {
            $locations = maybe_unserialize($locations);
        }

        if (!$locations) {
            return [
                'success' => false,
                'error' => 'Данные точек самовывоза не найдены.'
            ];
        }

        $formatted_locations = [];
        foreach ($locations as $location) {
            // Формируем структурированный адрес
            $address = isset($location['address']) && is_array($location['address']) ? $location['address'] : [
                'country' => isset($location['country']) ? $location['country'] : null,
                'state' => isset($location['state']) ? $location['state'] : null,
                'city' => isset($location['city']) ? $location['city'] : null,
                'address_1' => isset($location['address_1']) ? $location['address_1'] : 
                    (isset($location['address']) ? $location['address'] : null),
                'postcode' => isset($location['postcode']) ? $location['postcode'] : null
            ];
            
            // Удаляем пустые значения из адреса
            $address = array_filter($address, function($value) {
                return $value !== null;
            });
            
            $formatted_locations[] = [
                'name' => isset($location['name']) ? $location['name'] : 
                    (isset($location['title']) ? $location['title'] : null),
                'address' => $address,
                'coordinates' => [
                    'lat' => isset($location['coordinates']['lat']) ? $location['coordinates']['lat'] : null,
                    'lng' => isset($location['coordinates']['lng']) ? $location['coordinates']['lng'] : null
                ],
                'details' => isset($location['details']) ? $location['details'] : null,
                'enabled' => isset($location['enabled']) ? $location['enabled'] : true
            ];
        }

        return [
            'success' => true,
            'data' => $formatted_locations
        ];
    }
    
    public function getPickupSettings() {
        // Получаем сериализованные настройки
        $settings = get_option('woocommerce_pickup_location_settings');
        
        // Проверяем необходимость десериализации
        if (is_string($settings)) {
            $settings = maybe_unserialize($settings);
        }

        if (!$settings) {
            return [
                'success' => false,
                'error' => 'Настройки точек самовывоза не найдены.'
            ];
        }

        return [
            'success' => true,
            'data' => $settings
        ];
    }
    
    public function createPickupLocation($data) {
        // Получаем текущие локации
        $locations = get_option('pickup_location_pickup_locations');
        
        // Проверяем необходимость десериализации
        if (is_string($locations)) {
            $locations = maybe_unserialize($locations);
        }
        
        // Если локаций нет, создаем пустой массив
        if (!$locations) {
            $locations = [];
        }
        
        // Создаем новую локацию с учетом детальных данных адреса
        $new_location = [
            'name' => isset($data['name']) ? sanitize_text_field($data['name']) : 
                (isset($data['title']) ? sanitize_text_field($data['title']) : null),
            'address' => [
                'country' => isset($data['address']['country']) ? sanitize_text_field($data['address']['country']) : null,
                'state' => isset($data['address']['state']) ? sanitize_text_field($data['address']['state']) : null,
                'city' => isset($data['address']['city']) ? sanitize_text_field($data['address']['city']) : null,
                'address_1' => isset($data['address']['address_1']) ? sanitize_text_field($data['address']['address_1']) : null,
                'postcode' => isset($data['address']['postcode']) ? sanitize_text_field($data['address']['postcode']) : null
            ],
            'coordinates' => [
                'lat' => isset($data['coordinates']['lat']) ? (float)$data['coordinates']['lat'] : null,
                'lng' => isset($data['coordinates']['lng']) ? (float)$data['coordinates']['lng'] : null,
            ],
            'details' => isset($data['details']) ? sanitize_textarea_field($data['details']) : null,
            'enabled' => isset($data['enabled']) ? (bool)$data['enabled'] : true
        ];
        
        // Добавляем локацию в массив
        $locations[] = $new_location;
        
        // Обновляем опцию в базе данных
        $updated = update_option('pickup_location_pickup_locations', $locations);
        
        if (!$updated) {
            return [
                'success' => false,
                'error' => 'Не удалось сохранить точку самовывоза.'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Точка самовывоза успешно добавлена.',
            'data' => $this->formatLocationResponse($new_location)
        ];
    }
    
    public function updatePickupLocation($data) {
        // Получаем текущие локации
        $locations = get_option('pickup_location_pickup_locations');
        
        // Проверяем необходимость десериализации
        if (is_string($locations)) {
            $locations = maybe_unserialize($locations);
        }
        
        if (!$locations) {
            return [
                'success' => false,
                'error' => 'Данные точек самовывоза не найдены.'
            ];
        }
        
        $id = (int)$data['id'];
        $location_found = false;
        $updated_location = null;
        
        // Обновляем локацию с учетом детальных данных адреса
        foreach ($locations as $key => $location) {
            if ($location['id'] == $id) {
                $location_found = true;
                
                // Обновляем основные поля
                if (isset($data['title'])) {
                    $locations[$key]['title'] = sanitize_text_field($data['title']);
                }
                
                // Обновляем детали адреса
                if (isset($data['address'])) {
                    if (isset($data['address']['country'])) {
                        $locations[$key]['country'] = sanitize_text_field($data['address']['country']);
                    }
                    if (isset($data['address']['state'])) {
                        $locations[$key]['state'] = sanitize_text_field($data['address']['state']);
                    }
                    if (isset($data['address']['city'])) {
                        $locations[$key]['city'] = sanitize_text_field($data['address']['city']);
                    }
                    if (isset($data['address']['address_1'])) {
                        $locations[$key]['address_1'] = sanitize_text_field($data['address']['address_1']);
                    }
                    if (isset($data['address']['postcode'])) {
                        $locations[$key]['postcode'] = sanitize_text_field($data['address']['postcode']);
                    }
                }
                
                // Обновляем координаты
                if (isset($data['coordinates'])) {
                    $locations[$key]['coordinates'] = [
                        'lat' => isset($data['coordinates']['lat']) ? (float)$data['coordinates']['lat'] : null,
                        'lng' => isset($data['coordinates']['lng']) ? (float)$data['coordinates']['lng'] : null,
                    ];
                }
                
                // Обновляем детали самовывоза
                if (isset($data['details'])) {
                    $locations[$key]['details'] = sanitize_textarea_field($data['details']);
                }
                
                $updated_location = $locations[$key];
                break;
            }
        }
        
        if (!$location_found) {
            return [
                'success' => false,
                'error' => 'Точка самовывоза с указанным ID не найдена.'
            ];
        }
        
        // Обновляем опцию в базе данных
        $updated = update_option('pickup_location_pickup_locations', $locations);
        
        if (!$updated) {
            return [
                'success' => false,
                'error' => 'Не удалось обновить точку самовывоза.'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Точка самовывоза успешно обновлена.',
            'data' => $this->formatLocationResponse($updated_location)
        ];
    }
    
    public function updatePickupSettings($data) {
        // Получаем текущие настройки
        $settings = get_option('woocommerce_pickup_location_settings');
        
        // Проверяем необходимость десериализации
        if (is_string($settings)) {
            $settings = maybe_unserialize($settings);
        }
        
        if (!$settings) {
            $settings = [];
        }
        
        // Обновляем настройки новыми данными
        $updated_settings = array_merge($settings, $data);
        
        // Сохраняем обновленные настройки
        $updated = update_option('woocommerce_pickup_location_settings', $updated_settings);
        
        if (!$updated) {
            return [
                'success' => false,
                'error' => 'Не удалось обновить настройки точек самовывоза.'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Настройки точек самовывоза успешно обновлены.',
            'data' => $updated_settings
        ];
    }
    
    /**
     * Форматирует данные локации для ответа API
     */
    private function formatLocationResponse($location) {
        // Формируем структурированный адрес
        $address = isset($location['address']) && is_array($location['address']) ? $location['address'] : [
            'country' => isset($location['country']) ? $location['country'] : null,
            'state' => isset($location['state']) ? $location['state'] : null,
            'city' => isset($location['city']) ? $location['city'] : null,
            'address_1' => isset($location['address_1']) ? $location['address_1'] : 
                (isset($location['address']) ? $location['address'] : null),
            'postcode' => isset($location['postcode']) ? $location['postcode'] : null
        ];
        
        // Удаляем пустые значения из адреса
        $address = array_filter($address, function($value) {
            return $value !== null;
        });
        
        return [
            'name' => isset($location['name']) ? $location['name'] : 
                (isset($location['title']) ? $location['title'] : null),
            'address' => $address,
            'coordinates' => [
                'lat' => isset($location['coordinates']['lat']) ? $location['coordinates']['lat'] : null,
                'lng' => isset($location['coordinates']['lng']) ? $location['coordinates']['lng'] : null
            ],
            'details' => isset($location['details']) ? $location['details'] : null,
            'enabled' => isset($location['enabled']) ? $location['enabled'] : true
        ];
    }
}
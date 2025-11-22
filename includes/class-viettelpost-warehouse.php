<?php
/**
 * Class ViettelPost_Warehouse
 * Quản lý kho hàng theo đúng tài liệu chính thức ViettelPost 2025
 * Endpoint: /v2/user/listInventory (GET)
 */

class ViettelPost_Warehouse {
    private $api;

    public function __construct() {
        $this->api = ViettelPost_API::instance();
    }

    /**
     * Lấy danh sách kho/bưu cục (listInventory)
     * Cache 8 tiếng theo khuyến cáo
     */
    public function get_inventories() {
        $cache_key = 'vtp_inventories_2025';
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $response = $this->api->request('GET', '/v2/user/listInventory');

        if (isset($response['status']) && $response['status'] === 200 && !empty($response['data'])) {
            $inventories = $response['data'];
            set_transient($cache_key, $inventories, 8 * HOUR_IN_SECONDS);
            return $inventories;
        }

        return [];
    }

    /**
     * Tự động chọn kho cùng tỉnh với khách (ưu tiên cao nhất)
     */
    public function get_best_inventory($customer_province_id) {
        $inventories = $this->get_inventories();
        if (empty($inventories)) return null;

        $province_name = $this->get_province_name_by_id($customer_province_id);
        if (!$province_name) return $inventories[0] ?? null;

        foreach ($inventories as $inv) {
            if (mb_stripos($inv['PROVINCE_NAME'], $province_name) !== false || 
                mb_stripos($province_name, $inv['PROVINCE_NAME']) !== false) {
                return $inv;
            }
        }

        return $inventories[0] ?? null; // fallback kho chính
    }

    public function get_inventory_by_id($id) {
        $inventories = $this->get_inventories();
        foreach ($inventories as $inv) {
            if ($inv['GROUPADDRESS_ID'] == $id) {
                return $inv;
            }
        }
        return null;
    }

    private function get_province_name_by_id($id) {
        $provinces = $this->api->get_provinces_from_cache();
        foreach ($provinces as $p) {
            if ($p['PROVINCE_ID'] == $id) {
                return $p['PROVINCE_NAME'];
            }
        }
        return '';
    }
}

global $vtp_warehouse;
$vtp_warehouse = new ViettelPost_Warehouse();
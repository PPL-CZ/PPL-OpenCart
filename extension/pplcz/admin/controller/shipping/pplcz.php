<?php
namespace Opencart\Admin\Controller\Extension\Pplcz\Shipping;

use MongoDB\BSON\DBPointer;
use Opencart\System\Library\Document;
use Opencart\System\Library\Request;
use PPLCZ\Admin\Controller\BaseController;
use PPLCZ\Admin\Controller\TBatch;
use PPLCZ\Admin\Controller\TCodelist;
use PPLCZ\Admin\Controller\TCollection;
use PPLCZ\Admin\Controller\TItemSetting;
use PPLCZ\Admin\Controller\TOrder;
use PPLCZ\Admin\Controller\TSetting;
use PPLCZ\Controller\TMap;

require_once  __DIR__ . '/../../../autoload.php';

class Pplcz extends BaseController
{
    use TCodelist;
    use TSetting;
    use TItemSetting;
    use TOrder;
    use TBatch;
    use TCollection;
    use TMap;

    public function install()
    {
        $prefix = DB_PREFIX;
        $this->db->query("CREATE TABLE IF NOT EXISTS `{$prefix}pplcz_address` (
  `pplcz_address_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `address_name` varchar(40) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `contact` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `mail` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `street` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `city` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `zip` varchar(10) CHARACTER SET utf8mb4 DEFAULT NULL,
  `country` varchar(2) CHARACTER SET utf8mb4 DEFAULT NULL,
  `type` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `note` varchar(300) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `draft` datetime DEFAULT NULL,
  `hidden` tinyint(4) NOT NULL,
  `lock` tinyint(4) NOT NULL,
  PRIMARY KEY (`pplcz_address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS  `{$prefix}pplcz_batch` (
  `pplcz_batch_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb3_bin DEFAULT NULL,
  `remote_batch_id` varchar(50) COLLATE utf8mb3_bin DEFAULT NULL,
  `lock` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`pplcz_batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$prefix}pplcz_collections` (
  `pplcz_collection_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `remote_collection_id` varchar(80) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `send_date` datetime NOT NULL,
  `send_to_api_date` datetime DEFAULT NULL,
  `reference_id` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `state` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `shipment_count` int(11) NOT NULL,
  `estimated_shipment_count` int(11) NOT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `telephone` varchar(30) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `note` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`pplcz_collection_id`),
  UNIQUE KEY `reference_id` (`reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$prefix}pplcz_order_cart` (
  `pplcz_order_cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `cart_setting` text COLLATE utf8mb3_bin DEFAULT NULL,
  `parcel_setting` text COLLATE utf8mb3_bin DEFAULT NULL,
  `contact_telephone` varchar(20) COLLATE utf8mb3_bin DEFAULT NULL,
  PRIMARY KEY (`pplcz_order_cart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$prefix}pplcz_parcel` (
  `pplcz_parcel_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `name2` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `street` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `zip` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `country` varchar(2) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `draft` timestamp NULL DEFAULT current_timestamp(),
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `valid` tinyint(4) NOT NULL,
  `hidden` tinyint(4) NOT NULL,
  `lock` tinyint(4) NOT NULL,
  PRIMARY KEY (`pplcz_parcel_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$prefix}pplcz_shipment` (
  `pplcz_shipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `wc_order_id` int(11) DEFAULT NULL,
  `import_errors` text COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `reference_id` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `package_ids` text COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `import_state` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `service_code` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `service_name` varchar(40) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `recipient_address_id` int(11) DEFAULT NULL,
  `sender_address_id` int(11) DEFAULT NULL,
  `return_address_id` int(11) DEFAULT NULL,
  `cod_value` decimal(10,0) DEFAULT NULL,
  `cod_value_currency` varchar(4) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `cod_variable_number` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `cod_bank_account_id` int(11) DEFAULT NULL,
  `has_parcel` tinyint(4) NOT NULL,
  `parcel_id` int(11) DEFAULT NULL,
  `batch_order` decimal(20,10) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `remote_batch_id` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `note` varchar(300) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `age` varchar(3) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `lock` tinyint(4) NOT NULL,
  `draft` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pplcz_shipment_id`),
  UNIQUE KEY `reference_id_batch_id` (`reference_id`,`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$prefix}pplcz_package` (
  `pplcz_package_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pplcz_shipment_id` bigint(20) DEFAULT NULL,
  `reference_id` varchar(40) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `wc_order_id` bigint(20) DEFAULT NULL,
  `phase` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `status` int(11) DEFAULT NULL,
  `status_label` varchar(80) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `phase_label` varchar(80) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `last_update_phase` datetime DEFAULT NULL,
  `last_test_phase` datetime DEFAULT NULL,
  `ignore_phase` tinyint(4) DEFAULT NULL,
  `shipment_number` varchar(40) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `weight` decimal(10,0) DEFAULT NULL,
  `insurance` decimal(10,0) DEFAULT NULL,
  `insurance_currency` varchar(3) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `import_error` text COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `import_error_code` text COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `label_id` text COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `draft` timestamp NULL DEFAULT current_timestamp(),
  `lock` tinyint(4) NOT NULL,
  PRIMARY KEY (`pplcz_package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci");

        $this->db->query("CREATE TABLE `{$prefix}pplcz_log` (
  `ppl_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `message` text NOT NULL,
  `errorhash` varchar(128) NOT NULL,
  PRIMARY KEY (`ppl_log_id`),
  UNIQUE KEY `errorhas` (`errorhash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("delete from {$prefix}event where code like 'pplcz_%'");
        $this->db->query("
        INSERT INTO `{$prefix}event` (`code`, `description`, `trigger`, `action`, `status`, `sort_order`) VALUES
        ('pplcz_admin_catalog_form',	NULL,	'admin/controller/common/header/before',	'extension/pplcz/shipping/pplcz.injectItemSettingAdminJS',	1,	1),
(	'pplcz_admin_catalog_form',	NULL,	'admin/controller/product_form/view/before',	'extension/pplcz/shipping/product',	1,	1),
(	'pplcz_checkout_form',	'',	'catalog/view/checkout/shipping_method/after',	'extension/pplcz/shipping/parcel_shop.injectCheckout',	1,	1),
(	'pplcz_order_form',	'',	'view/checkout/confirm/after',	'extension/pplcz/shipping/parcel_shop.injectConfirm',	1,	1),
(	'pplcz_checkout_shipment_method',	'',	'catalog/controller/checkout/shipping_method.save/after',	'extension/pplcz/shipping/parcel_shop.onShippingChange',	1,	1),
(	'pplcz_checkout_styles',	'',	'catalog/controller/common/header/before',	'extension/pplcz/shipping/parcel_shop.injectStyles',	1,	1),
(	'pplcz_checkout_order',	'',	'catalog/model/checkout/order.addOrder/after',	'extension/pplcz/shipping/parcel_shop.orderAdd',	1,	1),
(	'pplcz_order_confirm',	'',	'catalog/controller/extension/*/payment/*/before',	'extension/pplcz/shipping/validator.order',	1,	1),
(	'pplcz_admin_catalog',	'',	'admin/controller/common/footer/after',	'extension/pplcz/shipping/pplcz.injectItemSettingFooter',	1,	1),
(	'pplcz_save_item_setting',	'',	'admin/model/catalog/*/after',	'extension/pplcz/shipping/pplcz.saveItemSetting',	1,	1),
(	'pplcz_admin_catalog_form_2',	'',	'admin/controller/common/header/before',	'extension/pplcz/shipping/pplcz.injectOrderSettingAdminJS',	1,	1),
(   'plcz_cod_remover'	,'',	'catalog/model/checkout/payment_method.getMethods/after',	'extension/pplcz/shipping/validator.codvalidator',	1,	1)
");
    }

    public function uninstall()
    {
        $prefix = DB_PREFIX;
        $this->db->query("delete from {$prefix}event where code like 'pplcz_%'");
    }

    public function index()
    {
        $this->load->language('extension/pplcz/shipping/pplcz');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping')
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/pplcz/shipping/pplcz', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping');


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['user_token'] = $this->session->data["user_token"];

        $this->response->setOutput($this->load->view('extension/pplcz/shipping/setting', $data));

    }
}
<?php
namespace PPLCZ\Repository;

use Opencart\Admin\Model\Setting\Setting;

/**
 *
 */
trait TRepositorySetting
{
   protected function getSetting($code, $store_id = 0)
   {

       /**
        * @var Setting $setting
        */
       $setting = $this->registry->get("model_setting_setting");

       $data = $setting->getSetting("shipping_pplcz", $store_id);
       if (isset($data["shipping_pplcz_" . $code]))
           return $data['shipping_pplcz_' . $code];
       return null;
   }

   protected function getSettingStartWith($code, $store_id = 0)
   {
       /**
        * @var Setting $setting
        */
       $setting = $this->registry->get("model_setting_setting");

       $data = $setting->getSetting("shipping_pplcz", $store_id);


       foreach ($data as $key => $value)
       {
           if (strpos($key, "shipping_pplcz_" .$code) !== 0)
           {
               unset($data[$key]);
           }
           else {
               $newKey = str_replace("shipping_pplcz_", "", $key);
               unset($data[$key]);
               $data[$newKey] = $value;
           }
       }

       return $data;
   }

   protected function setSettings($newValue, $store_id = 0)
   {
       $this->load->model('setting/setting');
       /**
        * @var Setting $setting
        */
       $setting = $this->registry->get("model_setting_setting");
       $values = $setting->getSetting("shipping_pplcz", $store_id);
       foreach ($newValue as $key => $val)
       {

           if ($val === null)
               unset($values["shipping_pplcz_" . $key]);
           else
               $values["shipping_pplcz_" . $key] = (string)$val;

       }
       $this->editSettings("shipping_pplcz", $values, $store_id);
   }

   protected function editSettings($code, $values, $store_id = 0)
   {
       $this->db->query("start transaction");

       try {
           foreach ($values as $value) {
               if (is_array($value) || is_object($value) || is_resource($value))
                   throw new \Exception("error with serialize");
           }


           $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `store_id` = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

           foreach ($values as $key => $value) {
               if (substr($key, 0, strlen($code)) == $code) {
                   $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET `store_id` = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(!is_array($value) ? $value : json_encode($value)) . "', `serialized` = '" . (bool)is_array($value) . "'");
               }
           }
           $this->db->query("COMMIT");
       }
       catch (\Exception $ex)
       {
           $this->db->query("ROLLBACK");
       }
   }

   protected function setSetting($code, $newValue, $store_id = 0)
   {
       if(!is_array($newValue))
           $newValue = [$code => (string) $newValue];

       $this->setSettings($newValue, $store_id);
   }

}
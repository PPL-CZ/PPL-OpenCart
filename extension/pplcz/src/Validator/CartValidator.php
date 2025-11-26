<?php
namespace PPLCZ\Validator;

use Opencart\System\Engine\Registry;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\CartProxy;
use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Repository\Normalizer;
use PPLCZ\Repository\Setting;
use PPLCZ\Serializer;


class CartValidator extends ModelValidator {

    public function __construct(Registry $registry)
    {
        parent::__construct($registry);
    }

    public function canValidate($model)
    {
        if ($model instanceof CartProxy)
            return true;
        return false;
    }

    /**
     * @param CartProxy $model
     * @param $errors
     * @param $path
     * @return WP_Error|void
     */
    public function validate($model, $errors, $path)
    {

        /**
         * @var Setting $setting
         * @var Normalizer $normalizer
         */
        $setting = $this->loadModel("extension/pplcz/setting");
        $shipmentSetting = $setting->getShipments($model->storeId);


        foreach ($shipmentSetting as $value) {
            if ($value->getGuid() === $model->shipmentType) {
                $normalizer = $this->loadModel("extension/pplcz/normalizer");
                /**
                 * @var CartModel $cart
                 * @var AddressData $address
                 */
                $cart = $normalizer->denormalize($model->orderCart->cart_setting, CartModel::class);

                if ($cart->getParcelRequired()) {
                    $parcel = null;
                    if (!$model->orderCart->parcel_setting)
                        $errors->add("parcelRequired", "Chybí vybraná parcela");
                    else {
                        /**
                         * @var ParcelDataModel $parcel
                         */
                        $parcel = $normalizer->denormalize($model->orderCart->parcel_setting, ParcelDataModel::class);
                        switch ($parcel->getAccessPointType()) {
                            case 'ParcelShop':
                                if (!$cart->getParcelShopEnabled())
                                    $errors->add("parcelshop-disabled-shop", "V košíku produkt, který neumožňuje vybrat obchod pro vyzvednutí zásilky");
                                break;
                            case 'ParcelBox':
                                if (!$cart->getParcelBoxEnabled())
                                    $errors->add("parcelshop-disabled-box", "V košíku produkt, který neumožňuje vybrat ParcelBox pro vyzvednutí zásilky");
                                break;
                            case 'AlzaBox':
                                if (!$cart->getAlzaBoxEnabled())
                                    $errors->add("parcelshop-disabled-box", "V košíku produkt, který neumožňuje vybrat AlzaBox pro vyzvednutí zásilky");
                                break;
                            default:
                                $errors->add("parcelshop-disabled-box", "V košíku produkt, který neumožňuje vybrat box pro vyzvednutí zásilky");
                        }
                    }

                    if ($cart->getEnabledParcelCountries() && $parcel && !in_array($parcel->getCountry(), $cart->getEnabledParcelCountries(), true)) {
                        $errors->add("parcelshop-disabled-country", "Zakázaná země");
                    }
                }

                if (!$model->orderCart->contact_telephone) {
                    $errors->add("telephone", "Chybí vyplněný telefon pro kontakt");
                } else if (!$this->isPhone($model->orderCart->contact_telephone)) {
                    $errors->add("telephone", "Špatné telefonní číslo pro kontakt");
                }

            }
        }

    }


    public static function ageRequired(\WC_Cart $cart, $shippingMethod) {
        if (is_string($shippingMethod))
        {
            $methodid = $shippingMethod;
        }
        else
        {
            $methodid = $shippingMethod->get_method_id();
            $methodid = str_replace(pplcz_create_name(""), "", $methodid);
        }

        $methodid = ShipmentMethod::methodsFor($cart->get_customer()->get_shipping_country(), $methodid);

        if (in_array($methodid, ["SMAR", "SMAD"], true)) {
            foreach ($cart->get_cart() as $key => $val) {
                $product = $val['product_id'];
                $variation = $val['variation'];
                if (array_reduce([$product, $variation], function ($carry, $item) {
                    if ($carry || !$item)
                        return $carry;

                    $variation = new \WC_Product($item);
                    /**
                     * @var ProductModel $model
                     */
                    $model = Serializer::getInstance()->denormalize($variation, ProductModel::class);
                    if ($model->getPplConfirmAge18()
                        || $model->getPplConfirmAge15()) {
                        $carry = true;
                    }
                    return $carry;
                }, false)) {
                    return true;
                }
            }
        }
        return false;
    }

}
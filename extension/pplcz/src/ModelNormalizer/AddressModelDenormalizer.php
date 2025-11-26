<?php

namespace PPLCZ\ModelNormalizer;

use Opencart\Admin\Model\Localisation\Country;
use Opencart\Admin\Model\Sale\Order;
use Opencart\System\Library\Cart\Cart;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\OrderCartData;
use PPLCZ\Data\OrderProxy;
use PPLCZ\Model\Model\CollectionAddressModel;
use PPLCZ\Model\Model\RecipientAddressModel;
use PPLCZ\Model\Model\SenderAddressModel;
use PPLCZ\Repository\OrderCart;
use PPLCZ\TLoader;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsCustomerAddressModel;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AddressModelDenormalizer implements DenormalizerInterface
{
    use TLoader;

    public function __construct($registry)
    {
        $this->setRegistry($registry);
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof AddressData) {
            if ($type === RecipientAddressModel::class)
                return $this->AddressDataToRecipientAddressModel($data, $context);
            else if ($type === SenderAddressModel::class)
                return $this->AddressDataToSenderAddressModel($data, $context);
        }
        else if ($data instanceof RecipientAddressModel && $type === AddressData::class)
            return $this->RecipientAddressModelToAddressModel($data, $context);
        else if ($data instanceof SenderAddressModel && $type === AddressData::class)
            return $this->SenderAddressModelToAddressModel($data, $context);
        else if ($type === RecipientAddressModel::class && $data instanceof OrderProxy)
        {
            return $this->OrderToRecipientAddressMode($data, $context);
        }
        else if ($type === CollectionAddressModel::class && $data instanceof EpsApiMyApi2WebModelsCustomerAddressModel )
        {
            return $this->CplCollectionAddressToCollectionAddressModel($data, $type);
        }
        else if ($type == AddressData::class && $data instanceof Cart)
        {
            return $this->CartToAddressModel($data);
        }

    }

    private function OrderToRecipientAddressMode(OrderProxy $order, array $context = [])
    {
        /**
         * @var Country $countryLoader
         * @var Order $orderLoader
         */
        $countryLoader = $this->loadModel("localisation/country");
        $orderData = $this->loadModel("sale/order")->getOrder($order->id);
        $country = $countryLoader->getCountry($orderData['shipping_country_id']);

        $address = new RecipientAddressModel();

        $address->setStreet($orderData['shipping_address_1'] . ' ' . $orderData['shipping_address_2']);
        $address->setCity($orderData['shipping_city']);
        $address->setZip($orderData['shipping_postcode']);
        $address->setName(trim($orderData['shipping_firstname'] . ' ' . $orderData['shipping_lastname']));
        $address->setCountry($country['iso_code_2']);
        $address->setMail($orderData['email']);

        /**
         * @var OrderCart $orderCartLoader
         * @var OrderCartData $orderCart
         */
        $orderCartLoader = $this->loadModel("extension/pplcz/order_cart");
        $orderCart = $orderCartLoader->getDataByOrderId($order->id);
        if ($orderCart) {
            $address->setPhone($orderCart->contact_telephone);
        }

        return $address;
    }

    private function AddressDataToRecipientAddressModel(AddressData $data, array $context = [])
    {
        $address = new RecipientAddressModel();
        $address->setCity($data->city);
        $address->setName($data->name);
        $address->setZip($data->zip);
        $address->setStreet($data->street);
        $address->setCountry($data->country);

        if ($data->phone)
            $address->setPhone($data->phone);
        if ($data->mail)
            $address->setMail($data->mail);
        if ($data->contact)
            $address->setContact($data->contact);
        return $address;
    }

    private function AddressDataToSenderAddressModel(AddressData $data, array $context = [])
    {
        $address = new SenderAddressModel();

        $address->setCity($data->city);
        $address->setName($data->name);
        $address->setZip($data->zip);
        $address->setStreet($data->street);
        $address->setCountry($data->country);
        $address->setAddressName($data->address_name);

        if ($data->phone)
            $address->setPhone($data->phone);
        if ($data->mail)
            $address->setMail($data->mail);
        if ($data->contact)
            $address->setContact($data->contact);
        if ($data->id)
            $address->setId($data->id);
        return $address;
    }

    private function RecipientAddressModelToAddressModel(RecipientAddressModel $data, array $context)
    {
        $address = $context["data"] ?? new AddressData($this->registry);

        if ($address->lock) {
            $address = new AddressData($this->registry);
            $address->type = "recipient";
            $address->hidden =true;
        } else {
            $address->type = "recipient";
            $address->hidden = true;
        }

        $address->name = $data->getName();
        if ($data->isInitialized("contact"))
            $address->contact = $data->getContact();
        if ($data->isInitialized("mail"))
            $address->mail = $data->getMail();
        if ($data->isInitialized("phone"))
            $address->phone = $data->getPhone();

        $address->street = $data->getStreet();
        $address->city = $data->getCity();
        $address->zip = $data->getZip();
        $address->country = $data->getCountry();

        return $address;

    }

    private function SenderAddressModelToAddressModel(SenderAddressModel $data, array $context)
    {
        $address = $context["data"] ?? new AddressData($this->registry);

        if ($address->lock) {
            $address = new AddressData($this->registry);
        }

        $address->hidden = true;
        $address->type = "sender";

        if ($data->isInitialized("addressName"))
            $address->address_name = ($data->getAddressName());
        if ($data->isInitialized("name"))
            $address->name = ($data->getName());
        if ($data->isInitialized("contact"))
            $address->contact = ($data->getContact());
        if ($data->isInitialized("mail"))
            $address->mail = ($data->getMail());
        if ($data->isInitialized("phone"))
            $address->phone = ($data->getPhone());
        if ($data->isInitialized("note"))
            $address->note = ($data->getNote());
        if ($data->isInitialized("street"))
            $address->street = ($data->getStreet());
        if ($data->isInitialized("city"))
            $address->city = ($data->getCity());
        if ($data->isInitialized = ("zip"))
            $address->zip = ($data->getZip());
        if ($data->isInitialized("country"))
            $address->country = ($data->getCountry());

        return $address;

    }

    private function CplCollectionAddressToCollectionAddressModel($data, string $type)
    {
        /**
         * @var EpsApiMyApi2WebModelsCustomerAddressModel $data
         */
        $collectionAddress = new CollectionAddressModel();
        $collectionAddress->setCity($data->getCity());
        $collectionAddress->setStreet($data->getStreet());
        $collectionAddress->setCountry($data->getCountry());
        $collectionAddress->setZip($data->getZipCode());
        $collectionAddress->setName(trim($data->getName() . ' ' . $data->getName2()));
        $collectionAddress->setCode(trim($data->getCode()));
        $collectionAddress->setDefault($data->getDefault());

        return $collectionAddress;
    }

    private function CartToAddressModel(Cart $cart)
    {

        $session = $this->registry->get("session");
        $findedAddress = null;
        // 2) Session: shipping_address (checkout vyplněná adresa)
        if (!empty($session->data['shipping_address'])) {
            $findedAddress = $session->data['shipping_address'];
        }

        // 3) Guest: guest->shipping
        if (!$findedAddress && !empty($session->data['guest']['shipping'])) {
            $findedAddress = $session->data['guest']['shipping'];
        }

        $customer = $this->registry->get("customer");

        // 4) Přihlášený: default adresa
        if (!$findedAddress && $customer->isLogged() && $customer->getAddressId()) {
            $addrs = $this->loadModel("account/address");
            $findedAddress = $addrs->getAddress($customer->getAddressId());
        }

        if (!$findedAddress)
            return null;


        $localization = $this->loadModel("localisation/country");
        $zone = $this->loadModel("localisation/zone");

        $country = !empty($findedAddress['country_id'])
            ? $localization->getCountry((int)$findedAddress['country_id'])
            : null;

        $zone = !empty($findedAddress['zone_id'])
            ? $zone->getZone((int)$findedAddress['zone_id'])
            : null;

        $findedAddress = [
            'firstname'        => $findedAddress['firstname'] ?? '',
            'lastname'         => $findedAddress['lastname'] ?? '',
            'company'          => $findedAddress['company'] ?? '',
            'address_1'        => $findedAddress['address_1'] ?? '',
            'address_2'        => $findedAddress['address_2'] ?? '',
            'postcode'         => $findedAddress['postcode'] ?? '',
            'city'             => $findedAddress['city'] ?? '',
            'country_id'       => (int)($findedAddress['country_id'] ?? 0),
            'country'          => $country['name'] ?? ($findedAddress['country'] ?? ''),
            'iso_code_2'       => $country['iso_code_2'] ?? '',
            'iso_code_3'       => $country['iso_code_3'] ?? '',
            'address_format'   => $country['address_format'] ?? '',
            'postcode_required'=> isset($country['postcode_required']) ? (bool)$country['postcode_required'] : false,
            'zone_id'          => (int)($findedAddress['zone_id'] ?? 0),
            'zone'             => $zone['name'] ?? ($findedAddress['zone'] ?? ''),
            'zone_code'        => $zone['code'] ?? '',
            'custom_field'     => $addr['custom_field'] ?? [],
            'address_id'       => $addr['address_id'] ?? null,
            'phone' => $addr['phone'] ?? null,
        ];

        $addressData = new AddressData($this->registry);
        $addressData->name = $findedAddress['firstname'] . ' ' . $findedAddress['lastname'];
        $addressData->city = $findedAddress['city'];
        $addressData->street = $findedAddress['address_1'] . ' ' . $findedAddress['address_2'];
        $addressData->zip = $findedAddress['postcode'];
        $addressData->country = $findedAddress['iso_code_2'];
        $addressData->phone =  $findedAddress['phone'];

        return $addressData;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof AddressData && in_array($type, [ RecipientAddressModel::class, SenderAddressModel::class], true)
            || ($data instanceof RecipientAddressModel || $data instanceof  SenderAddressModel) && $type === AddressData::class
            || $type === RecipientAddressModel::class && $data instanceof OrderProxy
            || $type === CollectionAddressModel::class && $data instanceof EpsApiMyApi2WebModelsCustomerAddressModel
            || $data instanceof Cart && $type === AddressData::class
            ;
    }
}
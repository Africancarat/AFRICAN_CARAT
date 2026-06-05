<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Session;
use Shippo;
use Shippo_Address;
use Shippo_CustomsDeclaration;
use Shippo_Shipment;
use Shippo_Transaction;

class Shipping
{
    public function __construct()
    {
        // Grab this private key from
        // .env and setup the Shippo api
        Shippo::setApiKey(env('SHIPPO_PRIVATE'));
    }


    public function fromAddress()
    {
        $fromAddress = [
            'object_purpose' => 'PURCHASE',
            'name' => 'Shawn Ippotle',
            'company' => 'Shippo',
            'street1' => '215 Clayton St.',
            'city' => 'San Francisco',
            'state' => 'CA',
            'zip' => '94117',
            'country' => 'US',
            'phone' => '+1 555 341 9393',
            'email' => 'shippotle@goshippo.com'
        ];
        return $fromAddress;
    }


    /**
     * Validate an address through Shippo service
     * @return Shippo_Adress
     */
    public function validateAddress()
    {
        // Grab the shipping address from the User model
        $toAddress = $this->shippingAddress();
        // Pass a validate flag to Shippo
        $toAddress['validate'] = true;
        // Verify the address data
        return Shippo_Address::create($toAddress);
    }

    public function shippingAddress()
    {
        $billing = Session::get('billing_address');
        return [
            'name' => $billing['bill_first_name'] . ' ' . $billing['bill_last_name'],
            'company' => $billing['bill_company'],
            'street1' => $billing['bill_city'],
            'city' => $billing['bill_city'],
            'zip' => $billing['bill_zip'],
            //'country' => $billing['bill_country'],
            'country' => 'Bangladesh',
            'phone' => $billing['bill_phone'],
            'email' => $billing['bill_email'],
        ];
    }


 

    /**
     * Create a Shippo shipping rates
     *
     * @param User $user
     * @param Product $product
     * @return Shippo_Shipment
     */
    public function rates()
    {
        $parcel = [
            'length' => '5',
            'width' => '5',
            'height' => '5',
            'distance_unit' => 'in',
            'weight' => '2',
            'mass_unit' => 'lb',
        ];

       
            $customs_item = array(
                'description'=> 'T-Shirt',
                'quantity'=> '20',
                'net_weight'=> '1',
                'mass_unit'=> 'lb',
                'value_amount'=> '200',
                'value_currency'=> 'USD',
                'origin_country'=> 'US');
            
            $customs_declaration = Shippo_CustomsDeclaration::create(
            array(
                'contents_type'=> 'MERCHANDISE',
                'contents_explanation'=> 'T-Shirt purchase',
                'non_delivery_option'=> 'RETURN',
                'certify'=> 'true',
                'certify_signer'=> 'Simon Kreuz',
                'items'=> array($customs_item)
            ));
        
            $objid = json_decode($customs_declaration,true)['object_id'];
           
        // Grab the shipping address from the User model
        $toAddress = $this->shippingAddress();
        // Pass the PURCHASE flag.
        $toAddress['object_purpose'] = 'PURCHASE';
       
        // Get the shipment object
        $ok = Shippo_Shipment::create([
            'object_purpose' => 'PURCHASE',
            'address_from' => $this->fromAddress(),
            'address_to' => $toAddress,
            'parcels' => $parcel,
            'customs_declaration' => $objid,
            'insurance_amount' => '30',
            'insurance_currency' => 'USD',
            'async' => false
        ]);
        return $ok;
    }


    /**
     * Create the shipping label transaction
     *
     * @param $rateId -- object_id from rates_list
     * @return Shippo_Transaction
     */
    public function createLabel($rateId)
    {
        return Shippo_Transaction::create([
            'rate' => $rateId,
            'label_file_type' => "PDF",
            'async' => false
        ]);
    }
}

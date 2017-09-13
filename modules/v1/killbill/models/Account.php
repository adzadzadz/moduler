<?php 

namespace app\modules\v1\killbill\models;

class Account extends \yii\base\Model
{
    private $tenant;
    private $accountData;
    private $externalAccountId;

    public $key;
    public $secret;
    public $serverUrl;
    public $name;
    public $externalKey;
    public $email;
    public $currency;
    public $paymentMethodId;
    public $address1;
    public $address2;
    public $city;
    public $company;
    public $state;
    public $country;
    public $phone;
    public $postalCode;
    public $firstNameLength;
    public $locale;
    public $billCycleDay;
    public $timeZone;

    public function init()
    {
        parent::init();

        // Killbill library
        require_once( dirname(dirname(__FILE__)) . '/lib/killbill-client-php/lib/killbill.php' );
        
        // Set server url
        \Killbill_Client::$serverUrl = $this->serverUrl;

        // Preparing api key and api secret
        $this->tenant = new \Killbill_Tenant();
        $this->tenant->apiKey = $this->key;
        $this->tenant->apiSecret = $this->secret;

        // Account Properties
        $this->accountData = new \Killbill_Account();
    }

    public function rules()
    {
        return [
            [['name', 'externalKey', 'email', 'currency', 'company', 'phone'], 'required'],
            [['name', 'externalKey', 'email', 'currency', 'timeZone', 'address1', 'address2', 'postalCode', 'company', 'city', 'state', 'country', 'locale', 'phone'], 'string', 'max' => 255],
        ];
    }

    public function get()
    {   
        if ($this->validate(['externalKey'])) {
            $this->accountData->externalKey = $this->externalKey;
            return Formatter::get($this->accountData->get($this->tenant->getTenantHeaders()), [
                'name',
                'email',
                'firstNameLength'
            ]);
        }
        return $this->getErrors();
    }

    public function create($name, $reason, $comment = '')
    {
        if ($this->validate()) {
            $accountData = new \Killbill_Account();
            $accountData->name = $this->name;
            $accountData->firstNameLength = $this->firstNameLength;
            $accountData->externalKey = $this->externalKey !== null ? $this->externalKey: uniqid();
            $accountData->email = $this->email;
            $accountData->currency = $this->currency;
            $accountData->address1 = $this->address1;
            $accountData->address2 = $this->address2;
            $accountData->city = $this->city;
            $accountData->company = $this->company;
            $accountData->state = $this->state;
            $accountData->postalCode = $this->postalCode;
            $accountData->country = $this->country;
            $accountData->phone = $this->phone;
            $accountData->billCycleDay = $this->billCycleDay;
            $accountData->timeZone = $this->timeZone;

            return $accountData->create($name, $reason, $comment, $this->tenant->getTenantHeaders());
        }
        return $this->getErrors();
        
    }

}
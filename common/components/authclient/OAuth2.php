<?php 

namespace common\components\authclient;

use yii\authclient\OAuth2 as YiiOAuth2;

class OAuth2 extends YiiOAuth2
{
    public $clientId = 'strepzId';
    public $clientSecret = 'strepzSecret';

    protected function initUserAttributes()
    {
        
    }
}
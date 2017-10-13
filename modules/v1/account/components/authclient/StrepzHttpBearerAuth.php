<?php 

namespace api\modules\v1\account\components\authclient;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use api\modules\v1\account\models\GlbUserToken;

class StrepzHttpBearerAuth extends HttpBearerAuth
{
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
        	// Set all user config necessary for the application
        	GlbUserToken::setStrepzConfig($matches[1]);
            $identity = $user->loginByAccessToken($matches[1], get_class($this));
            if ($identity === null) {
                $this->handleFailure($response);
            }
            return $identity;
        }
        return null;
    }
}
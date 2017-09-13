<?php

namespace frontend\models\auth;

use Yii;
use yii\base\Model;
use common\models\GlbUser;
use common\models\LoginForm;
use frontend\models\SignupForm;

class Clients extends Model
{
    public $client;
    public $authAttrs;
    public $auth;

    public function init()
    {
        parent::init();

        $this->authAttrs = $this->client->getUserAttributes();

        $this->auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $this->authAttrs['id'],
        ])->one();
    }

    public function googleSyncAcct()
    {
        if (!$this->auth) { // add auth provider
            $auth = new Auth([
                'user_id' => Yii::$app->user->id,
                'source' => $this->client->getId(),
                'source_id' => $this->authAttrs['id'],
            ]);
            $auth->save();
        }
    }

    private function googleSignup()
    {
        if (isset($this->authAttrs['emails'][0]['value']) && GlbUser::find()->where(['username' => $this->authAttrs['emails'][0]['value']])->exists()) {
            Yii::$app->getSession()->setFlash('error', [
                Yii::t('app', "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.", [
                    'client' => $this->client->getTitle()
                ]),
            ]);
        } else {
            $password = Yii::$app->security->generateRandomString(8);

            $signup = new SignupForm;

            $signup->username = $this->authAttrs['emails'][0]['value'];
            $signup->email = $this->authAttrs['emails'][0]['value'];
            $signup->password = $password;
            $signup->confirmpassword = $password;

            // Yii::info($signup->tmpSignup(), 'authGoogle');
            if ($user = $signup->tmpSignup()) {
                $auth = new Auth([
                    'user_id' => $user->id,
                    'username' => $this->authAttrs['emails'][0]['value'],
                    'source' => $this->client->getId(),
                    'source_id' => (string)$this->authAttrs['id'],
                ]);
                if ($auth->save()) {
                    $autoLogin = new LoginForm();
                    if ($autoLogin->registrationTmpUserAutoLogin($user->username) && $this->setUserMeta()) {}
                } else {
                    foreach ($auth->getErrors() as $each) {
                        foreach ($each as $error) {
                            Yii::$app->session->setFlash('error', $error);
                        }
                    }
                }
            } else {
                foreach ($signup->getErrors() as $each) {
                    foreach ($each as $error) {
                        Yii::$app->session->setFlash('error', $error);
                    }
                }
            }
        }
        return false;
    }

    public function googleLogin()
    {
        if ($this->auth) {
            // login
            $user = GlbUser::findOne(['username' => $this->auth->username]);
            $login = new LoginForm();
            $login->registrationTmpUserAutoLogin($user->username);
            if ($this->setUserMeta()) {}
        } else {
            // signup
            return $this->googleSignup();
        }
    }

    /**
     * Tested with: Google only
     */
    private function setUserMeta()
    {
        // Simply add the size in pixels after the variable
        $image = str_replace("sz=50", "sz=", $this->authAttrs['image']['url']);
        $userMeta = [
            'auth' => true,
            'image' => isset($image) ? $image : " ",
            'displayName' => isset($this->authAttrs['displayName']) ? $this->authAttrs['displayName'] : " ",
            'givenName' => isset($this->authAttrs['name']['givenName']) ? $this->authAttrs['name']['givenName'] : " ",
            'familyName' => isset($this->authAttrs['name']['familyName']) ? $this->authAttrs['name']['familyName'] : " ",
            'gender' => isset($this->authAttrs['gender']) ? $this->authAttrs['gender'] : " ",
        ];
        // Saves the profile picture url
        Yii::$app->session->set('userMeta', $userMeta);
        return true;
    }

    public function facebookLogin()
    {
        echo "lelx";
    }
}
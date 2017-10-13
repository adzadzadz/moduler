<?php 

namespace api\modules\v1\account\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use common\components\authclient\StrepzHttpBearerAuth;
use api\modules\v1\account\models\LoginForm;
use api\modules\v1\account\models\SignupForm;
use api\modules\v1\account\models\FncSignupForm;
use api\modules\v1\account\models\GlbUser;
use api\modules\v1\account\models\TmpUser;
use api\modules\v1\account\models\PasswordResetRequestForm;
use api\modules\v1\account\models\ResetPasswordForm;

/**
* Handles actions that are accessed directly from the browser.
* Typically in used with email verifications and such.
* REST services are also available for all actions provided in this controller
*/
class SiteController extends \yii\web\Controller
{
	/**
	 * Builds user data to the functional databases.
	 * This simply means that the user is verified and can now use the app's features
	 * @param  string $id     currently only accepts an email address
	 * @param  string $token  registration token
	 * @param  string $method only accepts the string 'email'
	 */
    public function actionBuild($id = null, $token = null, $method = null)
    {
        // Avoiding execution time error. 
        set_time_limit ( 360 );

        if (Yii::$app->user->isGuest || Yii::$app->user->identity->status < 6) {
            if ($id === null || $token === null) {
                throw new ForbiddenHttpException('You are not allowed to perform this action.');
            }
        } else {
            $id = Yii::$app->user->identity->username;
        }

        $currentRegion = strtolower(Yii::$app->params['app_region']);
        $glbUser = GlbUser::getUserData($id);
        $glbUser =  $glbUser['company'];
        Yii::$app->config->setCompanyId($glbUser->company_id);

        if ($currentRegion === $glbUser->region) {
            $tmpUser = new TmpUser;
            
            if (Yii::$app->user->isGuest && $token !== null) {
                $user = $tmpUser->findUserByRegistrationToken($id, $token);
            } else {
                $user = TmpUser::findOne(Yii::$app->user->id);
            }

            if ($user) {
                $signup = new SignupForm;
                if ($setUser = $signup->signup($user->id)) {
                    Yii::$app->config->setIsTempUser($setUser->status);

                    if ($method == 'email') {
                        return $this->redirect('/');
                    } else {
                        return true;
                    }
                }
            }
        } else {
            // REDIRECTION URL WITH ID AND TOKEN AS PARAMS if region does not match
            $token = Yii::$app->user->identity->_registration_token;
            $this->redirect(Yii::$app->params[$glbUser->region . '_domain'] . Url::to(['registration/finalize', 'id' => $id, 'token' => $token]));
        }
        throw new ForbiddenHttpException('You are not allowed to perform this action.');
    }

}
<?php
namespace api\components;

use Yii;
use yii\base\Object;
use common\models\FncConfig;
use common\models\FncUser;
// use yii\web\Controller;

/**
 * Will need proper implementation of specific user location sharing
 */
class StrepzConfigManager extends Object
{
    public $company_id;
    public $isTempUser = false;
    public $userConfig;
    public $selectedProject;

    // Loads all configurations required including custom user settings
    public function loadAllConfig()
    {   
        $this->setLanguage();
        // User Config
        $this->getUserDefault();
    }

    // Required for login. :O
    public function reloadCompanyId()
    {
        if (!is_null(Yii::$app->session->get('company_id'))) {
            $this->company_id = Yii::$app->session->get('company_id');
            return $this->company_id;
        }
        return false;
    }

    public function setCompanyId($company_id = null)
    {
        $this->company_id = $company_id;
    }

    /**
     * Sets the user's status
     * Triggered every account revalidation
     */
    public function setIsTempUser($status = null)
    {
        $this->isTempUser = $status == FncUser::STATUS_ACTIVE ? false : true;
        if ($this->isTempUser) {
            Yii::$app->user->identityClass = 'common\models\TmpUser';
        } else {
            Yii::$app->user->identityClass = 'common\models\FncUser';
        }
    }

    /**
     * @method checkIfUserIsTemp checks if user|guest requires email verification
     * DEPRECATED!!!
     */
    public function checkIfUserIsTemp()
    {
        if (Yii::$app->session->get('user_temp_mode') === true) {
            Yii::$app->user->identityClass = 'common\models\TmpUser';
        }
        if (Yii::$app->session->get('user_temp_mode') === true && !Yii::$app->user->isGuest) {
            // Working as needed but must be reviewed before production
            // return Controller::redirect(['/verification']);
            $this->isTempUser = true;
        }
        return $this->isTempUser;
    }

    public function setLanguage()
    {
        if (Yii::$app->request->cookies->get('language') !== null) {
            Yii::$app->language = Yii::$app->request->cookies->getValue('language');
        }
    }

    /**
     * No longer required since polymer is now pulling translations from the generated files (generated using the backend tool)
     */
    public function setLanguageObject()
    {
        Yii::$app->getI18n()->translations['*'] = [
            'class' => 'yii\i18n\DbMessageSource',
            'db' => !Yii::$app->user->isGuest && Yii::$app->session->get('user_temp_mode') !== true ? str_replace("fnc_db_", "fnc_lng_db_", Yii::$app->session->get('fnc_db')) : 'glb_lng_db_01', //Yii::$app->session->get('fnc_db_id', 'glb_sys_db_01'),
            'sourceMessageTable' => '{{%language_source}}',
            'messageTable' => '{{%language_message}}',
            // 'sourceLanguage' => 'en-EN',
            // 'enableCaching' => true,
        ];
    }

    private function getUserDefault()
    {
        if (!Yii::$app->user->isGuest) {
            if (!$this->isTempUser) {
                $config = FncConfig::findOne(['user_id' => Yii::$app->user->id, 'type' => 'userDefaults', 'name' => 'language']);
                Yii::$app->language = $config->value;
                // Selected Project assignment
                $project = FncConfig::getSelectedProject();
                if ($project) {
                    $this->selectedProject = !is_null($project) ? $project->id : null;
                }
            } else {
                Yii::$app->language = Yii::$app->user->identity->language;
            }
        } else {
            // Probably set site language based on area.
            // Or create a language selector tool or whatever.
            // Stick to en-US for now as the guest default.
        }
    }

}
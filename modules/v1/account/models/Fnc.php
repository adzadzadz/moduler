<?php

namespace api\modules\v1\account\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Command;
use yii\db\Schema;


class Fnc extends ActiveRecord
{
    /**
     * Purpose is to simplify loading of database tables during registration
     * @return Boolean
     */ 
    public static function loadTables($company_id)
    {
        $company = new FncCompany();
        $config  = new FncConfig();
        $logs    = new FncLogs();
        $rbac    = new FncRbac();
        $user    = new FncUser();
        $userMeta = new FncUserMeta();
        // $project = new FncProject();

        if ( $logs->initTables() && 
             $company->initTables() && 
             $user->initTables() &&
             $userMeta->initTables() &&
             $rbac->initTables() &&
             $config->initTables()) {
             // $project->initProjectTables()

            return true;
        }
        return false;
    }
}
<?php 

namespace api\modules\v1\account\rest;

use Yii;

/**
 * Default actually means the default controller of the module
 * DefaultUrlRule does not imply that the rules are initially applied to all controllers
 */
class AuthUrlRule extends \yii\rest\UrlRule
{
	public $patterns = [
        // 'PUT,PATCH {id}' => 'update',
        // 'DELETE {id}' => 'delete',
        // 'GET,HEAD {id}' => 'view',
        'GET {theo}' => 'theo',
        'GET,HEAD' => 'csrf',
        'POST' => 'login',
        // 'POST new' => 'signup',
        // 'GET,HEAD' => 'index',
        // '{id}' => 'options',
        // '' => 'options',
    ];
}
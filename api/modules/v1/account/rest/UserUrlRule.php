<?php 

namespace api\modules\v1\account\rest;

use Yii;

/**
 * Default actually means the default controller of the module
 * DefaultUrlRule does not imply that the rules are initially applied to all controllers
 */
class UserUrlRule extends \yii\rest\UrlRule
{
	public $patterns = [
        // 'PUT,PATCH {id}' => 'update',
        // 'DELETE {id}' => 'delete',
        // 'GET,HEAD {id}' => 'view',
        'GET me' => 'view',
        // 'GET {theo}' => 'theo',
        // 'POST' => 'login',
        // 'POST new' => 'signup',
        // 'GET,HEAD' => 'index',
        'me' => 'options',
        '{id}' => 'options',
        '' => 'options',
    ];
}
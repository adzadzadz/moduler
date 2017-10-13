<?php 

namespace api\modules\v1\account\rest;

use Yii;

/**
 * Default actually means the default controller of the module
 * DefaultUrlRule does not imply that the rules are initially applied to all controllers
 */
class DefaultUrlRule extends \yii\rest\UrlRule
{
	/**
	 * @inheritdoc
	 */
	public $tokens = [
		'{id}' => '<id:\\d[\\d,]*>',
        '{email}' => '<email:[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}>',
        '{token}' => '<token:[A-Za-z0-9_-]+>'
    ];

    /**
	 * @inheritdoc
	 */
	public $patterns = [
        // 'PUT,PATCH ' => 'build',
        'PUT,PATCH {email}' => 'verify',
        // 'DELETE {id}' => 'delete',
        // 'GET,HEAD {id}' => 'view',
        'POST ' => 'signup',
        // 'PUT,PATCH {email}/{token}/email' => 'build',

        'GET password-reset/{token}/{email}' => 'verify-reset-token', 
        'POST password-reset' => 'request-password-reset', 
        'PUT,PATCH password-reset' => 'reset-password', 
        // 'GET,HEAD' => 'index',
        'password-reset' => 'options',
        '{id}' => 'options',
        '' => 'options',
    ];
}
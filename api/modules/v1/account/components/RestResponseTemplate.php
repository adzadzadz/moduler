<?php 
namespace api\modules\v1\account\components;

use Yii;
use yii\base\Object;

class RestResponseTemplate extends Object
{
	public function success($result = null)
	{
		return [
			'timeStamp' => null,
			'lang' => 'en',
			'result' => $result,
			'status' => [
				'code' => 200,
				'errorType' => 'success'
			]
		];
	}

	public function fail($result = null, $errorCode, $errorType)
	{
		return [
			'timeStamp' => null,
			'lang' => 'en',
			'result' => $result,
			'status' => [
				'code' => $errorCode,
				'errorType' => $errorType
			]
		];
	}
}
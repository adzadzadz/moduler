# Strepz Front-end platform

## Core file changes
* /vendor/yiisoft/yii2-bootstrap/BootstrapPluginAsset.php
	* line 22 disabled
	* Reason: Conflicting javascript from main asset

* /vendor/yiisoft/yii2-bootstrap/BootstrapAsset.php
	* line 22 disabled
	* Reason: Conflicting css from main asset

* /vendor/yii2-gii/GiiAsset.php
	* line 32 asset added
	* Reason: Asset conflict fix resulted to gii asset bug. Fixed by adding the strepz main asset
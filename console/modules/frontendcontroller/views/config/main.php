<?= '<?php ' ?>
return [
	'id' => '<?= $environment_id ?>',
	'components' => [
		<?php foreach ($connections as $each): ?>
			<?php foreach ($dbs as $eachDb): ?>
				<?php

					/**
					 * if db name has "_sys_"
					 */
					$theName = '';
					if (strpos($eachDb->db_id,'glb_') !== false) {
						if (strpos($eachDb->db_id,'_sys_') !== false) {
							// Set the naming convention
							$theName = $dbNaming['glb']['_sys_'];
						} elseif (strpos($eachDb->db_id,'_reg_') !== false) {
							// Set the naming convention
							$theName = $dbNaming['glb']['_reg_'];
						} elseif (strpos($eachDb->db_id,'_cnf_') !== false) {
							// Set the naming convention
							$theName = $dbNaming['glb']['_cnf_'];
						}
					}

					if (strpos($eachDb->db_id,'fnc_') !== false) {
						if (strpos($eachDb->db_id,'_cnf_') !== false) {
							// Set the naming convention
							$theName = $dbNaming['fnc']['_cnf_'];
						} else {
							$theName = $dbNaming['fnc']['default'];
						}
					}
					
					$db_id = $theName . substr($eachDb->db_id, -2);
				?>

			"<?= $db_id ?>" => [
			        "class" => "yii\db\Connection",
			        "dsn" => "mysql:<?= CONNECTOR ?>=<?= Yii::getAlias("@dsn_con") . $each->name ?>;dbname=<?= $eachDb->db_id ?>",
			        "username" => "root",
			        "password" => "<?= $each->password ?>",
			        "tablePrefix" => "<?= $eachDb->tbl_prefix ?>",
			        "charset" => "utf8",
			    ],
			<?php endforeach ?>
		<?php endforeach ?>	
	],
];
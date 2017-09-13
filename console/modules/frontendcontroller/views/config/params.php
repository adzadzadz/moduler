<?php echo "<?php " ;

/**
 *	Sample: b1-42-login.eu.strepz.com
 *	{{build_no}}-{{alias}}.region.domain
 */

?>

<?php if (!empty($envData)): ?>
return [
	'app_region' => "<?= $envData['region'] ?>",
	'<?= $envData['region'] ?>_domain'  => 'b<?= $envData['config_id'] ?>-<?= $envData['build_no'] ?>-www.<?= $envData['region'] ?>.<?= $domain ?>',
];
<?php endif ?>


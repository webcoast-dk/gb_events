<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Main',
	array(
		'Event' => 'list, show, new, create, edit, update, delete',
		
	),
	array(
		'Event' => 'create, update, delete',
		
	)
);

?>
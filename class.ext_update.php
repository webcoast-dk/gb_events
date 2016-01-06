<?php
/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Update class for the extension manager.
 *
 */
class ext_update
{
    /**
     * Version of upgrade wizard
     */
    const UPGRADE_WIZARD_VERSION = 20160106;

    /**
     * Array of flash messages (params) array[][status,title,message]
     *
     * @var array
     */
    protected $messageArray = [];

    /**
     * Main update function called by the extension manager.
     *
     * @return string
     */
    public function main()
    {
        $this->processUpdates();

        return $this->generateOutput();
    }

    /**
     * Called by the extension manager to determine if the update menu entry
     * should by shown.
     *
     * @return bool
     */
    public function access()
    {
        $systemRegistry = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Registry::class);

        return (int)$systemRegistry->get('gb_events', 'upgradeWizardVersion') < static::UPGRADE_WIZARD_VERSION;
    }

    /**
     * The actual update function. Add your update task in here.
     *
     * @return void
     */
    protected function processUpdates()
    {
        $this->migrateSwitchableControllerActions();
        $this->markWizardDone();
    }

    /**
     * Updates the FlexForm configuration (removes switchableControllerActions, sets displayMode and pluginType)
     *
     * @return void
     */
    protected function migrateSwitchableControllerActions()
    {
        $title = 'Migrating output mode for gb_events plugin:';

        $res = $this->getDatabaseConnection()->exec_SELECTquery(
            'uid,list_type,pi_flexform',
            'tt_content',
            'CType=\'list\' AND list_type=\'gbevents_main\''
        );

        /** @var \TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools $flexformTools */
        $flexformTools = GeneralUtility::makeInstance(TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class);

        while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($res)) {
            $xmlArray = GeneralUtility::xml2array($row['pi_flexform']);

            if (!is_array($xmlArray) || !isset($xmlArray['data'])) {
                $status = FlashMessage::ERROR;
                $message = 'Flexform data of record tt_content:' . $row['uid'] . ' not found';
            } elseif (
                !isset($xmlArray['data']['sDEF']['lDEF']['switchableControllerActions'])
                || !is_array($xmlArray['data']['sDEF']['lDEF']['switchableControllerActions'])
            ) {
                $status = FlashMessage::WARNING;
                $message = 'Flexform data of record tt_content:' . $row['uid']
                    . ' did not contain switchableControllerActions';
            } else {
                $updated = true;
                $listType = 'gbevents_main';

                foreach ($xmlArray['data']['sDEF']['lDEF']['switchableControllerActions'] as $language => $actions) {
                    $primaryAction = array_shift(GeneralUtility::trimExplode(';', html_entity_decode($actions), 2));
                    switch ($primaryAction) {
                        case 'Event->show':
                            // Intentional fall through
                        case 'Event->list':
                            $xmlArray['data']['sDEF']['lDEF']['settings.displayMode'] = ['vDEF' => 'list'];
                            break;
                        case 'Event->upcoming':
                            $listType = 'gbevents_upcoming';
                            break;
                        case 'Event->calendar':
                            $xmlArray['data']['sDEF']['lDEF']['settings.displayMode'] = ['vDEF' => 'calendar'];
                            break;
                        default:
                            $updated = false;
                    }
                }

                if ($updated === true) {
                    unset($xmlArray['data']['sDEF']['lDEF']['switchableControllerActions']);
                    $this->getDatabaseConnection()->exec_UPDATEquery(
                        'tt_content',
                        'uid=' . $row['uid'],
                        [
                            'pi_flexform' => $flexformTools->flexArray2Xml($xmlArray),
                            'list_type' => $listType,
                        ]
                    );
                    $message = 'Flexform data of record tt_content:' . $row['uid']
                        . ' has been migrated';
                    $status = FlashMessage::OK;
                } else {
                    $status = FlashMessage::NOTICE;
                    $message = 'Flexform data of record tt_content:' . $row['uid']
                        . ' did not contain a known default action';
                }
            }

            $this->messageArray[] = [$status, $title, $message];
        }
    }

    /**
     * Generates output by using flash messages
     *
     * @return string
     */
    protected function generateOutput()
    {
        $output = '';
        foreach ($this->messageArray as $messageItem) {
            /** @var \TYPO3\CMS\Core\Messaging\FlashMessage $flashMessage */
            $flashMessage = GeneralUtility::makeInstance(
                TYPO3\CMS\Core\Messaging\FlashMessage::class,
                $messageItem[2],
                $messageItem[1],
                $messageItem[0]);
            $output .= $flashMessage->render();
        }

        return $output;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Marks this wizard as completed
     */
    protected function markWizardDone()
    {
        $systemRegistry = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Registry::class);
        $systemRegistry->set('gb_events', 'upgradeWizardVersion', static::UPGRADE_WIZARD_VERSION);
    }
}

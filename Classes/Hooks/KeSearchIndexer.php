<?php
namespace GuteBotschafter\GbEvents\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Morton Jonuschat <m.jonuschat@gute-botschafter.de>, Gute Botschafter GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


/**
 *
 * @package gb_events
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */

class KeSearchIndexer {
  /**
   * Pages to index
   * @var \array
   */
  protected $indexPids = '';
  /**
   * Array containing data of all pages
   * @var \array
   */
  protected $pageRecords;
  /**
   * Status information for user
   * @var \string
   */
  protected $content = '';
  /**
   * The indexer object
   * TODO: Find correct type
   */
  protected $indexerObject;
  /**
   * The indexer configuration
   * @var \array
   */
  protected $indexerConfig;

  /**
   * Custom index for ke_search to index content provided
   * by the extension gb_events
   *
   * @param   \array $indexerConfig
   * @param   \array $indexerObject
   * @return  \string $output
   * @author  Morton Jonuschat <mj@gute-botschafter.de>
   */
  public function customIndexer(&$indexerConfig, &$indexerObject) {
    # Set the passed indexer configuration as default
    $this->indexerConfig = $indexerConfig;
    $this->indexerObject = $indexerObject;

    $this->content = '';
  }

  /**
   * Add Tags to pages array
   * @author  Andreas Kiefer (kennziffer.com) <kiefer@kennziffer.com>
   * @author  Stefan Froemken (kennziffer.com) <froemken@kennziffer.com>
   * @author  Morton Jonuschat <yabawock@gmail.com>
   * @package tx_kesearch
   * @return void
   */
  protected function addTagsToPageRecords() {
    $tagChar = $this->indexerObject->extConf['prePostTagChar'];
    # add tags which are defined by page properties
    $fields = 'pages.*, GROUP_CONCAT(CONCAT("' . $tagChar . '", tx_kesearch_filteroptions.tag, "' . $tagChar . '")) as tags';
    $table = 'pages, tx_kesearch_filteroptions';
    $where = 'pages.uid IN (' . $this->indexPids . ')';
    $where .= ' AND pages.tx_kesearch_tags <> "" ';
    $where .= ' AND FIND_IN_SET(tx_kesearch_filteroptions.uid, pages.tx_kesearch_tags)';
    $where .= \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('tx_kesearch_filteroptions');
    $where .= \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('tx_kesearch_filteroptions');

    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where, 'pages.uid', '', '');
    while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
      $this->pageRecords[$row['uid']]['tags'] = $row['tags'];
    }

    # add tags which are defined by filteroption records
    $fields = 'automated_tagging, tag';
    $table = 'tx_kesearch_filteroptions';
    $where = 'automated_tagging <> "" ';
    $where .= \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('tx_kesearch_filteroptions');
    $where .= \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('tx_kesearch_filteroptions');

    $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($fields, $table, $where);

    # index only pages of doktype standard, advanced and "not in menu"
    $where = ' (doktype = 1 OR doktype = 2 OR doktype = 5) ';
    # index only pages which are searchable
    $where .= ' AND no_search <> 1 ';

    foreach($rows as $row) {
      $tempTags = array();
      $pageList = \TYPO3\CMS\Extbase\Utility\ArrayUtility::trimExplode(',', $this->queryGen->getTreeList($row['automated_tagging'], 99, 0, $where));
      foreach($pageList as $uid) {
        if($this->pageRecords[$uid]['tags']) {
          $this->pageRecords[$uid]['tags'] .= ',' . $tagChar . $row['tag'] . $tagChar;
        } else {
          $this->pageRecords[$uid]['tags'] = $tagChar . $row['tag'] . $tagChar;
        }
      }
    }
  }
}

<?php
namespace GuteBotschafter\GbEvents\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2015 Morton Jonuschat <m.jonuschat@gute-botschafter.de>, Gute Botschafter GmbH
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

use GuteBotschafter\GbEvents\Domain\Repository\EventRepository;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * ArchiveController
 */
abstract class BaseController extends ActionController
{
    /**
     * @var \GuteBotschafter\GbEvents\Domain\Repository\EventRepository
     */
    protected $eventRepository;

    /**
     * @var DataMapper
     */
    protected $dataMapper;

    /**
     * inject the eventRepository
     *
     * @param \GuteBotschafter\GbEvents\Domain\Repository\EventRepository eventRepository
     * @return void
     */
    public function injectEventRepository(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Dynamically add the right tags to the page cache for a details or list view
     *
     * @param mixed $items
     * @param string|array $additionalTags
     */
    protected function addCacheTags($items, $additionalTags = null)
    {
        if (TYPO3_MODE === 'BE') {
            return;
        }

        if (!is_array($items) && !$items instanceof \Traversable && !$items instanceof \ArrayAccess) {
            $items = [$items];
        }
        if (!is_array($additionalTags)) {
            $additionalTags = [(string)$additionalTags];
        }

        $tags = $additionalTags;
        foreach ($items as $item) {
            if ($item instanceof AbstractEntity) {
                $table = $this->getDataMapper()->convertClassNameToTableName(get_class($item));
                $uid = $item->getUid();
                $tags[] = sprintf('%s_%s', $table, $uid);
            } elseif ((string)$item !== '') {
                $tags[] = (string)$item;
            }
        }

        if (!empty($tags)) {
            $this->getTypoScriptFrontEndController()->addCacheTags($tags);
        }
    }

    /**
     * @return DataMapper
     */
    protected function getDataMapper()
    {
        if (!isset($this->dataMapper)) {
            $this->dataMapper = $this->objectManager->get(DataMapper::class);
        }

        return $this->dataMapper;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontEndController()
    {
        return $GLOBALS['TSFE'];
    }
}

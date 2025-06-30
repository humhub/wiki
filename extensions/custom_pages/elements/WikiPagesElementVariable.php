<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\extensions\custom_pages\elements;

use humhub\modules\custom_pages\modules\template\elements\BaseElementVariableIterator;

class WikiPagesElementVariable extends BaseElementVariableIterator
{
    public function __construct(WikiPagesElement $elementContent)
    {
        parent::__construct($elementContent);

        foreach ($elementContent->getItems() as $wikiPage) {
            $this->items[] = WikiPageElementVariable::instance($elementContent)->setRecord($wikiPage);
        }
    }
}

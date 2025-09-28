<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\components\Widget;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\permissions\CreatePage;

class WikiListHeader extends Widget
{
    /**
     * @var string
     */
    public $icon;

    /**
     * @var string
     */
    public $title;

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('wikiListHeader', [
            'icon' => $this->icon,
            'title' => $this->title,
            'contentContainer' => $this->contentContainer,
            'canCreate' => $this->contentContainer->permissionManager->can(CreatePage::class),
        ]);
    }
}

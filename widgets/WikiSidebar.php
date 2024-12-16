<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\JsWidget;

class WikiSidebar extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'wiki.Sidebar';

    /**
     * @inheritdoc
     */
    public $init = true;

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @var bool
     */
    public $hideOnSmallScreen;

    /**
     * @var string
     */
    public $resizableCacheKey = 'wiki.sidebar';

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('wikiSidebar', [
            'options' => $this->getOptions(),
            'contentContainer' => $this->contentContainer,
            'canCreate' => (new WikiPage($this->contentContainer))->content->canEdit(),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getAttributes()
    {
        return [
            'class' => 'wiki-page-sidebar col-lg-4 ' . ($this->hideOnSmallScreen ? 'visible-lg' : 'col-md-12'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getData()
    {
        return [
            'container-id' => $this->contentContainer->id,
            'resizable-key' => $this->resizableCacheKey,
        ];
    }
}

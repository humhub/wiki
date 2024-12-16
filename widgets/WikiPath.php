<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\components\Widget;
use humhub\modules\wiki\models\WikiPage;
use Yii;

class WikiPath extends Widget
{
    /**
     * @var WikiPage
     */
    public $page;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('wikiPath', [
            'page' => $this->page,
            'path' => $this->getPagePath(),
        ]);
    }

    /**
     * @return WikiPage[]
     */
    private function getPagePath(): array
    {
        $page = $this->page->categoryPage;
        $path = [];

        while ($page) {
            $path[] = $page;
            $page = $page->categoryPage;
        }


        $pathLength = count($path);
        if ($pathLength > 4) {
            $path = array_slice($path, 0, 4);
            $path[] = '...';
        }

        return array_reverse($path);
    }
}

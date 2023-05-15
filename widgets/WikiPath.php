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
        $page = $this->page;
        $path = [];

        $pathCharsCount = 0;
        while ($page) {
            $pathCharsCount += strlen($page->title);
            $path[] = $page->isNewRecord ? Yii::t('WikiModule.base', 'New Page') : $page;
            $page = $page->categoryPage;
        }

        $pathLength = count($path);
        if ($pathLength > 3 && $pathCharsCount > 50) {
            foreach ($path as $p => $page) {
                if ($p > 2 && $p < $pathLength - 1) {
                    unset($path[$p]);
                } elseif ($p === 2) {
                    $path[$p] = '...';
                }
            }
        }

        return array_reverse($path);
    }
}

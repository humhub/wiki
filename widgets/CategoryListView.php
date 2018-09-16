<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 07.09.2018
 * Time: 14:50
 */

namespace humhub\modules\wiki\widgets;


use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\EditPages;
use humhub\widgets\JsWidget;
use yii\db\Expression;

class CategoryListView extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'wiki.CategoryListView';

    /**
     * @inheritdoc
     */
    public $id = 'category_list_view';

    /**
     * @inheritdoc
     */
    public $init = true;

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function run()
    {
        // Get created categories
        $categories = WikiPage::findCategories($this->contentContainer)->all();

        $unsortedPages = WikiPage::findUnsorted($this->contentContainer)->all();

        $canEdit = $this->contentContainer->can([AdministerPages::class, EditPages::class]);

        return $this->render('categoryListView', [
            'options' => $this->getOptions(),
            'categories' => $categories,
            'unsortedPages' => $unsortedPages,
            'contentContainer' => $this->contentContainer,
            'canEdit' => $canEdit
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return [
            'class' => 'wiki-page-list'
        ];
    }

    public function getData()
    {
        return [
            'drop-url' => $this->contentContainer->createUrl('/wiki/page/sort')
        ];
    }

}
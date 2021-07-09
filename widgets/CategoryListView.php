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
     * @var int|null
     */
    public $parentCategoryId = null;

    /**
     * @var bool
     */
    public $showAddPage;

    /**
     * @var bool
     */
    public $showDrag;

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function run()
    {
        if ($this->parentCategoryId) {
            // Get pages of the requested category
            $categories = WikiPage::findByCategoryId($this->contentContainer, $this->parentCategoryId)->all();
            $unsortedPages = [];
        } else {
            // Get root categories and pages without category
            $categories = WikiPage::findCategories($this->contentContainer)
                ->andWhere(['IS', 'wiki_page.parent_page_id', new Expression('NULL')])->all();
            $unsortedPages = WikiPage::findUnsorted($this->contentContainer)->all();
        }

        if (empty($categories) && empty($unsortedPages)) {
            return '';
        }

        return $this->render('categoryListView', [
            'options' => $this->getOptions(),
            'categories' => $categories,
            'unsortedPages' => $unsortedPages,
            'contentContainer' => $this->contentContainer,
            'showAddPage' => $this->showAddPage,
            'showDrag' => $this->showDrag,
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
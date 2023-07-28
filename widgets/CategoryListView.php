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
     * @var int Level of the sub-category
     */
    public $level = 0;

    /**
     * @var int Text indent for level of the sub-category
     */
    public $levelIndent = 17;

    /**
     * @var int|null Max level deep to load sub-pages, null - to load all levels
     */
    public $maxLevel;

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function run()
    {
        if ($this->parentCategoryId) {
            // Get pages of the requested category
            $categories = WikiPage::findByCategoryId($this->contentContainer, $this->parentCategoryId)->all();
        } else {
            // Get root categories
            $categories = WikiPage::findCategories($this->contentContainer)
                ->andWhere(['IS', 'wiki_page.parent_page_id', new Expression('NULL')])
                ->all();
        }

        if (empty($categories)) {
            return '';
        }

        return $this->render('categoryListView', [
            'options' => $this->getOptions(),
            'categories' => $categories,
            'contentContainer' => $this->contentContainer,
            'showAddPage' => $this->showAddPage,
            'showDrag' => $this->showDrag,
            'level' => $this->level,
            'levelIndent' => $this->levelIndent,
            'maxLevel' => $this->maxLevel
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        $attrs = ['class' => 'wiki-page-list'];

        if ($this->isFolded()) {
            $attrs['style'] = 'display:none';
        }

        return $attrs;
    }

    public function getData()
    {
        return [
            'drop-url' => $this->contentContainer->createUrl('/wiki/page/sort'),
            'icon-page' => '',
            'icon-category' => 'fa fa-caret-down',
        ];
    }

    private function getParent(): ?WikiPage
    {
        return $this->parentCategoryId ? WikiPage::findOne($this->parentCategoryId) : null;
    }

    private function isFolded(): bool
    {
        $parent = $this->getParent();
        return $parent && $parent->isFolded();
    }

}

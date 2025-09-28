<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 07.09.2018
 * Time: 23:51
 */

namespace humhub\modules\wiki\models\forms;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\WikiPage;
use yii\db\ActiveQuery;
use yii\db\Expression;

class WikiPageItemDrop extends ItemDrop
{
    /**
     * @var string
     */
    public $modelClass = WikiPage::class;

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @inheritdoc
     */
    protected function getSortItemsQuery(): ActiveQuery
    {
        $parentFilter = $this->targetId
            ? [$this->getTableName() . '.parent_page_id' => $this->targetId]
            : ['IS', $this->getTableName() . '.parent_page_id', new Expression('NULL')];

        return WikiPage::find()
            ->contentContainer($this->contentContainer)
            ->readable()
            ->orderBy($this->getTableName() . '.sort_order')
            ->andWhere(['!=', $this->getTableName() . '.id', $this->id])
            ->andWhere($parentFilter);
    }

    protected function updateTarget()
    {
        $targetId = $this->targetId ?: new Expression('NULL');
        $this->getModel()->updateAttributes(['parent_page_id' => $targetId]);
    }
}

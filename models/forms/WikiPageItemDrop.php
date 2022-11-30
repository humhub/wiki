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
use yii\web\HttpException;

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
     * @return ActiveQuery
     * @throws HttpException
     * @throws \yii\base\Exception
     */
    protected function getSortItemsQuery()
    {
        if ($this->targetId) {
            return WikiPage::find()->contentContainer($this->contentContainer)
                ->readable()
                ->andWhere(['parent_page_id' => $this->targetId]);
        }

        return WikiPage::findRootPages($this->contentContainer);
    }

    protected function updateTarget()
    {
        $targetId = $this->targetId ?: new Expression('NULL');
        $this->getModel()->updateAttributes(['parent_page_id' => $targetId]);
    }
}
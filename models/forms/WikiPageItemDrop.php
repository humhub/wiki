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
        /* @var $model \humhub\modules\wiki\models\WikiPage */
        $model = $this->getModel();

        if($model->is_category) {
            return WikiPage::findCategories($this->contentContainer);
        } else if($this->targetId) {
            $target = WikiPage::findOne(['id' => $this->targetId]);
            if(!$target->is_category) {
                throw new HttpException(400);
            }
            return $target->findChildren();
        }

        return WikiPage::findUnsorted($this->contentContainer);
    }

    protected function updateTarget()
    {
        /* @var $model \humhub\modules\wiki\models\WikiPage */
        $model = $this->getModel();

        if($model->is_category) {
            return;
        }

        $targetId = $this->targetId ? $this->targetId : new Expression('NULL');
        $this->getModel()->updateAttributes(['parent_page_id' => $targetId]);
    }
}
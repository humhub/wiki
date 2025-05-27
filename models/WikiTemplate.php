<?php

namespace humhub\modules\wiki\models;

use humhub\components\ActiveRecord;
use humhub\modules\space\models\Space;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\content\widgets\richtext\ProsemirrorRichText;
use Yii;

/**
 * Class WikiTemplate
 *
 * @property int $id
 * @property int|null $contentcontainer_id  (nullable because Global templates = NULL)
 * @property string $title
 * @property string|null $content
 */
class WikiTemplate extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wiki_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['content', 'title_template'], 'string'],
            [['contentcontainer_id'], 'integer'],
            [['placeholders'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => Yii::t('WikiModule.base', 'Title'),
            'content' => Yii::t('WikiModule.base', 'Content'),
        ];
    }

    /**
     * Contentcontainer support (Space/User)
     */
    public function getContentName()
    {
        return $this->title;
    }

    public function getContentDescription()
    {
        return Yii::t('WikiModule.base', 'Wiki Template');
    }

    public function getContentContainer()
    {
        return $this->hasOne(ContentContainer::class, ['id' => 'contentcontainer_id']);
    }

    /**
     * Converting normal text into Richtext and storing in database
     */
    public function afterSave($insert, $changedAttributes)
    {
        ProsemirrorRichText::postProcess($this->content, $this);

        parent::afterSave($insert, $changedAttributes);
    }
}


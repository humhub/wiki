<?php

namespace humhub\modules\wiki;

use Yii;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\space\models\Space;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;

class Module extends ContentContainerModule
{

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [
            Space::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        parent::disable();
        foreach (WikiPage::model()->findAll() as $page) {
            $page->delete();
        }
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            return Yii::t('WikiModule.base', 'Adds a wiki to this space.');
        } elseif ($container instanceof User) {
            return Yii::t('WikiModule.base', 'Adds a wiki to your profile.');
        }
    }

    /**
     * @inheritdoc
     */
    public function disableContentContainer(ContentContainerActiveRecord $container)
    {
        parent::disableContentContainer($container);

        foreach (WikiPage::model()->contentContainer($container)->findAll() as $page) {
            $page->delete();
        }
    }

}

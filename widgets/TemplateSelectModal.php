<?php

namespace humhub\modules\wiki\widgets;

use humhub\modules\wiki\models\WikiTemplate;
use humhub\widgets\Modal;
use humhub\widgets\Button;
use humhub\widgets\ModalButton;
use yii\helpers\Html;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\content\components\ContentContainerActiveRecord;

class TemplateSelectModal extends Modal
{
    /**
     * @var ContentContainerActiveRecord
     */
    public $container;

    public $id = 'templateSelectModal';

    public function run()
    {
        $templates = WikiTemplate::find()
        ->where(['contentcontainer_id' => $this->container])
        ->all();

        return $this->render('templateSelectModal', [
            'templates' => $templates,
        ]);
    }
}

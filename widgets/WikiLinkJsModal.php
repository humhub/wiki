<?php

namespace humhub\modules\wiki\widgets;

use humhub\widgets\bootstrap\Button;
use humhub\widgets\modal\JsModal;
use humhub\widgets\modal\ModalButton;
use Yii;

class WikiLinkJsModal extends JsModal
{
    public $id = 'wikiLinkModal';

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    public function init()
    {
        $this->title = Yii::t('WikiModule.base', '<strong>Wiki</strong> link');
        $this->body = $this->render('wikiLinkModal', ['contentContainer' => $this->contentContainer]);
        $this->footer = Button::save()->action('wiki.linkExtension.setEditorLink')->loader(false) . ModalButton::cancel();
        parent::init();
    }

}

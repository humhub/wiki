<?php


namespace humhub\modules\wiki\widgets;


use humhub\widgets\Button;
use humhub\widgets\Modal;
use humhub\widgets\ModalButton;
use Yii;

class WikiLinkModal extends Modal
{
    public $id = 'wikiLinkModal';

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    public function init()
    {
        $this->header = Yii::t('WikiModule.base', '<strong>Wiki</strong> link');
        $this->body = $this->render('wikiLinkModal', ['contentContainer' => $this->contentContainer]);
        $this->footer = Button::save()->action('wiki.linkExtension.setEditorLink')->loader(false).ModalButton::cancel();
        parent::init();
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 11.09.2018
 * Time: 13:20
 */

namespace humhub\modules\wiki\widgets;


use humhub\components\Widget;
use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\widgets\PermaLink;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\ViewHistory;
use humhub\widgets\JsWidget;
use humhub\widgets\Link;
use Yii;

class WikiContent extends JsWidget
{
    public $jsWidget = 'wiki.Content';
    public $init = true;

    public $cssClass;
    public $title;
    public $titleIcon;
    public $cols = 9;

    public function init()
    {
        parent::init();
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $body = ob_get_clean();

        return Html::tag('div', $this->renderTitle().$body, $this->getOptions());
    }

    public function getAttributes()
    {
        $cssClass = 'col-lg-'.$this->cols.' col-md-'.$this->cols.' col-sm-'.$this->cols.' wiki-content';
        $cssClass .= ($this->cssClass) ? ' '.$this->cssClass : '';

        return [
            'class' => $cssClass
        ];
    }

    protected function renderTitle()
    {
        $icon = $this->titleIcon ? Html::tag('i', '', ['class' => 'fa '.$this->titleIcon]) : '';

        return empty($this->title) ? '' : Html::tag('h1', (empty($icon)) ? $this->title : $icon.' '.$this->title).'<hr>';
    }
}

<?php

namespace humhub\modules\wiki\activities;

use humhub\modules\activity\components\BaseContentActivity;
use humhub\modules\activity\interfaces\ConfigurableActivityInterface;
use Yii;

class WikiPageEditedActivity extends BaseContentActivity implements ConfigurableActivityInterface
{
    public static function getTitle(): string
    {
        return Yii::t('WikiModule.base', 'Wiki');
    }

    public static function getDescription(): string
    {
        return Yii::t('WikiModule.base', 'Whenever someone edits a wiki page.');
    }

    protected function getMessage(array $params): string
    {
        // Backward compatibility of translation placeholders
        $params['userName'] = $params['displayName'];
        $params['wikiPageTitle'] = $params['contentTitle'];

        return Yii::t('WikiModule.base', '{userName} edited the Wiki page "{wikiPageTitle}".', $params);
    }
}

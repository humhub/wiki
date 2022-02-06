<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2021 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\modules\comment\widgets\CommentLink;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\like\widgets\LikeLink;
use humhub\modules\content\widgets\ContentObjectLinks as ContentObjectLinksBase;
use yii\helpers\ArrayHelper;


class ContentObjectLinks extends ContentObjectLinksBase
{

    /**
     * Initialize default widgets for Content links
     */
    function initDefaultWidgets()
    {
        if (!($this->object instanceof ContentActiveRecord)) {
            return;
        }
        $this->addWidget(LikeLink::class, ['object' => $this->object], ['sortOrder' => 200]);
        $this->addWidget(CommentLink::class, ['object' => $this->object], ['sortOrder' => 100]);
    }
}

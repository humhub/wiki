<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\modules\stream\helpers\StreamHelper;
use humhub\modules\topic\models\Topic;
use humhub\modules\topic\widgets\TopicBadge;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\bootstrap\Link;

class WikiTopicBadge extends TopicBadge
{
    public static function getWikiTopicBadge(Topic $topic, WikiPage $page): static
    {
        $badge = parent::forTopic($topic);

        if ($page->content->hidden) {
            $badge->withLink(Link::withAction('', 'topic.addTopic')->options([
                'data-topic-id' => $topic->id,
                'data-topic-url' => StreamHelper::createUrl($page->content->container, [
                    'topicId' => $topic->id,
                    'filters[entry_hidden]' => 1,
                ]),
            ]));
        }

        return $badge;
    }
}

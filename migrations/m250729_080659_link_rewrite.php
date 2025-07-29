<?php

use humhub\modules\content\models\Content;
use humhub\modules\wiki\models\WikiPageRevision;
use yii\console\widgets\Table;
use yii\db\Migration;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Inflector;
use humhub\modules\space\models\Space;

class m250729_080659_link_rewrite extends Migration
{
    public function safeUp()
    {
        $revisionsQuery = WikiPageRevision::find()->where([
            'OR',

            ['REGEXP', 'content', 'http://intrane.jarola.nl/index.php?r=file%2Ffile%2Fdownload&guid=[a-f0-9-]{36}&download=1'],
            ['LIKE', 'content', 'http://intranet.jarola.nl/index.php?r=content%2Fperma&id=']
        ]);

        $errors = [];
        $done = 0;
        $total = $revisionsQuery->count();
        Console::startProgress($done, $total);

        foreach ($revisionsQuery->each() as $revision) {
            /** @var WikiPageRevision $revision */
            $revision->content = preg_replace(
                '#http://intranet\.jarola\.nl/index\.php\?r=file%2Ffile%2Fdownload&guid=([a-f0-9\-]{36})&download=1#i',
                'https://intranet.jarola.nl/file/view?guid=$1',
                $revision->content
            );


            preg_match_all(
                '#http://intranet\.jarola\.nl/index\.php\?r=content%2Fperma&id=(\d+)#i',
                $revision->content,
                $matches
            );

            foreach (array_filter(array_unique(ArrayHelper::getValue($matches, 1, []))) as $contentId) {
                $content = Content::find()
                    ->alias('c')
                    ->innerJoinWith('contentContainer cc', false)
                    ->where(['c.id' => $contentId])
                    ->one();

                if (empty($content->container)) {
                    $errors[] = ["Cant find container for content id: {$contentId}"];

                    continue;
                }

                $revision->content = str_replace(
                    'http://intranet.jarola.nl/index.php?r=content%2Fperma&id=' . $content->id,
                    sprintf(
                        'https://intranet.jarola.nl/%s/%s/wiki/%d/%s',
                        $content->container instanceof Space ? 's' : 'u',
                        $content->container->{$content->container instanceof Space ? 'url' : 'username' },
                        $content->model->id,
                        Inflector::slug($content->model->title)
                    ),
                    $revision->content
                );
            }

            $revision->save();
            Console::updateProgress(++$done, $total);
        }

        Console::endProgress();

        if (!empty($errors)) {
            echo Table::widget([
                'headers' => ['Errors'],
                'rows' => $errors,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250729_080659_link_rewrite cannot be reverted.\n";

        return false;
    }

}

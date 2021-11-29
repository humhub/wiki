<?php

namespace humhub\modules\wiki\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use humhub\modules\wiki\permissions\CreatePage;
use Yii;

/**
 * This is the model class for table "wiki_page_revision".
 *
 * The followings are the available columns in table 'wiki_page_revision':
 * @property integer $id
 * @property integer $revision
 * @property integer $is_latest
 * @property integer $wiki_page_id
 * @property integer $user_id
 * @property string $content
 *
 * @property-read WikiPage $page
 */
class WikiPageRevision extends ActiveRecord
{
    /**
     * @var bool
     */
    var $isCurrentlyEditing = false;

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'wiki_page_revision';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            [['revision', 'wiki_page_id', 'user_id'], 'required'],
            [['revision', 'is_latest', 'wiki_page_id', 'user_id'], 'integer'],
            ['content', 'safe'],
        ];
    }

    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getPage()
    {
        return $this->hasOne(WikiPage::class, ['id' => 'wiki_page_id']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'revision' => 'Revision',
            'is_latest' => 'Is Latest',
            'wiki_page_id' => 'Wiki Page',
            'user_id' => 'User',
            'content' => 'Content',
        );
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->is_latest = 1;

        $this->createPagesFromWikiLinks();

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        WikiPageRevision::updateAll(['is_latest' => 0], 'wiki_page_id=:wikiPageId AND id!=:selfId', [':wikiPageId' => $this->wiki_page_id, ':selfId' => $this->id]);

        try {
            Yii::$app->search->update(WikiPage::findOne(['id' => $this->wiki_page_id]));
        } catch (\Throwable $e) {
            Yii::error($e);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Create Wiki Pages from wiki links found in the content of this Revision
     */
    private function createPagesFromWikiLinks()
    {
        if (!$this->page->content->container->can(CreatePage::class)) {
            return;
        }

        if (!preg_match_all('/(\[.+?\]\(wiki:)(.+?)( ".+?"\))/i', $this->content, $wikiLinks)) {
            return;
        }

        foreach ($wikiLinks[2] as $w => $wikiTitle) {
            if ($wikiPage = $this->createOrFindPageByTitle($wikiTitle)) {
                $this->updateWikiLink($wikiLinks[0][$w], $wikiLinks[1][$w] . $wikiPage->id . $wikiLinks[3][$w]);
            }
        }
    }

    /**
     * Create or find existing Wiki Page by title
     *
     * @param string $title
     * @return WikiPage|null
     * @throws \yii\base\Exception
     */
    private function createOrFindPageByTitle(string $title): ?WikiPage
    {
        if (preg_match('/^\d+$/', $title) &&
            WikiPage::find()->contentContainer($this->page->content->container)->where(['wiki_page.id' => $title])->exists()) {
            // Exclude case when title is ID of the existing Wiki Page
            return null;
        }

        if ($wikiPage = WikiPage::find()->contentContainer($this->page->content->container)->where(['title' => $title])->one()) {
            return $wikiPage;
        }

        $wikiPage = new WikiPage($this->page->content->container);
        $wikiPage->setScenario(WikiPage::SCENARIO_CREATE);
        $wikiPage->title = $title;
        $wikiPage->parent_page_id = $this->page->parent_page_id;
        if (!$wikiPage->save()) {
            return null;
        }

        $wikiPageRevision = $wikiPage->createRevision();
        $wikiPageRevision->content = $title;
        if (!$wikiPageRevision->save()) {
            return null;
        }

        return $wikiPage;
    }

    /**
     * Update Wiki Link in content
     *
     * @param string $wikiLinkWithTitle
     * @param string $wikiLinkWithId
     */
    private function updateWikiLink(string $wikiLinkWithTitle, string $wikiLinkWithId)
    {
        $this->content = str_replace($wikiLinkWithTitle, $wikiLinkWithId, $this->content);
    }

}

<?php
namespace wiki;

use humhub\modules\wiki\helpers\RestDefinitions;
use humhub\modules\wiki\models\WikiPage;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \ApiTester
{
    use _generated\ApiTesterActions;

    /**
     * Define custom actions here
     */

    public function createWikiPage($title, $content, $params = [])
    {
        $params = array_merge([
            'isHome' => 0,
            'adminOnly' => 0,
            'isCategory' => 0,
            'parentPageId' => 0,
            'isPublic' => 0,
            'topics' => [],
            'containerId' => 1,
        ], $params);

        $this->amGoingTo('create a sample wiki page');
        $this->sendPost('wiki/container/' . $params['containerId'], [
            'WikiPage' => [
                'title' => $title,
                'is_home' => $params['isHome'],
                'admin_only' => $params['adminOnly'],
                'is_category' => $params['isCategory'],
                'parent_page_id' => $params['parentPageId'],
            ],
            'WikiPageRevision' => [
                'content' => $content,
            ],
            'PageEditForm' => [
                'isPublic' => $params['isPublic'],
                'topics' => $params['topics'],
            ],
        ]);
    }

    public function createSampleWikiPage()
    {
        $this->createWikiPage('Sample wiki page title', 'Sample wiki page content');
    }

    public function createSampleWikiPageWithRevisions($revisionsNumber = 2)
    {
        $this->createSampleWikiPage();

        for ($i = 1; $i <= $revisionsNumber; $i++) {
            $this->sendPut('wiki/page/1', [
                'WikiPage' => ['title' => $i . ' Updated title'],
                'WikiPageRevision' => ['content' => $i . ' Updated content.'],
            ]);
        }
    }

    public function getWikiPageDefinitionById($wikiPageId)
    {
        $wikiPage = WikiPage::findOne(['id' => $wikiPageId]);
        return ($wikiPage ? RestDefinitions::getWikiPage($wikiPage) : []);
    }

    public function seeLastCreatedWikiPageDefinition()
    {
        $wikiPage = WikiPage::find()
            ->orderBy(['id' => SORT_DESC])
            ->one();
        $wikiPageDefinition = ($wikiPage ? RestDefinitions::getWikiPage($wikiPage) : []);
        $this->seeSuccessResponseContainsJson($wikiPageDefinition);
    }

    public function seeWikiPageDefinitionById($wikiPageId)
    {
        $this->seeSuccessResponseContainsJson($this->getWikiPageDefinitionById($wikiPageId));
    }

    public function seePaginationWikiPagesResponse($url, $wikiPageIds)
    {
        $wikiPageDefinitions = [];
        foreach ($wikiPageIds as $wikiPageId) {
            $wikiPageDefinitions[] = $this->getWikiPageDefinitionById($wikiPageId);
        }

        $this->seePaginationGetResponse($url, $wikiPageDefinitions);
    }

}

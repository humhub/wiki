<?php

class WikiMarkdown extends cebe\markdown\GithubMarkdown
{

    protected function handleInternalUrls($url)
    {
        // Handle urls to file 
        if (substr($url, 0, 10) === "file-guid-") {
            $guid = str_replace('file-guid-', '', $url);
            $file = File::model()->findByAttributes(array('guid'=>$guid));
            if ($file !== null) {
                return $file->getUrl();
            }
        }
        
        // Handle internal wiki links
        if (substr($url, 0, 1) !== "." && substr($url, 0, 1) !== "/" && substr($url, 0, 7) !== "http://" && substr($url, 0, 8) !== "https://") {
            return Yii::app()->getController()->createContainerUrl('page/view', array('title' => $url));
        }

        return $url;
    }

    protected function renderLink($block)
    {
        if (isset($block['refkey'])) {
            if (($ref = $this->lookupReference($block['refkey'])) !== false) {
                $block = array_merge($block, $ref);
            } else {
                return $block['orig'];
            }
        }

        $block['url'] = $this->handleInternalUrls($block['url']);

        return '<a href="' . htmlspecialchars($block['url'], ENT_COMPAT | ENT_HTML401, 'UTF-8') . '"'
                . (empty($block['title']) ? '' : ' title="' . htmlspecialchars($block['title'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, 'UTF-8') . '"')
                . '>' . $this->renderAbsy($block['text']) . '</a>';
    }

    protected function renderImage($block)
    {
        if (isset($block['refkey'])) {
            if (($ref = $this->lookupReference($block['refkey'])) !== false) {
                $block = array_merge($block, $ref);
            } else {
                return $block['orig'];
            }
        }

        $block['url'] = $this->handleInternalUrls($block['url']);

        return '<img src="' . htmlspecialchars($block['url'], ENT_COMPAT | ENT_HTML401, 'UTF-8') . '"'
                . ' alt="' . htmlspecialchars($block['text'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, 'UTF-8') . '"'
                . (empty($block['title']) ? '' : ' title="' . htmlspecialchars($block['title'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, 'UTF-8') . '"')
                . ($this->html5 ? '>' : ' />');
    }

    /**
     * Renders a code block
     */
    protected function renderCode($block)
    {
        $class = isset($block['language']) ? ' class="' . $block['language'] . '"' : '';
        return "<pre><code$class>" . htmlspecialchars($block['content'] . "\n", ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</code></pre>\n";
    }

}

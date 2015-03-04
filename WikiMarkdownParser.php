<?php

/**
 * HumHub
 * Copyright © 2014 The HumHub Project
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 */

/**
 * WikiMarkdownParser also handles internal wiki urls
 *
 * @author luke
 */
class WikiMarkdownParser extends HMarkdown
{

    protected function handleInternalUrls($url)
    {

        if (Yii::app()->getController() instanceof ContentContainerController) {
            if (substr($url, 0, 10) !== "file-guid-" && substr($url, 0, 1) !== "." && substr($url, 0, 1) !== "/" && substr($url, 0, 7) !== "http://" && substr($url, 0, 8) !== "https://") {
                return Yii::app()->getController()->createContainerUrl('page/view', array('title' => $url));
            }
        }

        return parent::handleInternalUrls($url);
    }

}

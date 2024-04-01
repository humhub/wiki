humhub.module('wiki.Page', function (module, require, $) {
    var Widget = require('ui.widget').Widget;
    var wikiView = require('wiki');

    /**
     * This widget represents a wiki page and wraps the actual page content.
     */
    var Page = Widget.extend();

    Page.prototype.init = function () {
        var that = this;
        this.$.find('#wiki-page-richtext').on('afterInit', function () {
            $(this).data('inited-richtext', true);
            if (that.$.data('diff')) {
                compareBlocks($(that.$.data('diff')).find('#wiki-page-richtext'), $(this));
            } else {
                that.$.fadeIn('slow');
            }

            that.buildIndex();
            that.initAnchor();
            that.initHeaderEditIcons();
        });
    };

    Page.print = function () {
        window.print();
    }

    Page.prototype.initAnchor = function () {
        if (window.location.hash) {
            wikiView.toAnchor(window.location.hash);
        }

        $('.wiki-content').find('.header-anchor').on('click', function (evt) {
            evt.preventDefault();
            wikiView.toAnchor($(this).attr('href'));
        });
    };

    Page.prototype.initHeaderEditIcons = function () {
        const editUrl = this.data('edit-url');

        if (editUrl === undefined) {
            return;
        }

        // Wrap header + content below(before next header) into a block,
        // in order to make the header-edit-link visible only on hover the block
        this.$.html(this.$.html().replace(/(<h([1-3]).*?>.+?<\/h\2>([\s\S]*?(?=<h[1-3]|$)))/ig,
            '<div class="wiki-page-header-wrapper">$1</div>'));

        this.$.find('h1,h2,h3').each(function () {
            const anchor = $(this).find('a.header-anchor');
            const editIconLink = '<a href="' + editUrl + '" class="header-edit-link"><i class="fa fa-pencil"></i></a>';
            if (anchor.length) {
                anchor.before(editIconLink + ' ');
            } else {
                $(this).append(' ' + editIconLink);
            }
        });
    }

    Page.prototype.buildIndex = function () {
        var $list = $('<ul class="nav nav-pills nav-stacked">');

        var $listHeader = $('<li><a href="#">' + wikiView.text('pageindex') + '</li></a>').on('click', function (evt) {
            evt.preventDefault();
            var $siblings = $(this).siblings(':not(.nav-divider)');
            if ($siblings.first().is(':visible')) {
                $siblings.hide();
            } else {
                $siblings.show();
            }
        });

        $list.append($listHeader);

        var hasHeadLine = false;
        var headerLevel = 1;
        var headerNum = [];
        var minLevel = this.$.find('#wiki-page-richtext h1').length ? 1 : 2;
        var showh3 = this.$.find('#wiki-page-richtext h3').length <= module.config.tocMaxH3;
        this.$.find('#wiki-page-richtext').find('h1,h2' + (showh3 ? ',h3' : '')).each(function () {
            hasHeadLine = true;

            var $header = $(this).clone();
            var $anchor = $header.find('.header-anchor').clone();
            $anchor.show();

            $header.find('.header-anchor').remove();
            var text = $header.text();

            if (!text || !text.trim().length) {
                return;
            }

            var currentHeaderLevel = 1;
            if ($header.is('h2')) {
                currentHeaderLevel = minLevel === 2 ? 1 : 2;
            } else if ($header.is('h3')) {
                currentHeaderLevel = minLevel === 2 ? 2 : 3;
            }

            if (currentHeaderLevel !== headerLevel) {
                if (currentHeaderLevel > headerLevel) {
                    headerNum[currentHeaderLevel] = 0;
                }
                headerLevel = currentHeaderLevel;
            }

            if (typeof (headerNum[headerLevel]) === 'undefined') {
                headerNum[headerLevel] = 0;
            }

            headerNum[headerLevel]++;

            var numberString = '';
            for (var i = 1; i <= headerLevel; i++) {
                numberString += (i > 1 ? '.' : '') + (headerNum[i] ?? 1);
            }

            $anchor.text(text).prepend('<span>' + numberString + '</span>');

            var $li = $('<li>');

            var cssClass = 'wiki-page-index-section wiki-page-index-section-level' + currentHeaderLevel;

            $li.addClass(cssClass);

            $anchor.on('click', function (evt) {
                evt.preventDefault();
                wikiView.toAnchor($anchor.attr('href'));
            });
            $li.append($anchor);
            $list.append($li);
        });

        if (hasHeadLine) {
            var firstHeader = this.$.find('h1, h2' + (showh3 ? ',h3' : '')).first();
            if (firstHeader.length) {
                firstHeader.before($list);
            } else {
                this.$.prepend($list);
            }
            $list.wrap('<div class="wiki-page-index"></div>')
        }
    };

    /**
     * Compare HTML content of two blocks
     */
    var compareBlocks = function (oldBlock, newBlock) {
        var interval = setInterval(function () {
            if (!oldBlock.length || oldBlock.data('inited-richtext')) {
                var fixImgRegExp = new RegExp('(<img .+?)data-ui-gallery=".+?"(.+?>)', 'g');
                var oldHtml = oldBlock.length ? oldBlock.html().replace(fixImgRegExp, '$1$2') : '';
                var newHtml = newBlock.length ? newBlock.html().replace(fixImgRegExp, '$1$2') : '';

                newBlock.html(htmldiff(oldHtml, newHtml));
                newBlock.parent().fadeIn('slow');

                clearInterval(interval);
            }
        }, 100);
    }

    module.export = Page;
});

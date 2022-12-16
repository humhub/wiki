humhub.module('wiki.Page', function(module, require, $) {
    var Widget = require('ui.widget').Widget;
    var wikiView = require('wiki');

    /**
     * This widget represents a wiki page and wraps the actual page content.
     */
    var Page = Widget.extend();

    Page.prototype.init = function() {
        var that = this;
        this.$.find('#wiki-page-richtext').on('afterInit', function() {
            $(this).data('inited-richtext', true);
            if (that.$.data('diff')) {
                compareBlocks($(that.$.data('diff')).find('#wiki-page-richtext'), $(this));
            } else {
                that.$.fadeIn('slow');
            }

            that.buildIndex();
            that.initAnchor();
        });
    };

    Page.print = function() {
        window.print();
    }

    Page.prototype.initAnchor = function() {
        if (window.location.hash) {
            wikiView.toAnchor(window.location.hash);
        }

        $('.wiki-content').find('.header-anchor').on('click', function(evt) {
            evt.preventDefault();
            wikiView.toAnchor($(this).attr('href'));
        });
    };

    Page.prototype.buildIndex = function() {
        var $list = $('<ul class="nav nav-pills nav-stacked">');

        var $listHeader = $('<li><a href="#">'+wikiView.text('pageindex')+'</li></a>').on('click', function(evt) {
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
        var minLevel = $('#wiki-page-richtext').find('h1').length ? 1 : 2;
        $('#wiki-page-richtext').children('h1,h2').each(function() {
            hasHeadLine = true;

            var $header = $(this).clone();
            var $anchor = $header.find('.header-anchor').clone();
            $anchor.show();

            $header.find('.header-anchor').remove();
            var text = $header.text();

            if (!text || !text.trim().length) {
                return;
            }

            var currentHeaderLevel = $header.is('h2') ? (minLevel === 2 ? 1 : 2) : 1;
            if (currentHeaderLevel !== headerLevel) {
                if (currentHeaderLevel > headerLevel) {
                    headerNum[currentHeaderLevel] = 0;
                }
                headerLevel = currentHeaderLevel;
            }

            if (typeof(headerNum[headerLevel]) === 'undefined') {
                headerNum[headerLevel] = 0;
            }

            headerNum[headerLevel]++;

            var numberString = '';
            for (var i = 1; i <= headerLevel; i++) {
                numberString += (i > 1 ? '.' : '') + (headerNum[i] ?? 1);
            }

            $anchor.text(text).prepend('<span>' + numberString + '</span>');

            var $li = $('<li>');

            var cssClass = currentHeaderLevel === 1 ? 'wiki-page-index-section' : 'wiki-page-index-sub-section';

            $li.addClass(cssClass);

            $anchor.on('click', function(evt) {
                evt.preventDefault();
                wikiView.toAnchor($anchor.attr('href'));
            });
            $li.append($anchor);
            $list.append($li);
        });

        if (hasHeadLine) {
            var firstHeader = this.$.find('h1, h2').first();
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
    var compareBlocks = function(oldBlock, newBlock) {
        var interval = setInterval(function() {
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
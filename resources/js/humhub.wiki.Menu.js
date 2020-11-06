humhub.module('wiki.Menu', function(module, require, $) {
    var Widget = require('ui.widget').Widget;
    var wikiView = require('wiki');
    var event = require('event');
    var view = require('ui.view');

    /**
     * This widget represents the wiki menu
     */
    var Menu = Widget.extend();

    Menu.prototype.init = function() {
        var that = this;

        if(!view.isSmall()) {
            wikiView.registerStickyElement(this.$, $('.wiki-content'), function() {
                return that.$.height() < $(window).height() - view.getContentTop();
            });
        }

        if($('.wiki-page-content').length) {
            this.buildIndex();
            this.initAnchor();
        }

        event.off('humhub:content:afterMove.wiki').on('humhub:content:afterMove.wiki', function() {
            if(that.$.find('#wiki_index').length) {
                that.$.find('#wiki_index').click();
            }
        });
    };

    Menu.prototype.initAnchor = function() {
        if(window.location.hash) {
            wikiView.toAnchor(window.location.hash);
        }

        $('.wiki-content').find('.header-anchor').on('click', function(evt) {
            evt.preventDefault();
            wikiView.toAnchor($(this).attr('href'));
        });
    };

    Menu.prototype.buildIndex = function() {
        var $list = $('<ul class="nav nav-pills nav-stacked">');

        var $listHeader = $('<li><a href="#"><i class="fa fa-list-ol"></i> '+wikiView.text('pageindex')+'</li></a>').on('click', function(evt) {
            evt.preventDefault();
            var $siblings = $(this).siblings(':not(.nav-divider)');
            if($siblings.first().is(':visible')) {
                $siblings.hide();
            } else {
                $siblings.show();
            }
        });

        $list.append($listHeader);

        var hasHeadLine = false;
        $('#wiki-page-richtext').children('h1,h2').each(function() {
            hasHeadLine = true;

            var $header = $(this).clone();
            var $anchor = $header.find('.header-anchor').clone();
            $anchor.show();

            $header.find('.header-anchor').remove();
            var text = $header.text();

            if(!text || !text.trim().length) {
                return;
            }

            $anchor.text($header.text());

            var $li = $('<li>');

            var cssClass = $header.is('h2') ? 'wiki-menu-sub-section' : 'wiki-menu-section';

            $li.addClass(cssClass);

            $anchor.prepend('<i class="fa fa-caret-right"></i>');
            $anchor.on('click', function(evt) {
                evt.preventDefault();
                wikiView.toAnchor($anchor.attr('href'));
            });
            $li.append($anchor);
            $list.append($li);

        });

        if(hasHeadLine) {
            $list.append('<li class="nav-divider"></li>');
            $('.wiki-menu-fixed').prepend($list);
        }
    };

    module.export = Menu;
});
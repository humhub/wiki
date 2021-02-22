humhub.module('wiki.Menu', function(module, require, $) {
    var Widget = require('ui.widget').Widget;
    var wikiView = require('wiki');
    var event = require('event');
    var view = require('ui.view');
    var additions = require('ui.additions');

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

        if(localStorage.getItem("wiki-menu-state") === 'collapsed') {
            this.toggleMenu();
        }
    };

    Menu.prototype.save = function() {
        let $submit = $('#wiki-edit-form').find('[type="submit"]');
        $submit.trigger('click');
        $submit[0].scrollIntoView();
    };

    Menu.prototype.toggleMenu = function() {
        let $fixed = this.$.find('.wiki-menu-fixed');
        let $collapseMenu = this.$.find('.wiki-collapse-menu');
        let $page = $('.wiki-content');

        let pageClasses = [
            'col-lg-9 col-md-9 col-sm-9',
            'col-lg-11 col-md-11 col-sm-11'
        ];

        let collapseMenuClasses = [
            'clearfix',
            'collapsed'
        ];

        let menuClasses = [
            'col-lg-3 col-md-3 col-sm-3',
            'col-lg-1 col-md-1 col-sm-1'
        ];

        let buttonClasses = [
            'pull-right',
            ''
        ];

        let wasCollapsed = !$fixed.is(':visible');

        $fixed.toggle();

        $page.removeClass(pageClasses[+wasCollapsed])
            .addClass(pageClasses[+!wasCollapsed]);

        this.$.removeClass(menuClasses[+wasCollapsed])
            .addClass(menuClasses[+!wasCollapsed]);

        this.$.find('#wikiMenuToggle').removeClass(buttonClasses[+wasCollapsed])
            .addClass(buttonClasses[+!wasCollapsed]);

        $collapseMenu.removeClass(collapseMenuClasses[+wasCollapsed])
            .addClass(collapseMenuClasses[+!wasCollapsed]);

        if(!wasCollapsed) {
            this.$.find('.collapse-button').show();
        } else {
            this.$.find('.collapse-button').hide();
        }

        if (!this.$.data('build')) {
            this.$.find('.wiki-menu-main').find('a').each(function () {
                let $copy = $(this).clone();
                let text = $copy.text().trim();
                $copy.addClass('tt btn btn-default btn-xs collapse-button')
                    .attr('title', text)
                    .attr('data-placement', 'left')
                    .html($copy.find('i'));
                $collapseMenu.append($copy);
            });
            additions.applyTo($collapseMenu);
            this.$.data('build', true);
        }

        localStorage.setItem("wiki-menu-state", !wasCollapsed ? 'collapsed' : '');
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
        var $list = $('<ul class="nav wiki-menu-index nav-pills nav-stacked">');

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
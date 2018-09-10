humhub.module('wiki', function(module, require, $) {
    var Widget = require('ui.widget').Widget;
    var client = require('client');

    var CategoryListView = Widget.extend();

    CategoryListView.prototype.init = function() {
        this.$.find('.fa-caret-square-o-down').on('click', function() {
            var $icon = $(this);
            var $pageList = $icon.parent().siblings('.wiki-page-list');
            $pageList.slideToggle('fast', function() {
                var newIconClass = ($pageList.is(':visible')) ? 'fa-caret-square-o-down' : 'fa-caret-square-o-right';
                $icon.removeClass('fa-caret-square-o-down fa-caret-square-o-right').addClass(newIconClass);
            });
        });

        this.$.find('.page-title, .page-category-title').hover(function() {
            $(this).find('.wiki-edit').show();
        }, function() {
            $(this).find('.wiki-edit').hide();
        });

        this.$.sortable({
            handle: '.page-category-title',
            helper: 'clone',
            update: $.proxy(this.dropItem, this)
            //placeholder: "task-list-state-highlight",
            //update: $.proxy(this.dropItem, this)
        });

        this.$.find('.wiki-page-list').sortable({
            handle: '.page-title',
            connectWith: '.wiki-page-list:not(#category_list_view)',
            helper: 'clone',
            update: $.proxy(this.dropItem, this)
        });
    };

    CategoryListView.prototype.dropItem = function (event, ui) {
        var $item = ui.item;
        var pageId = $item.data('page-id');
        debugger;

        var targetId = $item.is('.wiki-category-list-item') ? null : $item.closest('.wiki-category-list-item').data('page-id');

        var data = {
            'ItemDrop[id]': pageId,
            'ItemDrop[targetId]': targetId,
            'ItemDrop[index]': $item.index()
        };

        var that = this;
        client.post(this.options.dropUrl, {data: data}).then(function(response) {
            if (!response.success) {
                $item.closest('.category_list_view, .wiki-page-list').sortable('cancel');
                module.log.error('', true);
            }
        }).catch(function(e) {
            module.log.error(e, true);
            $item.closest('.category_list_view, .wiki-page-list').sortable('cancel');
        });
    };

    var checkAnchor = function() {
        if(window.location.hash) {
            toAnchor(window.location.hash)
        }
    };

    var offset = null;

    var getViewOffset = function() {
        if(offset === null) {
            offset = $('#topbar-first').length ? $('#topbar-first').height() : 0;
            offset += $('#topbar-second').length ? $('#topbar-second').height() : 0;
            // TODO: Workaround for enterprise edition the offset should be configurable by theme variable!
            offset += $('.space-nav').find('.container-fluid').length ? $('.space-nav').height() : 0;
        }

        return offset;
    };

    var buildIndex = function() {
        var $list = $('<ul class="nav nav-pills nav-stacked">');
        var hasHeadLine = false;
        $('#wiki-page-richtext').children('h1,h2').each(function() {
            hasHeadLine = true;

            var $header = $(this).clone();
            var $anchor = $header.find('.header-anchor').clone();
            $anchor.show();

            $header.find('.header-anchor').remove();
            var test = $header.text();
            $anchor.text($header.text());

            var $li = $('<li>');

            var cssClass = $header.is('h2') ? 'wiki-menu-sub-section' : 'wiki-menu-section';

            $li.addClass(cssClass);

            $anchor.prepend('<i class="fa fa-caret-right"></i>');
            $anchor.on('click', function(evt) {
                evt.preventDefault();
                toAnchor($anchor.attr('href'));
            });
            $li.append($anchor);
            $list.append($li);

        });

        if(hasHeadLine) {
            $list.append('<li class="nav-divider"></li>');
            $('.wiki-menu-fixed').prepend($list);
        }
    };

    var toAnchor = function(anchor) {
        $('html, body').animate({
            scrollTop: $(anchor).offset().top - getViewOffset()
        }, 200);

        if(history && history.replaceState) {
            history.replaceState(null, null, anchor);
        }
    };

    module.initOnPjaxLoad = true;

    var init = function(pjax) {
        if(!$('.wiki-content').length) {
            return;
        }

        $('#wiki-page-richtext').on('afterInit', function() {
            $('.wiki-content').find('.markdown-render').fadeIn('slow');

            if($('.wiki-page-content').length) {
                buildIndex();
                checkAnchor();
                checkScroll();
            }
        });

        $(window).off('scroll.wiki').on('scroll.wiki', function () {
            checkScroll();
        });

    };


    var checkScroll = function() {
        var $window = $(window);
        var windowHeight = $window.height();
        var windowBottom = $window.scrollTop();

        var menuTop = $('.wiki-menu').offset().top;
        var scrollTop = $window.scrollTop() + getViewOffset();

        if(scrollTop > menuTop) {
            $('.wiki-menu-fixed').css({'margin-top' : (scrollTop - menuTop + 5)+'px'});

            if($('#wiki-page-edit').length) {
                var $richtext = $('#wiki-page-edit').find('.humhub-ui-richtext');
                var $richtextMenuBar = $('#wiki-page-edit').find('.ProseMirror-menubar');
                var richtextTop = $richtext.offset().top;
                if(scrollTop > richtextTop) {
                    $richtextMenuBar.css({'position':'absolute', 'top': (scrollTop - richtextTop+ 1)+'px'});
                } else {
                    $richtextMenuBar.css({'position':'relative', 'top': '0'});
                }
            }
        } else {
            $('.wiki-menu-fixed').css({'margin-top' : 0});
            if($('#wiki-page-edit').length) {
                var $richtextMenuBar = $('#wiki-page-edit').find('.ProseMirror-menubar');
                $richtextMenuBar.css({'position':'relative', 'top': '0'});
            }
        }
    };

    var revertRevision = function(evt) {
        client.post(evt).then(function(evt) {
            //client.pjax.redirect
        });
    };

    var unload = function() {
        $(window).off('scroll.wiki');
        offset = null;
    };

    module.export({
        CategoryListView: CategoryListView,
        init: init,
        revertRevision: revertRevision,
        unload: unload
    })
});
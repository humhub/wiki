humhub.module('wiki.CategoryListView', function(module, require, $) {
    var Widget = require('ui.widget').Widget;
    var client = require('client');
    var view = require('ui.view');

    /**
     * This widget represents the wiki index page
     */
    var CategoryListView = Widget.extend();

    var DELAY_DRAG_SMALL_DEVICES = 250;

    CategoryListView.prototype.init = function() {
        this.$.find('.fa-caret-down, .fa-caret-right').on('click', function() {
            var $icon = $(this);
            var $pageList = $icon.parent().siblings('.wiki-page-list');
            $pageList.slideToggle('fast', function() {
                var newIconClass = ($pageList.is(':visible')) ? 'fa-caret-down' : 'fa-caret-right';
                $icon.removeClass('fa-caret-down fa-caret-right').addClass(newIconClass);
                // Update folding state of the Category for current User
                var $categoryId = $icon.closest('.wiki-category-list-item[data-page-id]').data('page-id');
                if ($categoryId) {
                    client.get(module.config.updateFoldingStateUrl, {data: {
                        categoryId: $categoryId,
                        state: ($pageList.is(':visible') ? 0 : 1),
                    }});
                }
            });
        });

        this.$.sortable({
            delay: (view.isSmall()) ? DELAY_DRAG_SMALL_DEVICES : null,
            handle: '.drag-icon',
            items: '[data-page-id]',
            helper: 'clone',
            receive: $.proxy(this.beforeDropItem, this),
            update: $.proxy(this.dropItem, this)
        });

        this.$.find('.wiki-page-list').sortable({
            delay: (view.isSmall()) ? DELAY_DRAG_SMALL_DEVICES : null,
            handle: '.drag-icon',
            connectWith: '.wiki-page-list',
            helper: 'clone',
            receive: $.proxy(this.beforeDropItem, this),
            update: $.proxy(this.dropItem, this)
        });
    };

    CategoryListView.prototype.beforeDropItem = function (event, ui) {
        var fixListIndent = function ($item) {
            var $list = $item.closest('.wiki-page-list');
            if ($list.length) {
                var title = $list.prev('.page-category-title');
                var indent = title.length ? parseInt(title.css('padding-left')) + 20 : 12;
                $item.children('.page-title, .page-category-title').css('padding-left', indent + 'px');
                $item.children('.wiki-page-list').children('li').each(function () {
                    fixListIndent($(this));
                });
            }
        }
        fixListIndent(ui.item);
    }

    CategoryListView.prototype.dropItem = function (event, ui) {
        var $item = ui.item;
        var pageId = $item.data('page-id');

        var targetId = $item.is('.wiki-category-list-item') ? null : $item.closest('.wiki-category-list-item').data('page-id');

        var data = {
            'ItemDrop[id]': pageId,
            'ItemDrop[targetId]': targetId,
            'ItemDrop[index]': $item.index()
        };

        client.post(this.options.dropUrl, {data: data}).then(function (response) {
            if (!response.success) {
                $item.closest('.category_list_view, .wiki-page-list').sortable('cancel');
                module.log.error('', true);
            }
        }).catch(function (e) {
            module.log.error(e, true);
            $item.closest('.category_list_view, .wiki-page-list').sortable('cancel');
        });
    };

    module.export = CategoryListView;
});
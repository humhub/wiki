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
        this.$.find('.fa-caret-square-o-down, .fa-caret-square-o-right').on('click', function() {
            var $icon = $(this);
            var $pageList = $icon.parent().siblings('.wiki-page-list');
            $pageList.slideToggle('fast', function() {
                var newIconClass = ($pageList.is(':visible')) ? 'fa-caret-square-o-down' : 'fa-caret-square-o-right';
                $icon.removeClass('fa-caret-square-o-down fa-caret-square-o-right').addClass(newIconClass);
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
            items: '.wiki-category-list-item[data-page-id]',
            helper: 'clone',
            update: $.proxy(this.dropItem, this)
        });

        this.$.find('.wiki-page-list').sortable({
            delay: (view.isSmall()) ? DELAY_DRAG_SMALL_DEVICES : null,
            handle: '.drag-icon',
            connectWith: '.wiki-page-list:not(#category_list_view)',
            helper: 'clone',
            update: $.proxy(this.dropItem, this)
        });

        if(view.isNormal()) {
            this.$.find('.page-title, .page-category-title').hover(function() {
                $(this).find('.wiki-page-control:not(.drag-icon)').show();
            }, function() {
                $(this).find('.wiki-page-control:not(.drag-icon)').hide();
            });
        }
    };

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
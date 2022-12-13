humhub.module('wiki.CategoryListView', function(module, require, $) {
    var Widget = require('ui.widget').Widget;
    var client = require('client');
    var view = require('ui.view');

    /**
     * This widget represents the wiki index page
     */
    var CategoryListView = Widget.extend();

    var DELAY_DRAG_SMALL_DEVICES = 250;

    CategoryListView.prototype.indent = {
        default: 12,
        level: 40,
    };

    CategoryListView.prototype.init = function() {
        this.$.on('click', '.fa-caret-down, .fa-caret-right', function() {
            var $icon = $(this);
            var $pageList = $icon.parent().parent().siblings('.wiki-page-list');
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

        this.$.find('.wiki-page-list').add(this.$).sortable({
            containment : '#category_list_view',
            delay: view.isSmall() ? DELAY_DRAG_SMALL_DEVICES : null,
            handle: '.drag-icon',
            cursor: 'move',
            connectWith: '.wiki-page-list',
            items: '[data-page-id]',
            helper: 'clone',
            placeholder: 'ui-sortable-drop-area',
            change: $.proxy(this.changePosition, this),
            over: $.proxy(this.overList, this),
            out: $.proxy(this.clearDroppablePlaceholder, this),
            start: $.proxy(this.startDropItem, this),
            receive: $.proxy(this.beforeDropItem, this),
            update: $.proxy(this.dropItem, this)
        });
    };

    CategoryListView.prototype.changePosition = function () {
        var dropArea = $('.ui-sortable-drop-area');
        var parentIndent = parseInt(dropArea.parent().prev('.page-title').css('padding-left'));
        dropArea.css('margin-left', (parentIndent - this.indent.default) + 'px');
    }

    CategoryListView.prototype.overList = function (event, ui) {
        this.clearDroppablePlaceholder();
        ui.placeholder.closest('.wiki-page-list').prev('div').addClass('wiki-current-target-category');
    }

    CategoryListView.prototype.clearDroppablePlaceholder = function () {
        $('.wiki-current-target-category').removeClass('wiki-current-target-category');
    }

    CategoryListView.prototype.startDropItem = function (event, ui) {
        ui.item.show().addClass('wiki-current-dropping-page');
    }

    CategoryListView.prototype.updateIcons = function () {
        var that = this;
        var iconPageSelector = '.' + that.data('icon-page').replace(' ', '.');
        var iconCategorySelector = '.' + that.data('icon-category').replace(' ', '.');

        $('.page-title').each(function () {
            var hasChildren = $(this).next('ul.wiki-page-list').find('li.wiki-category-list-item').length;
            var isCategory = $(this).hasClass('page-is-category');

            if (hasChildren && !isCategory) {
                console.log('11111');
                $(this).addClass('page-is-category');
                $(this).find(iconPageSelector)
                    .after('<i class="' + that.data('icon-category') + '"></i>')
                    .remove();
            }

            if (!hasChildren && isCategory) {
                console.log('22222');
                $(this).removeClass('page-is-category');
                $(this).find(iconCategorySelector)
                    .after('<i class="' + that.data('icon-page') + '"></i>')
                    .remove();
            }
        });
    }

    CategoryListView.prototype.beforeDropItem = function (event, ui) {
        var that = this;
        var fixListIndent = function ($item) {
            var $list = $item.closest('.wiki-page-list');
            if ($list.length) {
                var title = $list.prev('.page-title');
                var indent = title.length ? parseInt(title.css('padding-left')) + that.indent.level : that.indent.default;
                $item.children('.page-title').css('padding-left', indent + 'px');
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

        var parent = $item.parents('.wiki-category-list-item').first();
        var targetId = parent.length ? parent.data('page-id') : null;

        var data = {
            'ItemDrop[id]': pageId,
            'ItemDrop[targetId]': targetId,
            'ItemDrop[index]': $item.index()
        };

        $item.removeClass('wiki-current-dropping-page');
        this.clearDroppablePlaceholder();
        this.updateIcons();

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
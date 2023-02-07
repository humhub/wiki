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
            delay: view.isSmall() ? DELAY_DRAG_SMALL_DEVICES : null,
            handle: '.drag-icon',
            cursor: 'move',
            connectWith: '.wiki-page-list',
            items: '[data-page-id]',
            helper: 'clone',
            placeholder: 'ui-sortable-drop-area',
            tolerance: 'pointer',
            create: $.proxy(this.fixer, this),
            change: $.proxy(this.changePosition, this),
            over: $.proxy(this.overList, this),
            out: $.proxy(this.clearDroppablePlaceholder, this),
            start: $.proxy(this.startDropItem, this),
            stop: $.proxy(this.stopDropItem, this),
            receive: $.proxy(this.beforeDropItem, this),
            update: $.proxy(this.dropItem, this)
        });
    };

    CategoryListView.prototype.changePosition = function () {
        const dropArea = $('.ui-sortable-drop-area');
        const indent = this.$.is(dropArea.parent()) ? 0
            : parseInt(dropArea.parent().prev('.page-title').css('padding-left'))
                + this.indent.level * 2 - this.indent.default - 8
        dropArea.css('margin-left', indent + 'px');
    }

    CategoryListView.prototype.overList = function (event, ui) {
        this.clearDroppablePlaceholder();
        const parent = ui.placeholder.closest('.wiki-page-list').parent();
        parent.addClass('wiki-current-target-category')
            .parents('.wiki-category-list-item:eq(0)').addClass('wiki-parent-target-category');
        if (parent.is(':hidden')) {
            parent.closest('.wiki-parent-target-category').addClass('wiki-current-target-category');
        }
        if (ui.placeholder.index() === 0 && ui.placeholder.parent().children('li').length > 1) {
            parent.addClass('wiki-current-target-category-over');
        }
    }

    CategoryListView.prototype.clearStyle = function (className) {
        $('.' + className).removeClass(className);
        return this;
    }

    CategoryListView.prototype.clearDroppablePlaceholder = function () {
        return this.clearStyle('wiki-current-target-category')
            .clearStyle('wiki-current-target-category-over')
            .clearStyle('wiki-parent-target-category');
    }

    CategoryListView.prototype.startDropItem = function (event, ui) {
        ui.item.show().addClass('wiki-current-dropping-page');
        ui.helper.height(ui.item.children('.page-title').outerHeight());
        this.clearStyle('wiki-list-item-selected');
        this.$.addClass('wiki-page-is-dropping');
        $('.wiki-page-list').each(function() {
            if ($(this).is(':hidden') || $(this).find('li').length === 0) {
                $(this).addClass('wiki-page-list-droppable-target');
            }
        });
    }

    CategoryListView.prototype.stopDropItem = function (event, ui) {
        this.clearDroppablePlaceholder()
            .clearStyle('wiki-current-dropping-page')
            .clearStyle('wiki-page-list-droppable-target')
            .clearStyle('wiki-page-is-dropping');
        $('.wiki-category-list-item .page-current').parent().addClass('wiki-list-item-selected');
    }

    CategoryListView.prototype.updateIcons = function () {
        var that = this;
        var iconPageSelector = '.' + that.data('icon-page').replace(' ', '.');
        var iconCategorySelector = '.' + that.data('icon-category').replace(' ', '.');

        $('.page-title').each(function () {
            var hasChildren = $(this).next('ul.wiki-page-list').find('li.wiki-category-list-item').length;
            var isCategory = $(this).hasClass('page-is-category');

            if (hasChildren && !isCategory) {
                $(this).addClass('page-is-category');
                $(this).find(iconPageSelector)
                    .after('<i class="' + that.data('icon-category') + '"></i>')
                    .remove();
            }

            if (!hasChildren && isCategory) {
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

    // Initialize and get a fixer element to help sort item under the last root item when it has children
    CategoryListView.prototype.fixer = function () {
        const className = 'wiki-category-list-sort-fixer';
        let fixer = this.$.children('.' + className);
        if (fixer.length) {
            return fixer;
        }

        fixer = $('<li data-page-id>').addClass(className);
        this.$.append(fixer);

        return fixer;
    }

    CategoryListView.prototype.refreshFixer = function () {
        this.$.append(this.fixer());
    }

    CategoryListView.prototype.dropItem = function (event, ui) {
        const $item = ui.item;
        const pageId = $item.data('page-id');

        const parent = $item.parents('.wiki-category-list-item').first();
        const targetId = parent.length ? parent.data('page-id') : null;
        let index = $item.index();

        const fixerIndex = this.fixer().index();
        if (index > fixerIndex) {
            index = fixerIndex;
            this.refreshFixer();
        }

        const data = {
            'ItemDrop[id]': pageId,
            'ItemDrop[targetId]': targetId,
            'ItemDrop[index]': index
        };

        this.stopDropItem();
        this.updateIcons();

        client.post(this.options.dropUrl, {data: data}).then(function (response) {
            if (!response.success) {
                $item.closest('.category_list_view, .wiki-page-list').sortable('cancel');
            }
        }).catch(function (e) {
            module.log.error(e, true);
            $item.closest('.category_list_view, .wiki-page-list').sortable('cancel');
        });
    };

    module.export = CategoryListView;
});
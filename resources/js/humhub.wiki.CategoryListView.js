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
        level: 17,
    };

    CategoryListView.prototype.init = function() {
        this.initFoldingSubpages();
        this.initSorting();
        this.initPageTitleLink();
        this.initDragButtonHoverDelay();
    }

    CategoryListView.prototype.initFoldingSubpages = function() {
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
    }

    CategoryListView.prototype.initSorting = function() {
        this.$.find('.wiki-page-list').add(this.$).sortable({
            delay: view.isSmall() ? DELAY_DRAG_SMALL_DEVICES : null,
            handle: '.drag-icon',
            cursor: 'move',
            connectWith: '.wiki-page-list',
            items: '[data-page-id]',
            helper: 'clone',
            placeholder: 'ui-sortable-drop-area',
            tolerance: 'pointer',
            sort: $.proxy(this.fixSort, this),
            create: $.proxy(this.fixer, this),
            change: $.proxy(this.updatePlaceholderStyle, this),
            over: $.proxy(this.overList, this),
            out: $.proxy(this.clearDroppablePlaceholder, this),
            start: $.proxy(this.startDropItem, this),
            stop: $.proxy(this.stopDropItem, this),
            update: $.proxy(this.dropItem, this)
        });
    }

    CategoryListView.prototype.initPageTitleLink = function() {
        this.$.on('click', '.page-title', function (e) {
            if (e.target !== this && !$(e.target).hasClass('page-title-text')) {
                return;
            }
            const subPagesList = $(this).next('ul.wiki-page-list');
            if (subPagesList.length && subPagesList.is(':hidden')) {
                // Unfold subpages
                $(this).find('.fa-caret-right').trigger('click');
            }
            if (e.target === this) {
                // Open URL of the page
                $(this).find('a.page-title-text').trigger('click');
            }
        });
    }

    CategoryListView.prototype.fixSort = function (event, ui) {
        this.fixPlaceholderPositionY(event, ui);
        this.fixPlaceholderPositionX(event, ui);
    }

    CategoryListView.prototype.fixPlaceholderPositionX = function (event, ui) {
        const itemCount = ui.placeholder.parent().children('li:not(.ui-sortable-helper)').length;
        const isLastItem = itemCount > 1 && itemCount - 1 === ui.placeholder.index();
        const cursorX = ui.position.left + ui.helper.find('.drag-icon').position().left;
        const parentX = parseInt(ui.placeholder.parent().prev('.page-title').css('padding-left'));
        const cursorIsOverParent = cursorX - this.indent.level < parentX;

        if (isLastItem && cursorIsOverParent) {
            // Move placeholder to parent category up
            ui.placeholder.parent().parent().after(ui.placeholder);
            this.updateTargetStyle(ui.placeholder);
            this.updatePlaceholderStyle();
        }
    }

    CategoryListView.prototype.fixPlaceholderPositionY = function (event, ui) {
        const deltaPosition = event.pageY - ui.placeholder.offset().top;
        const isWrongPosition = Math.abs(deltaPosition) > ui.item.outerHeight() / 2;

        if (isWrongPosition) {
            deltaPosition > 0
                ? ui.placeholder.next().after(ui.placeholder) // Move placeholder down
                : ui.placeholder.prev().before(ui.placeholder); // Move placeholder up
        }
    }

    CategoryListView.prototype.updatePlaceholderStyle = function () {
        const dropArea = $('.ui-sortable-drop-area');
        const indent = this.$.is(dropArea.parent()) ? 0
            : parseInt(dropArea.parent().prev('.page-title').css('padding-left'))
                + this.indent.level * 2 - this.indent.default - 8
        dropArea.css('margin-left', indent + 'px');
    }

    CategoryListView.prototype.overList = function (event, ui) {
        this.updateTargetStyle(ui.placeholder);
    }

    CategoryListView.prototype.updateTargetStyle = function (placeholder) {
        this.clearDroppablePlaceholder();
        const parent = placeholder.closest('.wiki-page-list').parent();
        parent.addClass('wiki-current-target-category')
            .parents('.wiki-category-list-item:eq(0)').addClass('wiki-parent-target-category');
        if (parent.is(':hidden')) {
            parent.closest('.wiki-parent-target-category').addClass('wiki-current-target-category');
        }
        if (placeholder.index() === 0 && placeholder.parent().children('li').length > 1) {
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
        if (typeof ui !== 'undefined') {
            this.fixListIndent(ui.item)
        }
    }

    CategoryListView.prototype.updateIcons = function () {
        const that = this;
        const hasIconPage = that.data('icon-page') !== undefined;

        $('.page-title').each(function () {
            var hasChildren = $(this).next('ul.wiki-page-list').find('li.wiki-category-list-item').length;
            var isCategory = $(this).hasClass('page-is-category');

            if (hasChildren && !isCategory) {
                $(this).addClass('page-is-category');
                const iconCategoryHtml = '<i class="' + that.data('icon-category') + '"></i> ';
                if (hasIconPage) {
                    $(this).find('.' + that.data('icon-page').replace(' ', '.'))
                        .after(iconCategoryHtml).remove();
                } else {
                    $(this).find('.page-title-text').before(iconCategoryHtml);
                }
            }

            if (!hasChildren && isCategory) {
                $(this).removeClass('page-is-category');
                const iconCategory = $(this).find('.' + that.data('icon-category').replace(' ', '.'));
                if (hasIconPage) {
                    iconCategory.after('<i class="' + that.data('icon-page') + '"></i>');
                }
                iconCategory.remove();
            }
        });
    }

    CategoryListView.prototype.fixListIndent = function (item) {
        const list = item.closest('.wiki-page-list');
        if (list.length) {
            const that = this;
            const title = list.prev('.page-title');
            const indent = title.length ? parseInt(title.css('padding-left')) + this.indent.level : this.indent.default;
            item.children('.page-title').css('padding-left', indent + 'px');
            item.children('.wiki-page-list').children('li').each(function () {
                that.fixListIndent($(this));
            });
        }
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
        const pageTitle = $item.children('.page-title');

        const parent = $item.parents('.wiki-category-list-item').first();
        const targetId = parent.length ? parent.data('page-id') : null;
        let index = $item.index();

        const fixerIndex = this.fixer().index();
        if (index > fixerIndex && $item.parent().is(this.$)) {
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
        pageTitle.addClass('wiki-page-dropped');

        client.post(this.options.dropUrl, {data: data}).then(function (response) {
            if (!response.success) {
                $item.closest('.category_list_view, .wiki-page-list').sortable('cancel');
            }
            pageTitle.removeClass('wiki-page-dropped');
        }).catch(function (e) {
            module.log.error(e, true);
            $item.closest('.category_list_view, .wiki-page-list').sortable('cancel');
            pageTitle.removeClass('wiki-page-dropped');
        });
    };

    CategoryListView.prototype.initDragButtonHoverDelay = function() {
        const HOVER_DELAY = 1000;
        const dragClass = '.wiki-page-control.drag-icon';
    
        this.$.find('.page-title').each(function () {
            const $item = $(this);
            const $dragBtn = $item.find(dragClass);
    
            let timer = null;
    
            $item.on('mouseenter', function () {
                timer = setTimeout(() => {
                    $dragBtn.addClass('visible');
                }, HOVER_DELAY);
            });
    
            $item.on('mouseleave', function () {
                clearTimeout(timer);
                $dragBtn.removeClass('visible');
            });
        });
    };

    module.export = CategoryListView;
});

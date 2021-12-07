humhub.module('wiki', function(module, require, $) {
    var Widget = require('ui.widget').Widget;
    var event = require('event');
    var view = require('ui.view');
    var client = require('client');

    var stickyElementSettings = [];

    var registerStickyElement = function($node, $trigger, condition) {
        stickyElementSettings.push({$node: $node, $trigger: $trigger, condition: condition});
    };

    var Content = Widget.extend();

    Content.prototype.init = function() {
        $(window).off('scroll.wiki').on('scroll.wiki', function () {
            $.each(stickyElementSettings, function(index, setting) {
                var sticky = $(window).scrollTop() + view.getContentTop() > setting.$trigger.offset().top;
                var canStick = setting.condition ? setting.condition.call() : true;

                sticky = sticky && canStick;

                var topMenu = setting.$node.data('menu-top');
                var shiftTopMenuPosition = topMenu && topMenu.length ? topMenu.height() : 0;
                var menuCss = {'position': 'relative', 'top': '0'};

                if (sticky) {
                    menuCss.position = 'sticky';
                    menuCss.top = (view.getContentTop() + shiftTopMenuPosition) + 'px';
                }

                setting.$node.css(menuCss);
                if (shiftTopMenuPosition) {
                    if (sticky) {
                        menuCss.top = (parseInt(menuCss.top) - shiftTopMenuPosition) + 'px';
                    }
                    topMenu.css(menuCss);
                }
            });
        });
    };

    var toAnchor = function(anchor) {
        var $anchor = $('#'+$.escapeSelector(anchor.substr(1, anchor.length)));

        if(!$anchor.length) {
            return;
        }

        $('html, body').animate({
            scrollTop: $anchor.offset().top - view.getContentTop()
        }, 200);

        if(history && history.replaceState) {
            history.replaceState(null, null, '#'+$anchor.attr('id'));
        }
    };

    var revertRevision = function(evt) {
        client.post(evt).then(function(response) {
            client.redirect(response.redirect);
            module.log.success('saved');
        }).catch(function(e) {
            module.log.error(e, true);
        });
    };

    var actionDelete = function(evt) {
        client.pjax.post(evt);
    };

    var unload = function() {
        stickyElementSettings = [];
        $(window).off('scroll.wiki');
        event.off('humhub:content:afterMove.wiki');
    };

    module.export({
        Content: Content,
        toAnchor: toAnchor,
        revertRevision: revertRevision,
        actionDelete: actionDelete,
        registerStickyElement: registerStickyElement,
        unload: unload
    })
});
humhub.module('wiki.Page', function(module, require, $) {
    var Widget = require('ui.widget').Widget;

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
        });
    };

    Page.print = function() {
        window.print();
    }

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
        var that = this;

        var $list = $('<ul class="nav nav-pills nav-stacked">');

        var $listHeader = $('<li><a href="#"><i class="fa fa-list-ol"></i> '+wikiView.text('pageindex')+'</li></a>').on('click', function(evt) {
            evt.preventDefault();
            var $siblings = $(this).siblings(':not(.nav-divider)');
            if($siblings.first().is(':visible')) {
                $siblings.hide();
            } else {
                $siblings.show();
            }
            if (that.$.css('position') === 'sticky') {
                var topMenu = that.$.data('menu-top');
                var shiftTopMenuPosition = topMenu && topMenu.length ? topMenu.height() : 0;
                that.$.css('top', (view.getContentTop() + shiftTopMenuPosition) + 'px');
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
            $('.wiki-page-content').before($list);
            $list.wrap('<div class="col-lg-3 col-md-3 col-sm-3 wiki-menu wiki-menu-top"></div>')
            this.$.data('menu-top', $list.parent());
        }
    };

    module.export = Menu;
});
humhub.module('wiki.Form', function(module, require, $) {
    var Widget = require('ui.widget').Widget;
    var wikiView = require('wiki');
    var modal = require('ui.modal');
    var additions = require('ui.additions');

    /**
     * This widget represents the wiki form
     */
    var Form = Widget.extend();

    Form.prototype.init = function() {
        var that = this;
        this.getRichtext().on('init', function() {
            wikiView.registerStickyElement(that.getRichtextMenu(), that.getRichtext());
        });

        // We need to wait until checkboxes are rendered
        setTimeout(function() {
            that.$.find('.regular-checkbox-container').each(function() {
                var $this = $(this);
                var $checkbox = $this.find('[type="checkbox"][title]');

                if($checkbox.length) {
                    $this.find('label').addClass('tt').attr('title', $checkbox.attr('title'));
                }

                additions.apply($this, 'tooltip');
            }, 200);

            if(that.options.isCategory) {
                $('#wikipage-is_category').click(function () {
                    var $this = $(this);
                    if($this.is(":not(:checked)")) {
                        return modal.confirm({
                            'body': that.options.changeCategoryConfirm
                        }).then(function(confirm) {
                            if(!confirm) {
                                $this.prop('checked', true);
                            }
                        });
                    }
                });
            }
        });
    };

    Form.prototype.getRichtextMenu = function() {
        if(!this.$menu) {
            this.$menu = this.$.find('.ProseMirror-menubar');
        }

        return this.$menu;
    };

    Form.prototype.getRichtext = function() {
        if(!this.$richtext) {
            this.$richtext = this.$.find('.ProsemirrorEditor');
        }

        return this.$richtext;
    };

    Form.prototype.backOverwriting = function () {
        $('input[type=hidden][name="PageEditForm[backOverwriting]"]').val(1);
        $('form[data-ui-widget="wiki.Form"]').submit();
    };
  
    Form.prototype.compareOverwriting = function(evt) {
        var form = this.$;
        var origFormAction = form.attr('action');
        form.attr('target', '_blank')
            .attr('action', evt.$trigger.data('action-click-url'))
            .submit();
        setTimeout(function () {
            form.attr('action', origFormAction);
            form.removeAttr('target');
        }, 500);
    };

    Form.submit = function () {
        $('form[data-ui-widget="wiki.Form"]').submit();
    };

    module.export = Form;
});
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
humhub.module('wiki.linkExtension', function (module, require, $) {
    var richtext = require('ui.richtext.prosemirror');
    var Widget = require('ui.widget').Widget;
    var modal = require('ui.modal');
    var client = require('client');

    var markdown = richtext.api.plugin.markdown;
    var menu = richtext.api.menu;
    var plugin = richtext.api.plugin;
    var NodeSelection = richtext.api.state.NodeSelection;

    /**
     * A wiki link is serialized as markdown [label](wiki:id "anchor")
     *
     * But returned as [label](wiki:id#anchor "title")
     */
    var wikiLinkSchema = {
        nodes: {
            wiki: {
                sortOrder: 1001,
                inline: true,
                group: "inline",
                marks: "em strong strikethrough",
                attrs: {
                    wikiId: {default: ''},
                    anchor: {default: ''},
                    title: {default: ''},
                    label: {default: ''}
                },
                inclusive: false,
                parseDOM: [{
                    tag: "span[data-wiki-page]", getAttrs: function getAttrs(dom) {
                        return {
                            wikiId: dom.getAttribute("data-wiki-page"),
                            title: dom.getAttribute("title"),
                            label: dom.textContent
                        }
                    }
                }],
                toDOM: function (node) {
                    return ["span", {
                        title: (node.attrs.wikiId === '#') ? module.text('pageNotFound') : node.attrs.title,
                        'data-wiki-page': node.attrs.wikiId
                    }, node.attrs.label];
                },
                parseMarkdown: {
                    node: "wiki", getAttrs: function (tok) {
                        var wikiId = tok.attrGet("wikiId");
                        var anchor = '';

                        if(wikiId.indexOf('#') >= 0) {
                            var splitted = wikiId.split('#');
                            wikiId = splitted[0];
                            anchor = splitted[1];
                        }

                        return ({
                            label: tok.attrGet("label"),
                            title: tok.attrGet("title"),
                            wikiId: wikiId,
                            anchor: anchor
                        });
                    }
                },
                toMarkdown: function (state, node) {
                    var link = 'wiki:' + node.attrs.wikiId;
                    state.write("[" + state.esc(node.attrs.label) + "](" + state.esc(link) + ' "'+node.attrs.anchor+'")');
                }
            }
        }
    };

    var registerMarkdownIt = function (markdownIt) {
        markdownIt.inline.ruler.before('link', 'wiki', markdown.createLinkExtension('wiki', {
            labelAttr: 'label',
            hrefAttr: 'wikiId',
            titleAttr: 'title'
        }));

        markdownIt.renderer.rules.wiki = function (token, idx) {
            var wiki = token[idx];

            if (!wiki.attrGet('label')) {
                return '';
            }

            var isEmpty = wiki.attrGet('wikiId') === '#';

            return $('<div>').append($('<a>').attr({
                href: wiki.attrGet('wikiId'),
                title: (isEmpty) ? module.text('pageNotFound') : wiki.attrGet('title'),
                'data-wiki-page': (isEmpty) ? '#' : '',
            }).text(wiki.attrGet('label'))).html();
        };
    };

    var wikiLinkMenu = function menu(context) {
        return [
            {
                id: 'wiki',
                node: 'wiki',
                group: 'marks',
                item: menuItem(context)
            }
        ];
    };

    var menuItem = function (context) {
        var schema = context.schema;
        var markType = schema.marks.wiki;

        var itemOptions = {
            title: context.translate("Add or remove Wiki link"),
            icon: {
                width: 32, height: 32,
                path: 'M30.212 7.3c0 0.1-0.031 0.194-0.094 0.281-0.063 0.081-0.131 0.125-0.212 0.125-0.625 0.063-1.137 0.263-1.531 0.6-0.4 0.338-0.806 0.994-1.225 1.95l-6.45 14.544c-0.044 0.137-0.163 0.2-0.356 0.2-0.15 0-0.269-0.069-0.356-0.2l-3.619-7.563-4.162 7.563c-0.088 0.137-0.2 0.2-0.356 0.2-0.188 0-0.306-0.069-0.369-0.2l-6.331-14.537c-0.394-0.9-0.813-1.531-1.25-1.887s-1.050-0.581-1.831-0.662c-0.069 0-0.131-0.037-0.188-0.106-0.063-0.069-0.087-0.15-0.087-0.244 0-0.237 0.069-0.356 0.2-0.356 0.562 0 1.156 0.025 1.775 0.075 0.575 0.050 1.112 0.075 1.619 0.075 0.513 0 1.125-0.025 1.825-0.075 0.731-0.050 1.381-0.075 1.95-0.075 0.137 0 0.2 0.119 0.2 0.356s-0.044 0.35-0.125 0.35c-0.563 0.044-1.012 0.188-1.338 0.431s-0.487 0.563-0.487 0.963c0 0.2 0.069 0.456 0.2 0.756l5.231 11.825 2.975-5.613-2.769-5.806c-0.5-1.037-0.906-1.706-1.225-2.006s-0.806-0.481-1.456-0.55c-0.063 0-0.113-0.037-0.169-0.106s-0.081-0.15-0.081-0.244c0-0.237 0.056-0.356 0.175-0.356 0.563 0 1.081 0.025 1.556 0.075 0.456 0.050 0.938 0.075 1.456 0.075 0.506 0 1.037-0.025 1.606-0.075 0.581-0.050 1.156-0.075 1.719-0.075 0.137 0 0.2 0.119 0.2 0.356s-0.038 0.35-0.125 0.35c-1.131 0.075-1.694 0.4-1.694 0.963 0 0.25 0.131 0.644 0.394 1.175l1.831 3.719 1.825-3.4c0.25-0.481 0.381-0.887 0.381-1.213 0-0.775-0.563-1.188-1.694-1.237-0.1 0-0.15-0.119-0.15-0.35 0-0.088 0.025-0.162 0.075-0.237s0.1-0.112 0.15-0.112c0.406 0 0.9 0.025 1.494 0.075 0.563 0.050 1.031 0.075 1.394 0.075 0.262 0 0.644-0.025 1.15-0.063 0.637-0.056 1.175-0.088 1.606-0.088 0.1 0 0.15 0.1 0.15 0.3 0 0.269-0.094 0.406-0.275 0.406-0.656 0.069-1.188 0.25-1.587 0.544s-0.9 0.963-1.5 2.013l-2.444 4.475 3.288 6.7 4.856-11.294c0.169-0.412 0.25-0.794 0.25-1.137 0-0.825-0.563-1.263-1.694-1.319-0.1 0-0.15-0.119-0.15-0.35 0-0.237 0.075-0.356 0.225-0.356 0.413 0 0.9 0.025 1.469 0.075 0.525 0.050 0.962 0.075 1.313 0.075 0.375 0 0.8-0.025 1.288-0.075 0.506-0.050 0.962-0.075 1.369-0.075 0.125 0 0.188 0.1 0.188 0.3z'
            },
            sortOrder: 410,
            enable: function(state) {
                return menu.canInsert(state, context.schema.nodes.wiki)
            },
            run: function (state, dispatch, view) {
                if (state.selection instanceof NodeSelection && state.selection.node.type === context.schema.nodes.wiki) {
                    editNode(context, state, dispatch, view, state.selection.node);
                } else {
                    openModal(context, state, dispatch, view);
                }

                return;
            },
        };

        return new menu.MenuItem(itemOptions);
    };

    var editNode = function (context, state, dispatch, view, node) {
        openModal(context, state, dispatch, view, node.attrs);
    };

    var openModal = function (context, state, dispatch, view, attrs) {

        $('.field-wikipagesearch-anchor').hide();

        $('#wikipagesearch-anchor').empty();

        var linkModal = modal.get('#wikiLinkModal');

        $('#wikipagesearch-title').off('change.extract').on('change.extract', function() {
            var id =  $('#wikipagesearch-title').val();

            if(!id) {
                return;
            }

            $('#wikipagesearch-label').val($('#wikipagesearch-title').select2('data')[0].text);

            client.get(module.config.extractTitleUrl, {data: {id:  $('#wikipagesearch-title').val()}}).then(function(response) {
                var slugs = {};

                if(!response.response || !response.response.length) {
                    $('.field-wikipagesearch-anchor').hide();
                    return;
                }

                var $option = $('<option>').attr({value: ''}).text('');
                $('#wikipagesearch-anchor').append($option);

                response.response.forEach(function(title) {
                    var slug = uniqueSlug(slugify(title), slugs);
                    var $option = $('<option>').attr({value: slug}).text(title);
                    $('#wikipagesearch-anchor').append($option);
                });

                if (attrs && attrs.anchor) {
                    $('#wikipagesearch-anchor').val(attrs.anchor)
                }

                $('.field-wikipagesearch-anchor').show();
            }).catch(function(e) {
                module.log.error(e);
            })

        });

        if (attrs && attrs.wikiId) {
            $('#wikipagesearch-title').val(attrs.wikiId).trigger('change');
        } else {
            $('#wikipagesearch-title').val(1).trigger('change');
        }



        if (attrs && attrs.label) {
            $('#wikipagesearch-label').val(attrs.label);
        } else {
            $('#wikipagesearch-label').val(state.doc.cut(state.selection.from, state.selection.to).textContent);
        }

        linkModal.$.off('submitted').on('submitted', function () {
            var $option = $('#wikipagesearch-title option:selected');
            var $label = $('#wikipagesearch-label');

            var newAttrs = {
                title: $option.text(),
                wikiId: $('#wikipagesearch-title').val(),
                anchor: $('#wikipagesearch-anchor').val(),
                label: $label.val()
            };

            linkModal.close();

            view.dispatch(view.state.tr.replaceSelectionWith(context.schema.nodes.wiki.createAndFill(newAttrs)));

            view.focus();
        });

        linkModal.$.on('shown.bs.modal', function (e) {
            $('.ProsemirrorEditor.fullscreen').css('z-index', $(e.target).css('z-index') - 10);
        });

        linkModal.show();
    };

    var slugify = function (s) {
        return encodeURIComponent(String(s).trim().toLowerCase().replace(/\s+/g, '-'))
    };

    var uniqueSlug = function (slug, slugs) {
        var uniq = slug;
        var i = 2;
        while (Object.prototype.hasOwnProperty.call(slugs, uniq)) {
            uniq = slug+'-'+i++;
        };
        slugs[uniq] = true;
        return uniq
    };

    var wiki = {
        id: 'wiki',
        schema: wikiLinkSchema,
        registerMarkdownIt: registerMarkdownIt,
        menu: wikiLinkMenu
    };

    plugin.registerPlugin(wiki);
    plugin.registerPreset('wiki', {
        extend: 'document',
        callback: function (addToPreset) {
            // Note the order here is important since the new plugin kind of overrules the em in some situations.
            addToPreset('wiki', 'wiki', {before: 'link'});
        }
    });

    var setEditorLink = function (evt) {
        var linkModal = modal.get('#wikiLinkModal');
        linkModal.$.trigger('submitted');
        linkModal.close();
    };

    var SearchInput = Widget.extend();

    module.initOnPjaxLoad = true;

    module.export({
        SearchInput: SearchInput,
        setEditorLink: setEditorLink
    });
});
humhub.module('wiki.History', function(module, require, $) {
    var Widget = require('ui.widget').Widget;
    var client = require('client');

    /**
     * This widget represents the wiki history
     */
    var History = Widget.extend();

    var revisions = [];
    var revisionsSelector = 'input[type=radio][name^=revision_]';
    var selectRevisionByIndex = function (index) {
        $('input[type=radio][name=revision_' + revisions[index] + ']').prop('checked', false);
    }

    History.prototype.init = function() {
        $(revisionsSelector).each(function(index) {
            revisions[index] = $(this).val();
            if (index < 2) {
                $(this).prop('checked', true);
            }
        });

        var topIndex = 0;
        var bottomIndex = 1;
        var lastChangedIndexPosition = 'top';

        this.$.on('click', revisionsSelector, function() {
            var currentIndex = $(revisionsSelector).index($('[value=' + $(this).val() + ']'));
            if ((lastChangedIndexPosition === 'top' && currentIndex > topIndex) || (currentIndex > bottomIndex && currentIndex > topIndex)) {
                selectRevisionByIndex(bottomIndex);
                lastChangedIndexPosition = 'bottom';
                bottomIndex = currentIndex;
            } else {
                selectRevisionByIndex(topIndex);
                lastChangedIndexPosition = 'top';
                topIndex = currentIndex;
            }
        });
    };

    History.compare = function () {
        var diffUrl = module.config.wikiDiffUrl;
        var revisionNum = 2;

        $(revisionsSelector + ':checked').each(function() {
            if (!revisionNum) {
                return;
            }
            diffUrl += diffUrl.indexOf('?') > -1 ? '&' : '?';
            diffUrl += 'revision' + revisionNum + '=' + $(this).val();
            revisionNum--;
        });

        client.pjax.redirect(diffUrl);
    }

    module.export = History;
});
/**
 * htmldiff.js a diff algorithm that understands HTML, and produces HTML in the browser.
 *
 * @author https://github.com/tnwinc
 * @see https://github.com/tnwinc/htmldiff.js
 */
!function(){var e,n,t,r,i,f,_,a,o,s,u,h,l,c,d,b,p;o=function(e){return">"===e},s=function(e){return"<"===e},h=function(e){return/^\s+$/.test(e)},u=function(e){return/^\s*<[^>]+>\s*$/.test(e)},l=function(e){return!u(e)},e=function(){return function(e,n,t){this.start_in_before=e,this.start_in_after=n,this.length=t,this.end_in_before=this.start_in_before+this.length-1,this.end_in_after=this.start_in_after+this.length-1}}(),a=function(e){var n,t,r,i,f,_;for(f="char",t="",_=[],r=0,i=e.length;r<i;r++)switch(n=e[r],f){case"tag":o(n)?(t+=">",_.push(t),t="",f=h(n)?"whitespace":"char"):t+=n;break;case"char":s(n)?(t&&_.push(t),t="<",f="tag"):/\s/.test(n)?(t&&_.push(t),t=n,f="whitespace"):/[\w\#@]+/i.test(n)?t+=n:(t&&_.push(t),t=n);break;case"whitespace":s(n)?(t&&_.push(t),t="<",f="tag"):h(n)?t+=n:(t&&_.push(t),t=n,f="char");break;default:throw new Error("Unknown mode "+f)}return t&&_.push(t),_},f=function(n,t,r,i,f,_,a){var o,s,u,h,l,c,d,b,p,g,w,v,k,m,y;for(s=i,o=_,u=0,w={},c=h=m=i,y=f;m<=y?h<y:h>y;c=m<=y?++h:--h){for(k={},d=0,b=(p=r[n[c]]).length;d<b;d++)if(!((l=p[d])<_)){if(l>=a)break;null==w[l-1]&&(w[l-1]=0),v=w[l-1]+1,k[l]=v,v>u&&(s=c-v+1,o=l-v+1,u=v)}w=k}return 0!==u&&(g=new e(s,o,u)),g},d=function(e,n,t,r,i,_,a,o){var s;return null!=(s=f(e,0,t,r,i,_,a))&&(r<s.start_in_before&&_<s.start_in_after&&d(e,n,t,r,s.start_in_before,_,s.start_in_after,o),o.push(s),s.end_in_before<=i&&s.end_in_after<=a&&d(e,n,t,s.end_in_before+1,i,s.end_in_after+1,a,o)),o},r=function(e){var n,t,r,i,f,_;if(null==e.find_these)throw new Error("params must have find_these key");if(null==e.in_these)throw new Error("params must have in_these key");for(r={},n=0,i=(f=e.find_these).length;n<i;n++)for(r[_=f[n]]=[],t=e.in_these.indexOf(_);-1!==t;)r[_].push(t),t=e.in_these.indexOf(_,t+1);return r},_=function(e,n){var t,i;return i=[],t=r({find_these:e,in_these:n}),d(e,n,t,0,e.length,0,n.length,i)},n=function(n,t){var r,i,f,a,o,s,u,h,l,c,d,b,p,g,w,v;if(null==n)throw new Error("before_tokens?");if(null==t)throw new Error("after_tokens?");for(w=g=0,p=[],r={"false,false":"replace","true,false":"insert","false,true":"delete","true,true":"none"},(d=_(n,t)).push(new e(n.length,t.length,0)),a=f=0,h=d.length;f<h;a=++f)"none"!==(i=r[[w===(c=d[a]).start_in_before,g===c.start_in_after].toString()])&&p.push({action:i,start_in_before:w,end_in_before:"insert"!==i?c.start_in_before-1:void 0,start_in_after:g,end_in_after:"delete"!==i?c.start_in_after-1:void 0}),0!==c.length&&p.push({action:"equal",start_in_before:c.start_in_before,end_in_before:c.end_in_before,start_in_after:c.start_in_after,end_in_after:c.end_in_after}),w=c.end_in_before+1,g=c.end_in_after+1;for(v=[],u={action:"none"},o=function(e){return"equal"===e.action&&(e.end_in_before-e.start_in_before==0&&/^\s$/.test(n.slice(e.start_in_before,+e.end_in_before+1||9e9)))},s=0,l=p.length;s<l;s++)o(b=p[s])&&"replace"===u.action||"replace"===b.action&&"replace"===u.action?(u.end_in_before=b.end_in_before,u.end_in_after=b.end_in_after):(v.push(b),u=b);return v},t=function(e,n,t){var r,i,f,_,a,o;for(_=void 0,f=i=0,a=(n=n.slice(e,+n.length+1||9e9)).length;i<a&&(o=n[f],!0===(r=t(o))&&(_=f),!1!==r);f=++i);return null!=_?n.slice(0,+_+1||9e9):[]},p=function(e,n){var r,i,f,_,a;for(_="",f=0,r=n.length;;){if(f>=r)break;if(i=t(f,n,l),f+=i.length,0!==i.length&&(_+="<"+e+">"+i.join("")+"</"+e+">"),f>=r)break;f+=(a=t(f,n,u)).length,_+=a.join("")}return _},(c={equal:function(e,n,t){return n.slice(e.start_in_before,+e.end_in_before+1||9e9).join("")},insert:function(e,n,t){var r;return r=t.slice(e.start_in_after,+e.end_in_after+1||9e9),p("ins",r)},delete:function(e,n,t){var r;return r=n.slice(e.start_in_before,+e.end_in_before+1||9e9),p("del",r)}}).replace=function(e,n,t){return c.delete(e,n,t)+c.insert(e,n,t)},b=function(e,n,t){var r,i,f,_;for(_="",r=0,i=t.length;r<i;r++)f=t[r],_+=c[f.action](f,e,n);return _},(i=function(e,t){var r;return e===t?e:(e=a(e),t=a(t),r=n(e,t),b(e,t,r))}).html_to_tokens=a,i.find_matching_blocks=_,_.find_match=f,_.create_index=r,i.calculate_operations=n,i.render_operations=b,"function"==typeof define?define([],function(){return i}):"undefined"!=typeof module&&null!==module?module.exports=i:"undefined"!=typeof window&&(window.htmldiff=i)}();
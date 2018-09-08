humhub.module('wiki', function(module, require, $) {
    let richtext = require('ui.richtext.prosemirror');
    let Widget = require('ui.widget').Widget;
    let modal = require('ui.modal');
    let client = require('client');

    let wiki = {
        id: 'wiki',
        schema: {
            marks: {
                sortOrder: 400,
                wiki: {
                    attrs: {
                        href: { default: '#'},
                        wikiId: { default: ''},
                        title: { default: ''},
                    },
                    inclusive: false,
                    parseDOM:
                        [{
                            tag: "a[href]", getAttrs: function getAttrs(dom) {
                                return {
                                    href: dom.getAttribute("href"),
                                    wikiId: dom.getAttribute("data-wiki-id"),
                                    title: dom.textContent
                                }
                            }
                        }],
                    toDOM: (node) => {
                        return ["a", {
                            href: node.attrs.href,
                            target: '_self',
                            'data-wiki-id': node.attrs.wikiId,
                            style: 'color:red'
                        }, node.attrs.title];
                    },
                    parseMarkdown: {
                        mark: "link", getAttrs: function (tok) {

                            return ({
                                href: tok.attrGet("href"),
                                title: tok.attrGet("title"),
                                wikiId: tok.attrGet("wikiId")
                            });
                        }
                    },
                    toMarkdown: {
                        open: "[",
                        close: function close(state, mark) {
                            let href = (mark.attrs.fileGuid) ? 'file-guid:'+mark.attrs.fileGuid  : mark.attrs.href;
                            return "](" + state.esc(href) + (mark.attrs.title ? " " + state.quote(mark.attrs.title) : "") + ")"
                        }
                    }
                }
            }
        },
        registerMarkdownIt: (markdownIt) => {
            markdownIt.inline.ruler.before('link','wiki', richtext.api.plugin.markdown.createLinkExtension('wiki', {
                labelAttr: 'title',
                hrefAttr : 'wikiId',
                titleAttr: 'href'
            }));
        },
        menu: function menu(context) {
            return [
                {
                    id: 'wiki',
                    mark: 'wiki',
                    group: 'marks',
                    item: menuItem(context)
                 }
            ]
        }
    };

    var menuItem = function(context) {
        let schema = context.schema;
        let markType = schema.marks.wiki;

        //let command = richtext.api.menu.markActive(schema.marks.wiki);
        let itemOptions = {
            title: context.translate("Wiki Link"),
            icon: {
                width: 32, height: 32,
                path:'M30.212 7.3c0 0.1-0.031 0.194-0.094 0.281-0.063 0.081-0.131 0.125-0.212 0.125-0.625 0.063-1.137 0.263-1.531 0.6-0.4 0.338-0.806 0.994-1.225 1.95l-6.45 14.544c-0.044 0.137-0.163 0.2-0.356 0.2-0.15 0-0.269-0.069-0.356-0.2l-3.619-7.563-4.162 7.563c-0.088 0.137-0.2 0.2-0.356 0.2-0.188 0-0.306-0.069-0.369-0.2l-6.331-14.537c-0.394-0.9-0.813-1.531-1.25-1.887s-1.050-0.581-1.831-0.662c-0.069 0-0.131-0.037-0.188-0.106-0.063-0.069-0.087-0.15-0.087-0.244 0-0.237 0.069-0.356 0.2-0.356 0.562 0 1.156 0.025 1.775 0.075 0.575 0.050 1.112 0.075 1.619 0.075 0.513 0 1.125-0.025 1.825-0.075 0.731-0.050 1.381-0.075 1.95-0.075 0.137 0 0.2 0.119 0.2 0.356s-0.044 0.35-0.125 0.35c-0.563 0.044-1.012 0.188-1.338 0.431s-0.487 0.563-0.487 0.963c0 0.2 0.069 0.456 0.2 0.756l5.231 11.825 2.975-5.613-2.769-5.806c-0.5-1.037-0.906-1.706-1.225-2.006s-0.806-0.481-1.456-0.55c-0.063 0-0.113-0.037-0.169-0.106s-0.081-0.15-0.081-0.244c0-0.237 0.056-0.356 0.175-0.356 0.563 0 1.081 0.025 1.556 0.075 0.456 0.050 0.938 0.075 1.456 0.075 0.506 0 1.037-0.025 1.606-0.075 0.581-0.050 1.156-0.075 1.719-0.075 0.137 0 0.2 0.119 0.2 0.356s-0.038 0.35-0.125 0.35c-1.131 0.075-1.694 0.4-1.694 0.963 0 0.25 0.131 0.644 0.394 1.175l1.831 3.719 1.825-3.4c0.25-0.481 0.381-0.887 0.381-1.213 0-0.775-0.563-1.188-1.694-1.237-0.1 0-0.15-0.119-0.15-0.35 0-0.088 0.025-0.162 0.075-0.237s0.1-0.112 0.15-0.112c0.406 0 0.9 0.025 1.494 0.075 0.563 0.050 1.031 0.075 1.394 0.075 0.262 0 0.644-0.025 1.15-0.063 0.637-0.056 1.175-0.088 1.606-0.088 0.1 0 0.15 0.1 0.15 0.3 0 0.269-0.094 0.406-0.275 0.406-0.656 0.069-1.188 0.25-1.587 0.544s-0.9 0.963-1.5 2.013l-2.444 4.475 3.288 6.7 4.856-11.294c0.169-0.412 0.25-0.794 0.25-1.137 0-0.825-0.563-1.263-1.694-1.319-0.1 0-0.15-0.119-0.15-0.35 0-0.237 0.075-0.356 0.225-0.356 0.413 0 0.9 0.025 1.469 0.075 0.525 0.050 0.962 0.075 1.313 0.075 0.375 0 0.8-0.025 1.288-0.075 0.506-0.050 0.962-0.075 1.369-0.075 0.125 0 0.188 0.1 0.188 0.3z'
            },
            sortOrder: 410,
            run(state, dispatch, view) {
                debugger;
                /*if (richtext.api.menu.markActive(state, markType)) {
                    richtext.api.commands.toggleMark(markType)(state, dispatch);
                    return true
                }*/

                let linkModal = modal.get('#wikiLinkModal');
                linkModal.$.off('submitted').on('submitted', function() {
                    debugger;
                    var pageItem = Widget.instance('#wikipagesearch-title').item;

                    var attrs = {
                        title: pageItem.title,
                        href: pageItem.href,
                        wikiId: pageItem.id
                    };

                    richtext.api.commands.toggleMark(markType, attrs)(state, dispatch);
                }).show();
            },
            enable(state) {
                return true; //richtext.api.menu.markActive(state, schema.marks.wiki)
            },
            select(state) {
                return true; //richtext.api.menu.markActive(state, schema.marks.wiki)
            }
        };

        return new richtext.api.menu.MenuItem(itemOptions);
    };

    richtext.api.plugin.registerPlugin(wiki);
    richtext.api.plugin.registerPreset('wiki', {
        extend: 'document',
        callback: function(addToPreset) {
            // Note the order here is important since the new plugin kind of overrules the em in some situations.
            addToPreset('wiki', 'wiki', {before: 'link'})
        }
    });

    var SearchDropdown = Widget.extend();

    SearchDropdown.prototype.init = function() {
        var that = this;
        this.$.autocomplete({
            source: this.options.searchUrl,
            minLength: 2,
            appendTo: '#wikiLinkModal',
            select: function(evt, ui) {
                that.item = ui.item;
            }
        })
    };

    var setEditorLink = function(evt) {
        debugger;
        modal = modal.get('#wikiLinkModal');
        modal.$.trigger('submitted');
        modal.close();
    };


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
        var url = window.location.href;

        var hash = url.substring(url.indexOf("#"));

        if(hash && hash.length) {
            toAnchor(hash)
        }
    }

    var offset = null;

    var getViewOffset = function() {
        if(offset === null) {
            offset = $('#topbar-first').length ? $('#topbar-first').height() : 0;
            offset += $('#topbar-second').length ? $('#topbar-second').height() : 0;
            // TODO: Workaround for enterprise edition the offset should be configurable by theme variable!
            offset += $('.space-nav').find('.container-fluid').length ? $('.space-nav').height() : 0;
        }

        return offset;
    }

    var buildIndex = function() {
        var $list = $('<ul class="nav nav-pills nav-stacked">');
        $('.markdown-render').find('h1').each(function() {
            var $h1 = $(this).clone();
            var $anchor = $h1.find('.header-anchor').clone();
            $anchor.show();

            $h1.find('.header-anchor').remove();
            var test = $h1.text();
            $anchor.text($h1.text());

            var $li = $('<li>');
            $anchor.prepend('<i class="fa fa-caret-right"></i>');
            $anchor.on('click', function(evt) {
                evt.preventDefault();
                toAnchor($anchor.attr('href'));
            });
            $li.append($anchor);
            $list.append($li);

        });
        $list.append('<li class="nav-divider"></li>');
        $('.wiki-menu-fixed').prepend($list);
    };

    var toAnchor = function(anchor) {
        $('html, body').animate({
            scrollTop: $(anchor).offset().top - getViewOffset()
        }, 200)
    };

    module.initOnPjaxLoad = true;

    var init = function(pjax) {
        if(!$('.wiki-content').length) {
            return;
        }

        if($('.wiki-page-content').length) {
            setTimeout(buildIndex, 200);
            setTimeout(checkAnchor, 1000);
        }

        $(window).off('scroll.wiki').on('scroll.wiki', function () {
            checkScroll();
        });

        checkScroll();
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

    var unload = function() {
        $(window).off('scroll.wiki');
        offset = null;
    }

    module.export({
        init: init,
        unload: unload,
        SearchDropdown: SearchDropdown,
        CategoryListView: CategoryListView,
        setEditorLink: setEditorLink
    })
});
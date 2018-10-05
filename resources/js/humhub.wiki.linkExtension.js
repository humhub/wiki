humhub.module('wiki.linkExtension', function (module, require, $) {
    var richtext = require('ui.richtext.prosemirror');
    var Widget = require('ui.widget').Widget;
    var modal = require('ui.modal');
    var client = require('client');

    var wiki = require('wiki');

    var markdown = richtext.api.plugin.markdown;
    var menu = richtext.api.menu;
    var commands = richtext.api.commands;
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
        ]
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
        }).show();
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
            addToPreset('wiki', 'wiki', {before: 'link'})
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
    })
});
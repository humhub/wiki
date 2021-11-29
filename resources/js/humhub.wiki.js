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
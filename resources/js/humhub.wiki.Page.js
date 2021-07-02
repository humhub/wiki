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
humhub.module('wiki.Page', function(module, require, $) {
    var Widget = require('ui.widget').Widget;

    /**
     * This widget represents a wiki page and wraps the actual page content.
     */
    var Page = Widget.extend();

    Page.prototype.init = function() {
        var that = this;
        this.$.find('#wiki-page-richtext').on('afterInit', function() {
            that.$.fadeIn('slow');
        });
    };

    module.export = Page;
});
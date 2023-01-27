humhub.module('wiki.ListTable', function(module, require, $) {
    const Widget = require('ui.widget').Widget;

    const ListTable = Widget.extend();

    ListTable.prototype.init = function() {
        this.$.find('.table > tbody > tr > td').on('click', function() {
            const link = $(this).find('a.wiki-page-list-row-title');
            if (link.length) {
                location.href = link.attr('href');
            }
        });
    };

    module.export = ListTable;
});
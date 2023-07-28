humhub.module('wiki.ListTable', function(module, require, $) {
    const Widget = require('ui.widget').Widget;

    const ListTable = Widget.extend();

    ListTable.prototype.init = function() {
        this.$.find('.table > tbody > tr > td').on('click', function(e) {
            if ($(e.target).prop('tagName') === 'A') {
                return;
            }
            const link = $(this).find('.wiki-page-list-row-title a');
            if (link.length) {
                location.href = link.attr('href');
            }
        });
    };

    module.export = ListTable;
});
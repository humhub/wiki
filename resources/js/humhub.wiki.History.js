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
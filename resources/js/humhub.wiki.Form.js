humhub.module('wiki.Form', function(module, require, $) {
    var Widget = require('ui.widget').Widget;
    var wikiView = require('wiki');
    var additions = require('ui.additions');
    var client = require('client');
    var modal = require('ui.modal');

    var editPollingInterval = 5000;
    var editPollingTimer = null;

    /**
     * This widget represents the wiki form
     */
    var Form = Widget.extend();

    Form.prototype.init = function() {
        var that = this;
        this.getRichtext().on('init', function() {
            wikiView.registerStickyElement(that.getRichtextMenu(), that.getRichtext());
        });

        that.$.find('div.checkbox').each(function() {
            var $this = $(this);
            var $checkbox = $this.find('[type=checkbox][title]');

            if($checkbox.length) {
                $this.find('label').addClass('tt').attr('title', $checkbox.attr('title'));
            }

            additions.apply($this, 'tooltip');
        });

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
        checkValidUser();
        startEditPolling();
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

    Form.prototype.openUrlLink = function(evt) {
        var form = this.$;
        form.attr('action', evt.$trigger.data('action-click-url'))
            .submit();
    }

    Form.submit = function () {
        if (editPollingTimer) {
            clearInterval(editPollingTimer);
        }
        $('form[data-ui-widget="wiki.Form"]').submit();
    };

    function pollTimerEditingStatus() {
        var url = document.querySelector('[data-url-editing-timer-update]').getAttribute('data-url-editing-timer-update');

        client.get(url).then(function(response) {
        }).catch(function(e) {
            module.log.error(e, true);
        });
    }

    function startEditPolling() {
        if (editPollingTimer) {
            clearInterval(editPollingTimer);
        }
        editPollingTimer = setInterval(pollTimerEditingStatus, editPollingInterval);
    }

    function checkValidUser() {
        var url = document.querySelector('[data-url-editing-timer-update]').getAttribute('data-url-editing-timer-update');
        client.get(url).then(function(response) {
            if(response.success&&response.conflictingEditing) {
                var options = {
                    'header': response.header,
                    'body': response.body,
                    'confirmText': response.confirmText,
                    'cancelText' : response.cancelText,
                };

                modal.confirm(options).then(function ($confirmed) {
                    if ($confirmed) {
                        client.redirect(response.url);
                    }
                });
            }
        }).catch(function(e) {
            module.log.error(e, true);
        })
    }

    module.export = Form;
});

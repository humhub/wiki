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
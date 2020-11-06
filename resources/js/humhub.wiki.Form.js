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
                    that.hideCategorySelect();
                });
            }

            that.hideCategorySelect();
        });
    };

    Form.prototype.hideCategorySelect = function() {
        if ($('#wikipage-is_category').is(":not(:checked)")) {
            $('.field-wikipage-parent_page_id').show();
        } else {
            $('.field-wikipage-parent_page_id').hide();
        }
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

    module.export = Form;
});
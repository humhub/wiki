humhub.module('wiki.Form', function(module, require, $) {
    var Widget = require('ui.widget').Widget;
    var richtext = require('ui.richtext.prosemirror');
    var wikiView = require('wiki');
    var additions = require('ui.additions');
    var client = require('client');
    var modal = require('ui.modal');

    var editPollingInterval = 5000;
    var editPollingTimer = null;
    var editStatusPollingInterval = 5000; // or another value in ms

    /**
     * This widget represents the wiki form
     */
    var Form = Widget.extend();

    Form.prototype.init = function() {
        var that = this;
        this.getRichtext().on('init', function() {
            wikiView.registerStickyElement(that.getRichtextMenu(), that.getRichtext());
        });

        let placeholders = JSON.parse($('#wikitemplate-placeholders').val() || '[]');
        renderPlaceholderTable(placeholders);

        that.$.find('div.checkbox').each(function() {
            var $this = $(this);
            var $checkbox = $this.find('[type=checkbox][title]');

            if($checkbox.length) {
                $this.find('label').addClass('tt').attr('title', $checkbox.attr('title'));
            }

            additions.apply($this, 'tooltip');
        });

        $('#wikitemplate-is_appendable').on('change', function () {
            if ($(this).is(':checked')) {
                $('#appendable-content-wrapper').show();
            } else {
                $('#appendable-content-wrapper').hide();
            }
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
        setInterval(pollEditingStatus, editStatusPollingInterval);
        pollEditingStatus();
        const editorWidget = Widget.instance('#wikipagerevision-content');
        requireTemplate(editorWidget);
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
        if (document.querySelector('[data-url-editing-timer-update]') == null) {
            if (editPollingTimer) {
                clearInterval(editPollingTimer);
            }
            return;
        }
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
        if(document.querySelector('[data-url-editing-timer-update]') != null)
        {
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
                // module.log.error(e, true);
            })
        }
    }

    function insertContentIntoEditor(editorWidget, content) {
        var view = editorWidget.editor.view;
    
        if (view) {
            var parser = richtext.api.model.DOMParser.fromSchema(view.state.schema);
            var html = content;
            var tempDoc = new DOMParser().parseFromString(html, 'text/html');
            var doc = parser.parse(tempDoc);
    
            var endPos = view.state.doc.content.size;
    
            var transaction = view.state.tr.insert(endPos, doc.content);
            view.dispatch(transaction);
        } else {
            console.warn('ProseMirror view not found');
        }
    }

    function requireTemplate(editorWidget) {
        if(document.querySelector('.ProseMirror .placeholder')) {
            $('#templateSelectModal').modal('show');
        }
        else {
            $('#templateSelectModal .modal-body').empty();
        }
        $('#useTemplateBtn').on('click', function (e) {
            e.preventDefault();
            const $selected = $('#templateSelectDropdown option:selected');
            const fetchUrl = $selected.data('url');
        
            if (!fetchUrl) {
                alert('Please select a template');
                return;
            }
        
            $.get(fetchUrl, function (response) {
                if (response.success) {
                    const content = response.content;
                    const titleTemplate = response.title;
                    const placeholders = JSON.parse(response.placeholders || []);
                    const is_appendable = response.is_appendable;
                    const appendable_content = response.appendable_content;
            
                    if (placeholders.length > 0) {
                        let formHtml = '<form id="templatePlaceholderForm">';
                        placeholders.forEach(ph => {
                            formHtml += `
                                <div class="form-group mb-2">
                                    <label>${ph.description + '(' + ph.key + ')'}</label>
                                    <input class="form-control" name="${ph.key}" value="${ph.default || ''}" required />
                                </div>`;
                        });
                        formHtml += '<button type="submit" class="btn btn-primary mt-2">Insert</button></form>';
            
                        $('#placeholderFormContainer').html(formHtml);
                        $('#templateSelectModal').modal('hide');
                        $('#placeholderModal').modal('show');
            
                        $('#templatePlaceholderForm').on('submit', function (e) {
                            e.preventDefault();
            
                            let filledContent = content;
                            let filledTitle = titleTemplate;
                            const formData = $(this).serializeArray();
                            formData.forEach(field => {
                                const regex = new RegExp('{{\\s*' + field.name + '\\s*}}', 'g');
                                filledContent = filledContent.replace(regex, field.value);
                                filledTitle = filledTitle.replace(regex, field.value);
                            });
                            // Optional: Update the title input if available
                            const $titleInput = $('#wikipage-title');
                            if ($titleInput.length) {
                                $titleInput.val(filledTitle);
                            }
                            insertContentIntoEditor(editorWidget, filledContent);
                            addAppendableContent(is_appendable, appendable_content);
                            $('#templateSelectModal .modal-body').empty();
                            $('#placeholderModal').modal('hide');
                        });
                    } else {
                        insertContentIntoEditor(editorWidget, content);
                        addAppendableContent(is_appendable, appendable_content);
                        $('#templateSelectModal .modal-body').empty();
                        $('#templateSelectModal').modal('hide');
                    }
                } else {
                    alert(response.message || 'Failed to fetch template.');
                }
            });
        });
    
        $('#blankPageBtn').off('click').on('click', function() {
            $('#templateSelectModal .modal-body').empty();
            $('#templateSelectModal').modal('hide');
        });
    }

    Form.prototype.addPlaceholder = function() {
        $('#addPlaceholderModal').modal('show');

        let formHtml = `<form id="newPlaceholderForm">
                            <div class="form-group">
                                <label>Name *</label>
                                <input class="form-control" name="key" required />
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <input class="form-control" name="description" />
                            </div>
                            <div class="form-group">
                                <label>Default Value</label>
                                <input class="form-control" name="default" />
                            </div>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </form>`;

        $('#newPlaceholderFormContainer').html(formHtml);

        $('#newPlaceholderForm').on('submit', function (e) {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(this).entries());
        
            if (!data.key) {
                alert('Name is required');
                return;
            }
        
            let placeholders = [];
            try {
                placeholders = JSON.parse($('#wikitemplate-placeholders').val() || '[]');
            } catch (e) {
                console.warn('Invalid placeholder data');
            }
        
            if (placeholders.some(ph => ph.key === data.key)) {
                alert('A placeholder with this name already exists.');
                return;
            }
        
            placeholders.push({
                key: data.key.trim(),
                description: data.description?.trim() || '',
                default: data.default?.trim() || ''
            });
        
            updatePlaceholderField(placeholders);
            renderPlaceholderTable(placeholders);
            $('#addPlaceholderModal').modal('hide');
        });

        $('#placeholder-table').on('click', '.remove-placeholder', function () {
            const index = $(this).closest('tr').data('index');
            let placeholders = JSON.parse($('#wikitemplate-placeholders').val() || '[]');
        
            placeholders.splice(index, 1);
            updatePlaceholderField(placeholders);
            renderPlaceholderTable(placeholders);
        });
    };

    function updatePlaceholderField(placeholders) {
        $('#wikitemplate-placeholders').val(JSON.stringify(placeholders));
    }

    function renderPlaceholderTable(placeholders) {
        const $tbody = $('#placeholder-table tbody');
        $tbody.empty();
    
        if (!placeholders.length) {
            return;
        }
    
        placeholders.forEach((ph, index) => {
            $tbody.append(`
                <tr data-index="${index}">
                    <td class="text-center">${ph.key}</td>
                    <td class="text-start">${ph.description}</td>
                    <td class="text-center">${ph.default}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-placeholder">Delete</button>
                    </td>
                </tr>
            `);
        });

        $('#placeholder-table').on('click', '.remove-placeholder', function () {
            const index = $(this).closest('tr').data('index');
            let placeholders = JSON.parse($('#wikitemplate-placeholders').val() || '[]');
        
            placeholders.splice(index, 1);
            updatePlaceholderField(placeholders);
            renderPlaceholderTable(placeholders);
        });
    }

    function addAppendableContent(is_appendable, appendable_content) {
        $('#pageeditform-isappendable').val(is_appendable);
        if (is_appendable) {
            $('#pageeditform-appendablecontent').val(appendable_content);
        }
    }

    function pollEditingStatus() {
        const $container = $('[data-url-editing-status]');
        const url = $container.data('url-editing-status');
        if (!url) return;
        client.get(url).then(function (response) {
            if (response.success && response.isEditing) {
                disableSubmit(response.user);
            } else {
                enableSubmit();
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    }

    function disableSubmit(user) {
        $('#append-save-button')
            .prop('disabled', true)
            .addClass('disabled')
            .text(user + ' is editing...');
    }

    function enableSubmit() {
        $('#append-save-button')
            .prop('disabled', false)
            .removeClass('disabled')
            .text('Append');
    }

     
    module.export = Form;
});

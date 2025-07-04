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

        try {
            renderPlaceholderTable();
        } catch (e) {
            console.warn("Error with rendering placeholderTable");
        }

        const $container = $('[data-url-append-content]');
        const appendUrl = $container.data('url-append-content');

        if (appendUrl != null) {
            client.get(appendUrl).then(function (response) {
                let placeholders = [];
    
                try {
                    placeholders = response.placeholders ? JSON.parse(response.placeholders) : [];
                } catch (parseError) {
                    console.error('Failed to parse placeholders JSON:', parseError);
                    placeholders = [];
                }

                if (placeholders.length > 0) {
                    renderAppendablePlaceholderForm(response);
                } else {
                    let content = response.content;
                    const user = response.user;
                    const specialPlaceholders = applySpecialPlaceholders(content, "", user);
                    const editedcontent = specialPlaceholders.content;
    
                    const editorWidget = Widget.instance('#pageappendform-content');
                    if (editorWidget != null) {
                        insertContentIntoEditor(editorWidget, editedcontent);
                    }
                }
            }).catch(err => {
                console.error('Failed to fetch appendable content:', err);
            });
        }

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
                module.log.error(e, true);
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
        
        $('#useTemplateBtn').off('click').on('click', function (e) {
            e.preventDefault();
            const $selected = $('#templateSelectDropdown option:selected');
            const fetchUrl = $selected.data('url');
        
            if (fetchUrl == null) {
                alert('Please select a template');
                return;
            }
        
            $.get(fetchUrl, function (response) {
                if (response.success) {
                    let content = response.content;
                    const titleTemplate = response.title;
                    const placeholders = response.placeholders? JSON.parse(response.placeholders): [];
                    const is_appendable = response.is_appendable;
                    const appendable_content = response.appendable_content;
                    const appendable_content_placeholder = response.appendable_content_placeholder;
                    const user = response.user;
            
                    if (placeholders.length > 0) {
                        let formHtml = '';
                        placeholders.forEach(ph => {
                            formHtml += `
                                <div class="form-group mb-2">
                                    <label>${ph.description + ' (' + ph.key + ')'}</label>
                                    <input class="form-control" name="${ph.key}" value="${ph.default || ''}" required />
                                </div>`;
                        });
                        $('#placeholderFormFields').html(formHtml);
                        $('#templateSelectModal').modal('hide');
                        $('#placeholderModal').modal('show');
            
                        $('#templatePlaceholderForm').on('submit', function (e) {
                            e.preventDefault();
            
                            let filledContent = content;
                            let filledTitle = titleTemplate;
                            const specialPlaceholders = applySpecialPlaceholders(filledContent, filledTitle, user);
                            const formData = $(this).serializeArray();
                            filledContent = replacePlaceholders(specialPlaceholders.content, formData);
                            filledTitle = replacePlaceholders(specialPlaceholders.title, formData);

                            const $titleInput = $('#wikipage-title');
                            if ($titleInput.length != null) {
                                $titleInput.val(filledTitle);
                            }
                            insertContentIntoEditor(editorWidget, filledContent);
                            addAppendableContent(is_appendable, appendable_content, appendable_content_placeholder);
                            $('#templateSelectModal .modal-body').empty();
                            $('#placeholderModal').modal('hide');
                        });
                    } else {
                        const specialPlaceholders = applySpecialPlaceholders(response.content, response.title, response.user);
                        let filledContent = specialPlaceholders.content;
                        let filledTitle = specialPlaceholders.title;
                        const $titleInput = $('#wikipage-title');
                        if ($titleInput.length != null) {
                            $titleInput.val(filledTitle);
                        }
                        insertContentIntoEditor(editorWidget, filledContent);
                        addAppendableContent(is_appendable, appendable_content, appendable_content_placeholder);
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

        if(document.querySelector('.ProseMirror .placeholder') != null) {
            $('#templateSelectModal').modal('show');
        }else {
            $('#templateSelectModal .modal-body').empty();
        }
    }

    function applySpecialPlaceholders(content, title, user) {
        const now = new Date();
    
        const formatDate = (format) => {
            const yyyy = now.getFullYear();
            const mm = String(now.getMonth() + 1).padStart(2, '0');
            const dd = String(now.getDate()).padStart(2, '0');
    
            switch (format) {
                case 'YYYY-DD-MM':
                    return `${yyyy}-${dd}-${mm}`;
                case 'DD.MM.YYYY':
                    return `${dd}.${mm}.${yyyy}`;
                default:
                    return '';
            }
        };
    
        content = content.replace(/{{\s*today\s+YYYY-DD-MM\s*}}/gi, formatDate('YYYY-DD-MM'));
        title = title.replace(/{{\s*today\s+YYYY-DD-MM\s*}}/gi, formatDate('YYYY-DD-MM'));
    
        content = content.replace(/{{\s*today\s+DD\.MM\.YYYY\s*}}/gi, formatDate('DD.MM.YYYY'));
        title = title.replace(/{{\s*today\s+DD\.MM\.YYYY\s*}}/gi, formatDate('DD.MM.YYYY'));
        
        content = content.replace(/{{\s*author\s*}}/gi, `<span data-mention="${user.guid}" contenteditable="false" style="display:inline-block" draggable="true"><span style="display:block">${user.displayName}</span></span>`);
        title = title.replace(/{{\s*author\s*}}/gi, user.displayName);
        return { content, title };
    }
    

    Form.prototype.addPlaceholder = function() {
        let translation = $('#newPlaceholderFormContainer').data('translation');
        console.log(translation);
        translation = JSON.parse(translation || '{}');
        console.log(translation);
        const placeholderFormHtml = `<form id="newPlaceholderForm">
                                    <div class="form-group">
                                        <label>`+ document.getElementById('name').textContent.trim()+ ` *</label>
                                        <input class="form-control" name="key" required />
                                    </div>
                                    <div class="form-group">
                                        <label>`+ document.getElementById('description').textContent.trim()+ `</label>
                                        <input class="form-control" name="description" />
                                    </div>
                                    <div class="form-group">
                                        <label>`+ document.getElementById('default').textContent.trim()+ `</label>
                                        <input class="form-control" name="default" />
                                    </div>
                                    <div class="form-check my-2">
                                        <input type="checkbox" class="form-check-input" id="isAppendable" name="isAppendable">
                                        <label class="form-check-label" for="isAppendable">`+$('#newPlaceholderFormContainer').data('translation-type')+`</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">`+$('#newPlaceholderFormContainer').data('translation-add')+`</button>
                                </form>`;

        $('#addPlaceholderModal').modal('show');

        $('#newPlaceholderFormContainer').html(placeholderFormHtml);

        $('#newPlaceholderForm').on('submit', function (e) {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(this).entries());
        
            if (data.key == null) {
                alert('Name is required');
                return;
            }

            const isAppendable = !!data.isAppendable;
            const fieldId = isAppendable ? '#wikitemplate-appendable_content_placeholder' : '#wikitemplate-placeholders';
        
            let placeholders = [];
            try {
                placeholders = JSON.parse($(fieldId).val() || '[]');
            } catch (e) {
                console.warn('Invalid placeholder data');
            }
        
            if (placeholders.some(ph => ph.key.toLowerCase() === data.key.toLowerCase())) {
                alert('A placeholder with this name already exists.');
                return;
            }
        
            placeholders.push({
                key: data.key.trim(),
                description: data.description?.trim() || '',
                default: data.default?.trim() || '',
                type: isAppendable ? 'appendable' : 'normal'
            });
            
            $(fieldId).val(JSON.stringify(placeholders));
            renderPlaceholderTable();
            $('#addPlaceholderModal').modal('hide');
        });
    };

    function renderPlaceholderTable() {
        const $tbody = $('#placeholder-table tbody');
        $tbody.empty();

        const normal = JSON.parse($('#wikitemplate-placeholders').val() || '[]');
        const appendable = JSON.parse($('#wikitemplate-appendable_content_placeholder').val() || '[]');
    
        normal.forEach((ph, index) => {
            $tbody.append(`
                <tr data-index="${index}" data-type="normal">
                    <td class="text-center">${escapeHtml(ph.key)}</td>
                    <td class="text-start">
                        <span class="placeholder-description">${escapeHtml(ph.description)}</span>
                    </td>
                    <td class="text-center">${escapeHtml(ph.default)}</td>
                    <td class="text-center">Normal</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-placeholder">Delete</button>
                    </td>
                </tr>
            `);
        });

        appendable.forEach((ph, index) => {
            $tbody.append(`
                <tr data-index="${index}" data-type="appendable">
                    <td class="text-center">${escapeHtml(ph.key)}</td>
                    <td class="text-start">
                        <span class="placeholder-description">${escapeHtml(ph.description)}</span>
                    </td>
                    <td class="text-center">${escapeHtml(ph.default)}</td>
                    <td class="text-center">Appendable</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-placeholder">Delete</button>
                    </td>
                </tr>
            `);
        });

        $('#placeholder-table .remove-placeholder').off('click').on('click', function () {
            const $row = $(this).closest('tr')
            const index = $row.data('index');
            const type = $row.data('type');
            const fieldId = type === 'appendable' ? '#wikitemplate-appendable_content_placeholder' : '#wikitemplate-placeholders';
            let placeholders = JSON.parse($(fieldId).val() || '[]');
            placeholders.splice(index, 1);
            $(fieldId).val(JSON.stringify(placeholders));
            renderPlaceholderTable();
        });

    }

    function replacePlaceholders(content, replacements) {
        replacements.forEach(field => {
            const key = field.name.toLowerCase();
            const regex = new RegExp('{{\\s*' + key + '\\s*}}', 'gi');
            content = content.replace(regex, field.value);
        });
        return content;
    }

    function addAppendableContent(is_appendable, appendable_content, appendable_content_placeholder) {
        $('#pageeditform-isappendable').val(is_appendable);
        if (is_appendable) {
            $('#pageeditform-appendablecontent').val(appendable_content);
            $('#pageeditform-appendablecontentplaceholder').val(appendable_content_placeholder);
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
        const $btn = $('#append-save-button');
        const originalLabel = $btn.data('original-label') || 'Append';

        $btn
            .prop('disabled', false)
            .removeClass('disabled')
            .text(originalLabel);
    }

    function renderAppendablePlaceholderForm(response) {
        const placeholders = response.placeholders? JSON.parse(response.placeholders): [];
        const content = response.content;
        const user = response.user;

        let formHtml = '';
        placeholders.forEach(ph => {
            formHtml += `<div class="form-group mb-2">
                            <label>${ph.description + ' (' + ph.key + ')'}</label>
                            <input class="form-control" name="${ph.key}" value="${ph.default || ''}" required />
                        </div>`;
        });
        $('#appendablePlaceholderFormFields').html(formHtml);
        $('#appendablePlaceholderModal').modal('show');

        $('#appendablePlaceholderForm').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serializeArray();
            const specialPlaceholders = applySpecialPlaceholders(content, "", user);
            editedcontent = replacePlaceholders(specialPlaceholders.content, formData);
            const editorWidget = Widget.instance('#pageappendform-content');
            if (editorWidget != null){
                insertContentIntoEditor(editorWidget, editedcontent);
            }

            $('#appendablePlaceholderModal').modal('hide');

        })
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    $(document).on('pjax:send', function () {
        // Remove modals related to the Wiki module
        $('#templateSelectModal, #addPlaceholderModal, #placeholderModal, #appendablePlaceholderModal').remove();
    
        // Remove any stuck Bootstrap backdrop and cleanup body scroll lock
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });
     
    module.export = Form;
});

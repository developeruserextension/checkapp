/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'tinymce',
    'mage/adminhtml/wysiwyg/tiny_mce/html5-schema',
    'mage/translate',
    'prototype',
    'mage/adminhtml/events',
    'mage/adminhtml/browser'
], function(jQuery, _, tinyMCE, html5Schema) {

    rfqTinyMceWysiwygSetup = Class.create();

    rfqTinyMceWysiwygSetup.prototype = {
        mediaBrowserOpener: null,
        mediaBrowserTargetElementId: null,

        initialize: function(htmlId, config) {
            if (config.baseStaticUrl && config.baseStaticDefaultUrl) {
                tinyMCE.baseURL = tinyMCE.baseURL.replace(config.baseStaticUrl, config.baseStaticDefaultUrl);
            }
            this.id = htmlId;
            this.config = config;
            this.schema = config.schema || html5Schema;

            _.bindAll(this, 'beforeSetContent', 'saveContent', 'onChangeContent', 'openFileBrowser', 'updateTextArea');

            varienGlobalEvents.attachEventHandler('tinymceChange', this.onChangeContent);
            varienGlobalEvents.attachEventHandler('tinymceBeforeSetContent', this.beforeSetContent);
            varienGlobalEvents.attachEventHandler('tinymceSaveContent', this.saveContent);

            if (typeof tinyMceEditors == 'undefined') {
                tinyMceEditors = $H({});
            }

            tinyMceEditors.set(this.id, this);
        },

        setup: function(mode) {
            if (this.config.widget_plugin_src) {
                tinyMCE.PluginManager.load('magentowidget', this.config.widget_plugin_src);
            }

            if (this.config.plugins) {
                this.config.plugins.each(function(plugin) {
                    tinyMCE.PluginManager.load(plugin.name, plugin.src);
                });
            }

            if (jQuery.isReady) {
                tinyMCE.dom.Event.domLoaded = true;
            }

            tinyMCE.init(this.getSettings(mode));
        },

        getSettings: function(mode) {
            var plugins = 'inlinepopups,safari,pagebreak,style,layer,table,advhr,advimage,emotions,iespell,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
                self = this;

            if (this.config.widget_plugin_src) {
                plugins = 'magentowidget,' + plugins;
            }

            var magentoPluginsOptions = $H({});
            var magentoPlugins = '';

            if (this.config.plugins) {
                this.config.plugins.each(function(plugin) {
                    magentoPlugins = plugin.name + ',' + magentoPlugins;
                    magentoPluginsOptions.set(plugin.name, plugin.options);
                });
                if (magentoPlugins) {
                    plugins = '-' + magentoPlugins + plugins;
                }
            }

            var settings = {
                mode: (mode != undefined ? mode : 'none'),
                elements: this.id,
                theme: 'advanced',
                plugins: plugins,
                theme_advanced_buttons1: magentoPlugins + 'magentowidget,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect',
                theme_advanced_buttons2: 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor',
                theme_advanced_buttons3: 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,ltr,rtl,|,fullscreen',
                theme_advanced_buttons4: 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak',
                theme_advanced_toolbar_location: 'top',
                theme_advanced_toolbar_align: 'left',
                theme_advanced_statusbar_location: 'bottom',
                theme_advanced_resizing: true,
                theme_advanced_resize_horizontal: false,
                convert_urls: false,
                relative_urls: false,
                content_css: this.config.content_css,
                custom_popup_css: this.config.popup_css,
                magentowidget_url: this.config.widget_window_url,
                magentoPluginsOptions: magentoPluginsOptions,
                doctype: '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
                setup: function(ed){
                    ed.onInit.add(self.onEditorInit.bind(self));

                    ed.onSubmit.add(function(ed, e) {
                        varienGlobalEvents.fireEvent('tinymceSubmit', e);
                    });

                    ed.onPaste.add(function(ed, e, o) {
                        varienGlobalEvents.fireEvent('tinymcePaste', o);
                    });

                    ed.onBeforeSetContent.add(function(ed, o) {
                        varienGlobalEvents.fireEvent('tinymceBeforeSetContent', o);
                    });

                    ed.onSetContent.add(function(ed, o) {
                        varienGlobalEvents.fireEvent('tinymceSetContent', o);
                    });

                    ed.onSaveContent.add(function(ed, o) {
                        varienGlobalEvents.fireEvent('tinymceSaveContent', o);
                    });

                    var onChange = function(ed, l) {
                        varienGlobalEvents.fireEvent('tinymceChange', l);
                    };

                    ed.onChange.add(onChange);
                    ed.onKeyUp.add(onChange);

                    ed.onExecCommand.add(function(ed, cmd, ui, val) {
                        varienGlobalEvents.fireEvent('tinymceExecCommand', cmd);
                    });
                }
            };

            // Set the document base URL
            if (this.config.document_base_url) {
                settings.document_base_url = this.config.document_base_url;
            }

            if (this.config.files_browser_window_url) {
                settings.file_browser_callback = function(fieldName, url, objectType, w) {
                    varienGlobalEvents.fireEvent("open_browser_callback", {
                        win: w,
                        type: objectType,
                        field: fieldName
                    });
                };
            }

            if (this.config.width) {
                settings.width = this.config.width;
            }

            if (this.config.height) {
                settings.height = this.config.height;
            }

            if (this.config.settings) {
                Object.extend(settings, this.config.settings)
            }

            return settings;
        },
        applySchema: function (editor) {
            var schema      = editor.schema,
                schemaData  = this.schema,
                makeMap     = tinyMCE.makeMap;

            jQuery.extend(true, {
                nonEmpty: schema.getNonEmptyElements(),
                boolAttrs: schema.getBoolAttrs(),
                whiteSpace: schema.getWhiteSpaceElements(),
                shortEnded: schema.getShortEndedElements(),
                selfClosing: schema.getSelfClosingElements(),
                blockElements: schema.getBlockElements()
            }, {
                nonEmpty: makeMap(schemaData.nonEmpty),
                boolAttrs: makeMap(schemaData.boolAttrs),
                whiteSpace: makeMap(schemaData.whiteSpace),
                shortEnded: makeMap(schemaData.shortEnded),
                selfClosing: makeMap(schemaData.selfClosing),
                blockElements: makeMap(schemaData.blockElements)
            });
        },
        openFileBrowser: function(o) {
            var typeTitle,
                storeId = this.config.store_id !== null ? this.config.store_id : 0,
                frameDialog = jQuery(o.win.frameElement).parents('[role="dialog"]'),
                wUrl = this.config.files_browser_window_url +
                'target_element_id/' + this.id + '/' +
                'store/' + storeId + '/';

            this.mediaBrowserOpener = o.win;
            this.mediaBrowserTargetElementId = o.field;

            if (typeof(o.type) != 'undefined' && o.type != "") {
                typeTitle = 'image' == o.type ? this.translate('Insert Image...') : this.translate('Insert Media...');
                wUrl = wUrl + "type/" + o.type + "/";
            } else {
                typeTitle = this.translate('Insert File...');
            }
            frameDialog = jQuery('[role="dialog"]');
            frameDialog.hide();
            jQuery('#mceModalBlocker').hide();

            MediabrowserUtility.openDialog(wUrl, false, false, typeTitle, {
                closed: function() {
                    jQuery(document).find('[role="dialog"]').show();
                    jQuery('#mceModalBlocker').show();
                }
            });
        },

        translate: function(string) {
            return jQuery.mage.__ ? jQuery.mage.__(string) : string;
        },

        getMediaBrowserOpener: function() {
            return this.mediaBrowserOpener;
        },

        getMediaBrowserTargetElementId: function() {
            return this.mediaBrowserTargetElementId;
        },

        getToggleButton: function() {
            return $('toggle' + this.id);
        },

        getPluginButtons: function() {
            return $$('#buttons' + this.id + ' > button.plugin');
        },

        turnOn: function(mode) {
            this.closePopups();

            this.setup(mode);
            
            tinyMCE.execCommand('mceAddControl', false, this.id);
            
            this.getPluginButtons().each(function(e) {
                e.hide();
            });
            
            return this;
        },

        turnOff: function() {
            this.closePopups();
            
            tinyMCE.execCommand('mceRemoveControl', false, this.id);
            
            this.getPluginButtons().each(function(e) {
                e.show();
            });

            return this;
        },

        closePopups: function() {
            if (typeof closeEditorPopup == 'function') {
                // close all popups to avoid problems with updating parent content area
                closeEditorPopup('widget_window' + this.id);
                closeEditorPopup('browser_window' + this.id);
            }
        },

        toggle: function() {
            if (!tinyMCE.get(this.id)) {
                this.turnOn();
                return true;
            } else {
                this.turnOff();
                return false;
            }
        },

        onEditorInit: function (editor) {
            this.applySchema(editor);
        },

        onFormValidation: function() {
            if (tinyMCE.get(this.id)) {
                $(this.id).value = tinyMCE.get(this.id).getContent();
            }
        },

        onChangeContent: function() {
            // Add "changed" to tab class if it exists
            if (tinyMCE.get(this.id)) {
                jQuery('#' + this.id).val(tinyMCE.get(this.id).getContent()).trigger('change');
            }
            if (this.config.tab_id) {
                var tab = $$('a[id$=' + this.config.tab_id + ']')[0];
                if ($(tab) != undefined && $(tab).hasClassName('tab-item-link')) {
                    $(tab).addClassName('changed');
                }
            }
        },

        // retrieve directives URL with substituted directive value
        makeDirectiveUrl: function(directive) {
            return this.config.directives_url.replace('directive', 'directive/___directive/' + directive);
        },

        encodeDirectives: function(content) {
            // collect all HTML tags with attributes that contain directives
            return content.gsub(/<([a-z0-9\-\_]+.+?)([a-z0-9\-\_]+=".*?\{\{.+?\}\}.*?".+?)>/i, function(match) {
                var attributesString = match[2];
                // process tag attributes string
                attributesString = attributesString.gsub(/([a-z0-9\-\_]+)="(.*?)(\{\{.+?\}\})(.*?)"/i, function(m) {
                    return m[1] + '="' + m[2] + this.makeDirectiveUrl(Base64.mageEncode(m[3])) + m[4] + '"';
                }.bind(this));

                return '<' + match[1] + attributesString + '>';

            }.bind(this));
        },

        encodeWidgets: function(content) {
            return content.gsub(/\{\{widget(.*?)\}\}/i, function(match) {
                var attributes = this.parseAttributesString(match[1]);
                if (attributes.type) {
                    attributes.type = attributes.type.replace(/\\\\/g, "\\");
                    var imageSrc = this.config.widget_placeholders[attributes.type];
                    var imageHtml = '<img';
                    imageHtml += ' id="' + Base64.idEncode(match[0]) + '"';
                    imageHtml += ' src="' + imageSrc + '"';
                    imageHtml += ' title="' + match[0].replace(/\{\{/g, '{').replace(/\}\}/g, '}').replace(/\"/g, '&quot;') + '"';
                    imageHtml += '>';

                    return imageHtml;
                }
            }.bind(this));
        },

        decodeDirectives: function(content) {
            // escape special chars in directives url to use it in regular expression
            var url = this.makeDirectiveUrl('%directive%').replace(/([$^.?*!+:=()\[\]{}|\\])/g, '\\$1');
            var reg = new RegExp(url.replace('%directive%', '([a-zA-Z0-9,_-]+)'));
            return content.gsub(reg, function(match) {
                return Base64.mageDecode(match[1]);
            }.bind(this));
        },

        decodeWidgets: function(content) {
            return content.gsub(/<img([^>]+id=\"[^>]+)>/i, function(match) {
                var attributes = this.parseAttributesString(match[1]);
                if (attributes.id) {
                    var widgetCode = Base64.idDecode(attributes.id);
                    if (widgetCode.indexOf('{{widget') != -1) {
                        return widgetCode;
                    }
                    return match[0];
                }
                return match[0];
            }.bind(this));
        },

        parseAttributesString: function(attributes) {
            var result = {};
            attributes.gsub(/(\w+)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|([^>\s]+)))?/, function(match) {
                result[match[1]] = match[2];
            });
            return result;
        },

         updateTextArea: function () {
            var editor = tinyMCE.get(this.id),
                content;

            if (!editor) {
                return;
            }

            content = editor.getContent();
            content = this.decodeContent(content);

            jQuery('#' + this.id).val(content).trigger('change');
        },
        decodeContent: function (content) {
            var result = content;

            if (this.config.add_widgets) {
                result = this.decodeWidgets(result);
                result = this.decodeDirectives(result);
            } else if (this.config.add_directives) {
                result = this.decodeDirectives(result);
            }

            if(result!=''){
                result = result.replace(/&nbsp;/g, ' ');
            }

            return result;
        },
        encodeContent: function (content) {
            var result = content;

            if (this.config.add_widgets) {
                result = this.encodeWidgets(result);
                result = this.encodeDirectives(result);
            } else if (this.config.add_directives) {
                result = this.encodeDirectives(result);
            }

            return result;
        },
        beforeSetContent: function(o){
            o.content = this.encodeContent(o.content);
        },

        saveContent: function(o) {
            o.content = this.decodeContent(o.content);
        }
    };

});
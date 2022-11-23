humhub.module('wiki.Sidebar', function(module, require, $) {
    const Widget = require('ui.widget').Widget;

    const Sidebar = Widget.extend();

    Sidebar.prototype.init = function() {
        const that = this;
        const sidebar = this.$;
        const wrapper = sidebar.parent();
        const content = sidebar.next();
        const padding = sidebar.outerWidth() - sidebar.width() + content.outerWidth() - content.width();

        sidebar.resizable({
            minWidth: wrapper.width() * 0.25,
            maxWidth: wrapper.width() * 0.75,
            handles: 'e',
            resize: function() {
                content.width(wrapper.width() - sidebar.width() - padding);
            },
            stop: function() {
                const sidebarWidth = (sidebar.outerWidth() / wrapper.outerWidth() * 100).toFixed(2);
                that.setWidth(sidebarWidth);
                that.setCache(sidebarWidth);
            }
        });
    }

    Sidebar.prototype.setWidth = function(sidebarWidth) {
        if (sidebarWidth) {
            this.$.css('width', sidebarWidth + '%');
            this.$.next().css('width', (100 - sidebarWidth) + '%');
        }
    }

    Sidebar.prototype.getCache = function() {
        let cache = localStorage.getItem(this.cacheName());
        return  cache ? JSON.parse(cache) : {};
    }

    Sidebar.prototype.setCache = function(sidebarWidth) {
        const cache = this.getCache();
        cache[this.key()] = sidebarWidth;

        localStorage.setItem(this.cacheName(), JSON.stringify(cache));
    }

    Sidebar.prototype.cacheName = function() {
        return this.$.data('resizable-key');
    }

    Sidebar.prototype.key = function() {
        return this.$.data('container-id');
    }

    module.export = Sidebar;
});
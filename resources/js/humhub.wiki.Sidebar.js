humhub.module('wiki.Sidebar', function(module, require, $) {
    const Widget = require('ui.widget').Widget;

    const Sidebar = Widget.extend();

    // Same value as $wiki-column-min-width in humhub.wiki.scss
    const MIN_COLUMN_WIDTH = 320;

    Sidebar.prototype.init = function() {
        const that = this;
        const sidebar = this.$;
        const wrapper = sidebar.parent();
        const content = sidebar.next();
        const padding = sidebar.outerWidth() - sidebar.width() + content.outerWidth() - content.width();

        sidebar.resizable({
            minWidth: Math.max(wrapper.width() * 0.25, MIN_COLUMN_WIDTH),
            maxWidth: Math.min(wrapper.width() * 0.75, wrapper.width() - MIN_COLUMN_WIDTH),
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
        sidebarWidth = parseFloat(sidebarWidth);
        if (isFinite(sidebarWidth)) {
            // Keep both blocks in a usable proportion, same limits as the resize handle
            sidebarWidth = Math.min(75, Math.max(25, sidebarWidth));
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
pimcore.registerNS("pimcore.safeDelete.elementNavigator");

pimcore.safeDelete.elementNavigator = Class.create({

    initialize: function () {
        this.registerEvents();
    },

    registerEvents: function () {
        Ext.onReady(this.onReady.bind(this));
        window.addEventListener("load", this.init.bind(this));
    },

    onReady: function () {
        if (!this.urlHasSearchParameters()) {
            return;
        }

        this.setPointerEvents(false);
    },

    init: function () {
        if (!this.urlHasSearchParameters()) {
            return;
        }

        this.waitForFocus(this.openElementFromUrl.bind(this));
    },

    getSearchParameters: function () {
        return Object.fromEntries(
            new URLSearchParams(window.location.search)
        );
    },

    urlHasSearchParameters: function () {
        const searchParams = this.getSearchParameters();

        return searchParams.elementId && searchParams.type;
    },

    setPointerEvents: function (enabled) {
        document.documentElement.style.pointerEvents = enabled
            ? "auto"
            : "none";
    },

    waitForFocus: function (callback, interval = 100) {
        if (document.hasFocus()) {
            callback();
            return;
        }

        setTimeout(
            () => this.waitForFocus(callback, interval),
            interval
        );
    },

    waitForActiveTab: function (callback, interval = 100) {
        const tabPanel = Ext.getCmp("pimcore_panel_tabs");
        const activeTab = tabPanel?.getActiveTab();

        if (activeTab) {
            callback();
            return;
        }

        setTimeout(
            () => this.waitForActiveTab(callback, interval),
            interval
        );
    },

    openElementFromUrl: function () {
        const searchParams = this.getSearchParameters();

        let success = false;

        Ext.Ajax.request({
            async: false,
            url: Routing.generate("validate_parameter_values"),
            method: "GET",
            params: {
                id: searchParams.elementId,
                type: searchParams.type
            },

            success: function (response) {
                const result = JSON.parse(response.responseText);

                success = result.success;

                eval(result.result);
            }
        });

        if (!success) {
            this.setPointerEvents(true);
            return;
        }

        this.waitForActiveTab(function () {
            this.setPointerEvents(true);
        }.bind(this));
    }
});

new pimcore.safeDelete.elementNavigator();
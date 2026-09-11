function getSearchParameters() {
    return Object.fromEntries(new URLSearchParams(window.location.search));
}

function setPointerEvents(enabled) {
    document.documentElement.style.pointerEvents = enabled ? "auto" : "none";
}

function waitForFocus(callback, interval = 100) {
    if (document.hasFocus()) {
        callback();
        return;
    }

    setTimeout(() => waitForFocus(callback, interval), interval);
}

function waitForActiveTab(callback, interval = 100) {
    const tabPanel = Ext.getCmp("pimcore_panel_tabs");
    const activeTab = tabPanel?.getActiveTab();

    if (activeTab) {
        callback();
        return;
    }

    setTimeout(() => waitForActiveTab(callback, interval), interval);
}

function openElementFromUrl() {
    const { elementId, type } = getSearchParameters();

    if (!elementId) {
        return;
    }

    let success = false;

    Ext.Ajax.request({
        async: false,
        url: Routing.generate("check_parameter"),
        method: "GET",
        params: {
            id: elementId,
            type: type
        },

        success: function (response) {
            const result = JSON.parse(response.responseText);

            success = result.success;

            eval(result.result);
        }
    });

    if (!success) {
        setPointerEvents(true);
        return;
    }

    waitForActiveTab(function () {
        setPointerEvents(true);
    });
}

function init() {
    const { elementId } = getSearchParameters();

    if (!elementId) {
        return;
    }

    waitForFocus(openElementFromUrl);
}

Ext.onReady(() => {
    const { elementId } = getSearchParameters();

    if (elementId) {
        setPointerEvents(false);
    }
});

window.addEventListener("load", init);
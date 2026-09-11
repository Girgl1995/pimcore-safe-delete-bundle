pimcore.elementservice.deleteElementCheckDependencyComplete = function (window, res, options) {
    try {
        const message = buildDeleteMessage(res);

        if (!res.hasDependencies) {
            showDeleteConfirmation(window, res, options, message);
            return;
        }

        const includeChildren = getincludeChildren();

        const safeDeletePanel = new pimcore.object.safeDeletePanel(
            res,
            options,
            message,
            includeChildren
        );

        safeDeletePanel.getLayout();
    } catch (error) {
        console.error("Error while checking delete dependencies:", error);
    }
};

function getincludeChildren() {
    let includeChildren = false;

    Ext.Ajax.request({
        async: false,
        url: Routing.generate("check_config"),
        method: "GET",

        success: function (response) {
            const result = Ext.decode(response.responseText);
            includeChildren = Boolean(result.include_children);
        }
    });

    return includeChildren;
}

function showDeleteConfirmation(window, res, options, message) {
    Ext.MessageBox.show({
        title: t("delete"),
        msg: message,
        buttons: Ext.Msg.OKCANCEL,
        icon: Ext.MessageBox.INFO,
        fn: pimcore.elementservice.deleteElementFromServer.bind(
            window,
            res,
            options
        )
    });
}

function buildDeleteMessage(res) {
    const firstItem = res.itemResults?.[0];
    const messageParts = [];

    messageParts.push(buildBaseDeleteMessage(res));
    messageParts.push(buildElementMessage(res, firstItem));

    if (res.hasDependencies) {
        messageParts.push(t("delete_message_dependencies"));
    }

    if (res.children > 100) {
        messageParts.push(
            `<b>${t("too_many_children_for_recyclebin")}</b>`
        );
    }

    if (firstItem?.type === "folder") {
        messageParts.push(
            `<b>${t("delete_entire_folder_question")}</b>`
        );
    }

    return messageParts
        .filter(Boolean)
        .join("<br />");
}

function buildBaseDeleteMessage(res) {
    if (!res.batchDelete) {
        return t("delete_message");
    }

    const itemResults = res.itemResults || [];
    let message = sprintf(
        t("delete_message_batch"),
        itemResults.length
    );

    if (itemResults.length === 0) {
        return message;
    }

    const items = itemResults
        .map(item => {
            return (
                "<li>" +
                htmlspecialchars(item.path) +
                "<b>" +
                htmlspecialchars(item.key) +
                "</b></li>"
            );
        })
        .join("");

    return message + "<br /><div><ul>" + items + "</ul></div>";
}

function buildElementMessage(res, firstItem) {
    if (!res.elementKey || !firstItem) {
        return "";
    }

    return (
        "<div><ul>" +
        "<li>" +
        htmlspecialchars(firstItem.path) +
        "<b>" +
        htmlspecialchars(res.elementKey) +
        "</b></li>" +
        "</ul></div>"
    );
}

pimcore.registerNS("pimcore.object.safeDeletePanel");

pimcore.object.safeDeletePanel = Class.create({

    res: {},
    options: {},
    message: "",
    includeChildren: false,

    form: null,
    window: null,

    initialize: function (res, options, message, includeChildren) {
        this.res = res;
        this.options = options;
        this.message = message;
        this.includeChildren = includeChildren;
    },

    buildForm: function () {
        if (this.form) {
            return this.form;
        }

        this.form = Ext.create("Ext.form.FormPanel", {
            bodyPadding: 10,

            layout: {
                type: "vbox",
                align: "stretch"
            },

            items: [
                this.buildMessagePanel(),
                this.buildDependencyTabs()
            ]
        });

        return this.form;
    },

    buildMessagePanel: function () {
        return {
            xtype: "panel",
            flex: 0,
            height: 130,
            autoScroll: true,
            border: true,

            bodyStyle: {
                padding: "5px 10px"
            },

            style: {
                margin: "10px 0"
            },

            html: this.message
        };
    },

    buildDependencyTabs: function () {
        return {
            xtype: "tabpanel",
            flex: 1,
            border: false,
            plain: true,

            items: this.buildTabs()
        };
    },

    buildTabs: function () {
        return this.buildDependencyStores().map(function (store) {
            const dependencyPanel = new pimcore.object.dependencyPanel(
                store
            );

            let icon = '';
            let iconCls = '';
            if(store.config.type === 'object') {
                icon = store.config.icon;
            } else {
                iconCls = store.config.icon
            }

            return {
                title: this.getTabTitle(store),
                layout: "fit",
                border: false,
                icon: icon,
                iconCls: iconCls,

                items: [
                    dependencyPanel.buildPanel()
                ]
            };
        }.bind(this));
    },

    buildDependencyStores: function () {
        const dependencies = new pimcore.object.dependenciesLoader(
            this.res.itemResults,
            this.includeChildren,
            this.options.elementType
        );

        return dependencies.loadDependencies();
    },

    getTabTitle: function (store) {
        return htmlspecialchars(store.config.key);
    },

    handleDelete: function () {
        pimcore.elementservice.deleteElementFromServer(
            this.res,
            this.options,
            "ok"
        );

        this.close();
    },

    handleCancel: function () {
        this.close();
    },

    close: function () {
        if (this.window) {
            this.window.close();
        }
    },

    getLayout: function () {
        if (!this.window) {
            this.window = new Ext.Window({
                title: t("safe_delete"),
                width: 700,
                height: 600,

                border: false,
                modal: true,
                layout: "fit",

                items: [
                    this.buildForm()
                ],

                bbar: this.buildToolbar()
            });
        }

        this.window.show();

        return this.window;
    },

    buildToolbar: function () {
        return [
            "->",
            {
                xtype: "button",
                text: t("ok"),
                iconCls: "pimcore_icon_save",
                handler: this.handleDelete.bind(this)
            },

            {
                xtype: "button",
                text: t("cancel"),
                iconCls: "pimcore_icon_cancel",
                handler: this.handleCancel.bind(this)
            }
        ];
    }

});

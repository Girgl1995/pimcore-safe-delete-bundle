pimcore.registerNS("pimcore.object.dependencyPanel");

pimcore.object.dependencyPanel = Class.create({

    store: null,
    form: null,
    dependencyList: null,

    initialize: function (store) {
        this.store = store;
    },

    buildPanel: function () {
        if (this.form) {
            return this.form;
        }

        this.dependencyList = this.buildDependencyGrid();

        this.form = Ext.create("Ext.form.FormPanel", {
            bodyPadding: 10,

            layout: {
                type: "vbox",
                align: "stretch"
            },

            items: [
                this.buildObjectHeader(),
                this.buildDependencyFieldset()
            ]
        });

        return this.form;
    },

    buildObjectHeader: function () {
        const config = this.store.config;

        return {
            xtype: "container",

            layout: {
                type: "hbox",
                pack: "left"
            },

            cls: "object-path-scroll-container",
            scrollable: "x",

            items: [
                this.buildOpenObjectButton(),

                {
                    xtype: "label",
                    text: "Path:",
                    cls: "object-path-label__key",
                },

                {
                    xtype: "label",
                    text: config.path,
                    cls: "object-path-label__path",
                },

                {
                    xtype: "label",
                    text: config.key,
                    cls: "object-path-label__value",
                }
            ]
        };
    },

    buildOpenObjectButton: function () {
        return {
            xtype: "button",
            text: t("open_object"),
            cls: "open-object-button",
            iconCls: "pimcore_icon_open",
            scope: this,
            handler: this.openElement.bind(this)
        };
    },

    buildDependencyFieldset: function () {
        return {
            xtype: "fieldset",
            title: t("required_by"),
            flex: 1,
            layout: "fit",

            items: [
                this.dependencyList
            ]
        };
    },

    buildDependencyGrid: function () {
        return Ext.create("Ext.grid.Panel", {
            store: this.store,

            columns: {
                defaults: {
                    sortable: false
                },

                items: this.getColumns()
            },

            width: "100%",
            height: "100%",
            forceFit: true,

            cls: "multihref_field",
            componentCls: "object_field",
            bodyCssClass: "pimcore_object_tag_multihref",

            border: true,
        });
    },

    getColumns: function () {
        return [
            this.buildColumn(t("id"), "id", 45),
            this.buildColumn(t("name"), "name", 80),
            this.buildColumn(t("class"), "subtype", 80),
            this.buildColumn(t("type"), "type", 80),
            this.buildColumn(t("path"), "path", 80),

            {
                xtype: "actioncolumn",
                menuText: t("open"),
                width: 40,

                items: [
                    {
                        tooltip: t("open"),
                        icon: "/bundles/pimcoreadmin/img/flat-color-icons/open_file.svg",
                        handler: this.openDependencyElement.bind(this)
                    }
                ]
            }
        ];
    },

    buildColumn: function (text, dataIndex, width) {
        return {
            text: text,
            dataIndex: dataIndex,
            width: width,

            filter: {
                type: "list"
            }
        };
    },

    openElement: function () {
        const config = this.store.config;

        this.openAdminElement(
            config.id,
            config.type
        );
    },

    openDependencyElement: function (grid, rowIndex) {
        const record = grid.getStore().getAt(rowIndex);

        if (!record) {
            return;
        }

        this.openAdminElement(
            record.get("id"),
            record.get("type")
        );
    },

    openAdminElement: function (id, type) {
        const url = new URL("/admin/", window.location.origin);

        url.searchParams.set("elementId", id);
        url.searchParams.set("type", type);

        window.open(url.toString());
    },
});

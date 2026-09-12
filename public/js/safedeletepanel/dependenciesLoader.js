pimcore.registerNS("pimcore.object.dependenciesLoader");

pimcore.object.dependenciesLoader = Class.create({

    selected_items: {},
    includeChildren: false,
    elementType: '',
    results: {},

    initialize: function (selected_items, includeChildren, elementType) {
        this.selected_items = selected_items;
        this.includeChildren = includeChildren;
        this.elementType = elementType;
    },

    loadDependencies: function () {
        Ext.Ajax.request({
            async: false,
            url: Routing.generate("get_element_dependencies"),
            method: "GET",

            params: {
                selected_items: JSON.stringify(this.selected_items),
                include_children: this.includeChildren,
                element_type: this.elementType
            },

            success: function (response) {
                const result = Ext.decode(response.responseText);

                this.results = result.result
                    ? JSON.parse(result.result)
                    : {};
            }.bind(this),

            failure: function () {
                this.results = {};
            }.bind(this)
        });

        return Object.entries(this.results).map(([key, value]) => {
            return new Ext.data.JsonStore({
                key: value.key,
                data: value.data || [],
                id: value.id,
                type: value.type,
                icon: value.icon,
                path: value.path
            });
        });
    }

});

(function(listData, root, $, Backbone, Marionette, MediaManager) {
   "use strict";
    var starterListData = {
        items: [
            {
                title: '',
                description: ''
            }
        ]
    };
    var ListEditor = {
        options: {

        }
    }

    ListEditor.initialize = function ($editorElm, options) {
        this.pubsub = $('<p/>');
        options = options || {};
        this.editorElm = $editorElm;
        this.options = $.extend(this.options, options);
        this.listData = options.listData ? options.listData : starterListData;
        this.initializeEvents();
        this.initializeItemsListManager();

    }

    ListEditor.initializeEvents = function() {
        var editor = this.editorElm;
        editor.find('.save-draft-btn').click(this.saveDraft.bind(this));
        editor.find('.publish-btn').click(this.publish.bind(this));
        editor.find('.preview-btn').click(this.preview.bind(this));
    }

    ListEditor.validate = function() {
        var isListItemsValid = this.validateItems();
    }

    ListEditor.validateListItems = function() {
        if(this.isListItemsEmpty())
            return false;
        return ItemsManager.validate();
    }

    ListEditor.isListItemsEmpty = function() {
        var listItems = this.getData();
        return (!listItems || !listItems.length);
    }

    ListEditor.saveDraft = function() {

    }

    ListEditor.publish = function() {

    }

    ListEditor.preview = function() {

    }

    ListEditor.on = function(event, cb) {
        this.pubsub.on(event, cb.bind(this));
    }

    ListEditor.trigger = function(event, data) {
        this.pubsub.trigger(event, data);
    }

    ListEditor.initializeItemsListManager = function() {
        var self = this;
        ItemsManager.reqres.setHandler("list-items", function(type){
            return self.listData.items;
        });
        ItemsManager.on('change:items', function() {
            self.trigger('change');
        })
        ItemsManager.start();
        this.itemsManager = ItemsManager;
        this.getData = ItemsManager.getData.bind(ItemsManager);
    }



    /* ---  Items manager App --- Start --- */

    var ItemsManager =  new Marionette.Application();

    ItemsManager.addRegions({
        itemsListEditor: '#itemsListEditorContainer'
    });

    ItemsManager.validate = function() {
        var valid = true;
        this.itemsCollection.each(function(item) {
            if(!item.isValid()) {
                valid = false;
            }
        });
        return valid;
    }

    ItemsManager.addInitializer(function(options){
        var self = this;
        var listItemsCollection = new ListItemsCollection(this.reqres.request("list-items"))
        var listItemsView = new ItemsListView({
            collection: listItemsCollection
        });
        this.itemsCollection = listItemsCollection;
        listItemsCollection.on('change add remove', function() {
            self.trigger('change:items');
        })
        ItemsManager.itemsListEditor.show(listItemsView);
    });

    ItemsManager.getData= function() {
        return this.itemsCollection.toJSON();
    }

    /*--- Models --- start ---- */
    var ItemModel = Backbone.Model.extend({
        defaults: {
            title: '',
            description: ''
        },
        validate: function(attrs) {
            if(!attrs.title || !attrs.title.length) {
                var error = __('titleShouldNotBeEmpty');
                this.trigger('invalid:title', error);
                return error;
            }
            return false;
        }
    });

    /*--- Collections --- start ---- */
    var ListItemsCollection = Backbone.Collection.extend({
        initialize: function() {
        },
        model: ItemModel
    });


    /*--- Views --- start ---- */
    var ListItemView = Marionette.ItemView.extend({
        initialize: function() {
            this.listenTo(this.model, 'invalid:title', this.onValidationError.bind(this, 'title'))
        },

        template: '#listItemTemplate',
        /* ui selector cache */
        ui: {},

        /* Ui events hash */
        events: {
            "click .move-up-btn": "moveUp",
            "click .move-down-btn": "moveDown",
            "click .delete-btn": "delete",
            "keyup .item-title-field": "updateEditedData",
            "keyup .item-description-field": "updateEditedData",
            "click .item-media-placeholder": "addMedia",
            "click .media-edit-btn": "addMedia",
            "click .media-delete-btn": "deleteMedia"
        },
        moveUp: function(e) {
            this.move('up', e);
        },
        moveDown: function(e) {
            this.move('down', e);
        },
        move: function(direction, e) {
            var collection = this.model.collection;
            var oldIndex = this.model.collection.indexOf(this.model),
                newIndex;

            if(direction == 'up') {
                newIndex = oldIndex-1;
            } else {
                newIndex = oldIndex+1;
            }
            if(newIndex < 0 || newIndex >= this.model.collection.models.length)
                return;
            collection.remove(this.model);
            collection.add(this.model, {at: newIndex, silent: true});
            collection.trigger('change');
            e.preventDefault();
        },
        delete: function(e) {
            this.model.collection.remove(this.model);
            e.preventDefault();
        },
        addMedia: function() {
            var self = this;
            MediaManager.open(function(media) {
                if(!media) {
                    return;
                }
                console.log(media)
                self.model.set('media', media);
            }, {
                type: 'all'
            });
        },
        deleteMedia: function() {
            var self = this;
            self.model.unset('media');
        },
        updateEditedData: function(e) {
            var elm = $(e.target);
            elm.closest('.form-group').removeClass('has-error');
            var field = elm.data('field-name');
            this.model.set(field, elm.val(), {silent: true});
        },
        templateHelpers: function(){
            var self = this;
            return {
                position: function(){
                    return (self.model.collection.indexOf(self.model) + 1);
                },
                isFirst: function(){
                    return (self.model.collection.indexOf(self.model) == 0);
                },
                isLast: function(){
                    return (self.model.collection.indexOf(self.model) == self.model.collection.models.length-1);
                }
            };
        },
        onRender: function() {
            console.log(this.model.toJSON())
            this.$('.dropdown-toggle').dropdown();
        },
        onValidationError: function(field, error) {
            var elm = this.$('[data-field-name="' + field + '"]');
            elm.closest('.form-group').addClass('has-error').find('.help-block').text(error);
        }
    });

    var ItemsListView = Marionette.CompositeView.extend({
        initialize: function() {
            this.collection.on('change', this.render, this);
        },
        childView: ListItemView,
        childViewContainer: '#itemsList',
        template: '#itemsListEditor',
        events: {
            'click .add-item-btn': 'addItem'
        },
        onRender: function() {

        },
        addItem: function() {
            this.collection.add(new ItemModel());
        }
    });


    root.ListEditor = ListEditor;
})({}, window, $, Backbone, Backbone.Marionette, MediaManager);
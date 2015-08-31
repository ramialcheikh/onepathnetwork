@extends('layout')

@section('head')
    @parent
    <link rel="stylesheet" href="{{ asset('/bower_components/dropzone/dist/min/dropzone.min.css') }}"/>
@stop

@section('content')
    <div class="row">
        @if(!empty($list) && !$list->isApproved())
            <div class="col-md-12">
                <div class="alert alert-danger clearfix">
                    <div class="btn btn-warning pull-right" data-dismiss="alert">{{__('Okay')}}</div>

                    @if($list->isAwaitingApproval())
                        <strong>{{__('thisListIsAwaitingApproval')}}</strong>

                    @elseif($list->isDisapproved())
                        <strong>{{__('thisListIsDisapproved')}}</strong>
                    @else
                        <strong>{{__('thisListIsNotPublishedYet')}}</strong>
                    @endif
                    <p>{{ __('onlyYouCouldViewThis') }}</p>
                </div>
            </div>
        @endif

        <div class="col-md-12 list-editor">
            {{ Form::open(array('url' => 'foo/bar', 'method' => 'post', 'id' => 'listForm'), ListController::_getListFormRules()) }}

            @if(!empty($list) || !empty($duplicateList))
                <h1 class="page-header">{{__('editListPageHeading')}}</h1>
            @else
                <h1 class="page-header">{{__('createListPageHeading')}}</h1>
            @endif

            @if(!empty($duplicateList))
                <div class="editing-list-header-notice">
                    <h4>{{__('youAreEditingThisList')}}</h4>
                    <p><b>{{$duplicateList->title}}</b></p>
                </div>
                <hr/>
            @endif

            @if(!empty($hasChangesPendingApproval))
                <div class="alert alert-info">
                    {{__('changesToThisListArePendingApproval')}}
                </div>
            @endif

            <div class="list-editor-validation-error alert alert-danger">
                <i class="fa fa-times-circle fa-2x alert-icon"></i>
                {{__('missingOrInvalidInputCheckMarkedRed')}}
            </div>
            <div class="row">
                <div class="col-md-4 col-xs-12">
                    <div class="white-tile banner-container">
                        <div class="banner-placeholder">
                            <i class="fa fa-plus fa-2x"></i><br>
                            {{__('listBanner')}}
                        </div>
                        <div class="banner-missing-error text-danger"><small>{{__('bannerShouldBeSelected')}}</small></div>
                    </div>
                </div>
                <div class="col-md-8 col-xs-12">
                    <div class="form-group">
                        {{Form::text('title', null, array('id' => 'titleField', 'class' => 'title-field form-control', 'placeholder' => __('yourListTitleHere')))}}
                    </div>
                    <div class="form-group">
                        {{Form::textarea('description', null, array('id' => 'descriptionField', 'class' => 'description-field form-control', 'placeholder' => __('yourListDescriptionHere')))}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8" id="itemsListEditorContainer">

                </div>
                <script>
                    $(function() {
                        $("select").select2({dropdownCssClass: 'dropdown-inverse'});
                    });
                </script>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <h4>{{__('category')}}</h4>
                        {{Form::select('category_id', ['' => __('notSelected')] + $categoriesMap, null, array('id' => 'categoryField', 'class' => 'category-field form-control select select-primary select-block mbl'))}}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <h4>{{__('tags')}}</h4>
                        {{Form::text('tags', null, array('id' => 'tagsField', 'class' => 'tags-field form-control tagsinput', 'data-role' => "tagsinput"))}}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    @if(!empty($list) && $list->isApproved() && !ListController::isAutoApproveUpdatesEnabled())
                        <div class="alert alert-warning">
                            {{__('changesWillNeedApprovalAgain')}}
                        </div>
                    @endif
                    @if(empty($list) || (!$list->isApproved() && !$list->isAwaitingApproval() ))
                        <div class="btn btn-default save-draft-btn"><i class="fa fa-save"></i> {{__('saveDraft')}}</div>
                    @else
                        <div class="btn btn-success save-changes-btn"><i class="fa fa-save"></i> {{__('saveChanges')}}</div>
                    @endif

                    @if(!empty($list) && $list->exists())
                        @if($list->isNotSubmitted())
                            <div class="btn btn-success publish-btn"><i class="fa fa-cloud-upload"></i> {{__('publishList')}}</div>
                        @endif
                        @if($list->isApproved())
                            <a href="{{ListHelpers::viewListUrl($list)}}" class="btn btn-info preview-btn pull-right"><i class="fa fa-eye"></i> {{__('viewList')}}</a>
                        @else
                            <a href="{{ route('previewList', array('list-id' => $list->id)) }}" target="_blank" class="btn btn-info preview-btn pull-right"><i class="fa fa-eye"></i> {{__('preview')}}</a>
                        @endif
                    @endif
                </div>
            </div>
            {{Form::close()}}

            <div id="editorDialog" class="dialog">
                <div class="dialog__overlay"></div>
                <div class="dialog__content">
                    <div class="btn btn-link action close-btn" data-dialog-close=""><i class="fa fa-times"></i></div>
                    <div class="status-icon success-icon text-success"><i class="fa fa-check-circle fa-5x"></i></div>
                    <div class="status-icon default-icon text-muted"><i class="fa fa-check-circle fa-5x"></i></div>
                    <div class="status-icon error-icon text-danger"><i class="fa fa-times-circle fa-5x"></i></div>
                    <div class="status-icon warning-icon text-warning"><i class="fa fa-warning fa-5x"></i></div>
                    <div class="dialog-main-content"></div>
                    <div class="sk-rotating-plane loading-anim loading-primary"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates -->
    <script id="listItemTemplate" type="text/template">
        <div class="row item-block">
            <div class="col-md-2 col-xs-2 item-number-container">
                <div class="btn-group">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                        <b><%= position() %></b> &nbsp;<small><i class="fa fa-sort"></i></small>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li class="<% if(isFirst()) print('disabled') %>"><a href="#" <% if(isFirst()) print('onclick="return false;"') %>class="move-up-btn"><i class="fa fa-chevron-up"></i> &nbsp;&nbsp;{{__('moveUp')}}</a></li>
                        <li class="<% if(isLast()) print('disabled') %>"><a href="#"  <% if(isFirst()) print('onclick="return false;"') %>class="move-down-btn"><i class="fa fa-chevron-down"></i> &nbsp;&nbsp;{{__('moveDown')}}</a></li>
                        <li><a href="#" class="delete-btn"><i class="fa fa-times text-danger"></i> &nbsp;&nbsp;{{__('delete')}}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-10 col-xs-10 form-group">
                <input type="text" data-field-name="title" class="item-title-field form-control" placeholder="{{{__('title')}}}" value="<%= _.escape(title) %>">
                <div class="help-block error-block"></div>
            </div>
            <div class="col-md-12 col-xs-12">
                <div class="item-media-container white-tile">
                    <% if(typeof media != "undefined") { %>
                    <div class="media-actions-bar">
                        <div class="btn btn-warning media-edit-btn">
                            <i class="fa fa-pencil"></i>
                        </div>
                        <div class="btn btn-danger media-delete-btn">
                            <i class="fa fa-times"></i>
                        </div>
                    </div>
                    <%
                        if(media.embedCode) { print(media.embedCode); }
                        if(media.type == 'quote')
                            print(_.template($('#quoteTemplate').html())(media))
                        if(media.type == 'photo')
                            print(_.template($('#photoMediaTemplate').html())(media))
                     %>

                    <% } else { %>
                    <div class="item-media-placeholder">
                        <i class="fa fa-plus fa-2x"></i><br>
                        MEDIA<br>
                        <small class="text-muted">Click to add Photo, video or more</small>
                    </div>
                    <% } %>
                </div>
                <textarea type="text" data-field-name="description" class="item-description-field form-control" placeholder="{{{__('description')}}}"><%= _.escape(description) %></textarea>
            </div>
        </div>
    </script>

    <script id="itemsListEditor" type="text/template">
        <h4>{{__('itemsInTheList')}}</h4>
        <div id="itemsList" class="container-fluid">
            <!-- Item blocks here -->
        </div>
        <div class="text-danger no-items-error hide"><small>{{__('atleastOneItemIsNeeded')}}</small></div>
        <div class="row add-item-btn-block">
            <div class="col-md-12 col-xs-12">
                <div class="btn btn-info btn-block add-item-btn"><i class="fa fa-plus"></i> {{__('addItem')}}</div>
            </div>
        </div>
    </script>

@stop

@section('foot')
    @parent


    @include('partials.media-manager')

    <script id="quoteTemplate" type="text/template">
        <blockquote>
            <p>
                <%= _.escape(quote) %>
            </p>
            <footer><%= _.escape(source) %></footer>
        </blockquote>
    </script>

    <script id="photoMediaTemplate" type="text/template">
        <img class="media-photo" src="<%= _.escape(asset(url)) %>" alt=""/>
    </script>


    <script id="dialogTemplate" type="script/template">
        <% if(typeof heading != "undefined") { %>
        <div class="heading"><%= heading %></div>
        <% } %>
        <div class="dialog-message"><%= message %></div>
    </script>

    <script>
        (function() {
            var dialogElm = $('#editorDialog'),
                    dialogContentElm = dialogElm.find('.dialog-main-content'),
                    closeButton = dialogElm.find('[data-dialog-close]'),
                    loadingAnim = dialogElm.find('.loading-anim');
            dialogElm.$ = dialogElm.find.bind(dialogElm);
            var dialog = new DialogFx( dialogElm[0] );
            var defaultDialogData = {
                closeButton: true,
                message: '',
                progress: false,
                type: 'normal'
            };
            var dialogTypes = ['error', 'success', 'warning', 'default'];
            function getTextTypeClass(type) {
                var typeClasses = {error: '', warning: '', success: ''};
                return typeClasses[type];
            }
            window.EditorDialog = {
                open: function(dialogData) {
                    dialogData = $.extend({}, defaultDialogData, dialogData || {});
                    !dialogData.closeButton ? closeButton.hide() : closeButton.show();
                    dialogData.progress ? loadingAnim.show() : loadingAnim.hide();
                    dialogContentElm.html(_.template($('#dialogTemplate').html())(dialogData));
                    dialogElm.removeClass('dialog-' + dialogTypes.join(' dialog-'));
                    dialogElm.addClass('dialog-' + dialogData.type);
                    if(dialogData.heading){
                        var heading = dialogElm.$('.heading');
                        (dialogTypes.indexOf(dialogData.type) >= 0) && heading.addClass(getTextTypeClass(dialogData.type));
                    }
                    dialog.open();
                },
                close: function() {
                    dialog.close();
                }
            }
        } )();
    </script>

    <script src="{{ asset('/bower_components/underscore/underscore-min.js') }}"></script>
    <script src="{{ asset('/bower_components/backbone/backbone-min.js') }}"></script>
    <script src="{{ asset('/bower_components/marionette/lib/backbone.marionette.min.js') }}"></script>
    <script src="{{ asset('/bower_components/dropzone/dist/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('/js/EmbedPreview.js') }}"></script>
    <script src="{{ asset('/js/main.js') }}"></script>
    <script src="{{ asset('/js/list-editor.js') }}"></script>
    <script src="{{asset('bower_components/jquery.validate/dist/jquery.validate.min.js')}}"></script>
    <script src="{{asset('packages/bllim/laravalid/jquery.validate.laravalid.js')}}"></script>

    <script>

        // override jquery validate plugin defaults
        $.validator.setDefaults({
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function(error, element) {
                if(element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });

    </script>

    <script>
        $(function() {
            var justSavedTheNewList = {{ $justSavedTheNewList ? 'true' : 'false'}};
            function showListSavedSuccessDialog(list, data) {
                data = data || {};
                var message;
                if(list.status == 'not_submitted')
                    message = '<p>' + __('nowPublishTheList') + '</p> <div class="btn btn-primary btn-lg publish-btn"><i class="fa fa-cloud-upload"></i> ' + __('publishList') + '</div>';
                else if(data.markedForApprovalAgain)
                    message = __('changesWillBeApprovedByAdmin');
                else
                    message = '<div class="btn btn-primary preview-list-btn"><i class="fa fa-eye"></i> ' + __('previewTheList') + '</div>';
                EditorDialog.open({message: message, heading: __('listSaved'), type: 'default'})
            }

            function showListPublishedSuccessMessage(data) {
                var message,
                        heading;
                if(data.status == 'approved') {
                    message = '<a href="' + data.viewListUrl + '" class="btn btn-primary">' + __('viewTheList') + '</a>';
                    heading = __('listPublished');
                } else {
                    message = '<p>' + __('listWillBePublishedAfterApproval') + '</p><a href="' + data.previewListUrl + '" class="btn btn-primary">' + __('previewTheList') + '</a>';
                    heading = undefined;
                }
                EditorDialog.open({message: message, heading: heading, type: 'success'});
            }

            window.savedListData = undefined;
            @if(!empty($list) || !empty($duplicateList))
            savedListData = {{!empty($list) ? $list->toJson() : $duplicateList->toJson()}};
            @if(!empty($duplicateList))
                delete savedListData['id'];
            @endif
            $('#titleField').val(savedListData.title);
            $('#descriptionField').val(savedListData.description);
            setBannerImage(savedListData.image);
            $('#categoryField').select2("val", savedListData.category_id);
            //Tags input is set right in the dom due to incompatibility with the tagsInput plugin
            $('#tagsField').tagsinput('add', savedListData.tags);
            @endif

            if(justSavedTheNewList) {
                showListSavedSuccessDialog(savedListData);
            }

            var saveListUrl = '{{ route('createList') }}';
            var publishListUrl = '{{ route('publishList') }}';
            ListEditor.initialize($('.list-editor'), {
                listData : (typeof savedListData != "undefined") ? {items: savedListData.content} : null
            });
            function getListData() {
                var listData = {
                    title: $('#titleField').val(),
                    description: $('#descriptionField').val(),
                    image: $('.banner-container').data('image-url'),
                    category_id: $('#categoryField').val(),
                    tags: $('#tagsField').val(),
                    content: ListEditor.getData()
                }
                if(savedListData) {
                    listData.id = savedListData.id;
                }
                return listData;
            }

            function saveList () {
                var listData = getListData();
                if(!ListValidator.validate()) {
                    $('.list-editor').addClass('show-validation-error');
                    $(window).scrollTop($('.list-editor').offset().top);
                    return false;
                } else {
                    $('.list-editor').removeClass('show-validation-error');
                }
                EditorDialog.open({'heading' : __('savingList'), progress: true});
                var params = {
                    list: JSON.stringify(listData)
                };

                @if(!empty($duplicateList))
                params['create-from'] = {{$duplicateList->id}};
                @endif

                $.post(saveListUrl, params).done(function(data) {
                    try {
                        //data = JSON.parse(data);
                        console.log('List saved', data.list);
                        if(data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            showListSavedSuccessDialog(data.list, data);
                        }

                    } catch(e) {
                        EditorDialog.open({message: '{{__('someErrorOccured')}}', type: 'error'})
                    }
                }).fail(function(jqXHR, message) {
                    EditorDialog.open({message: jqXHR.responseText, heading: '{{__('listNotSaved')}}', type: 'error'})
                })
            }

            function publishList() {
                EditorDialog.open({'heading' : __('publishingList'), progress: true});
                var listData = getListData();
                $.post(publishListUrl, {
                    "list-id": listData.id
                }).done(function (data) {
                    showListPublishedSuccessMessage(data);
                }).fail(function (jqXHR, message) {
                    EditorDialog.open({message: jqXHR.responseText, type: 'error'});
                });
            }


            $('body').on('click', '.publish-btn', publishList);
            @if(!empty($list))
                $('body').on('click', '.preview-list-btn', function() {
                    window.open('{{ route('previewList', array('list-id' => $list->id)) }}');
                });
            @endif
            $('.save-draft-btn, .save-changes-btn').click(saveList);

            window.ListValidator = {
                validate: function() {
                    var listData = getListData();

                    //Checks title, description, and category - tags doesn't seem to work with this check
                    var listFormValid = $('#listForm').valid();

                    //So, separate check for tags
                    var tagsFieldValid = $('#tagsField').valid();

                    //Validating the list items from the List editor
                    var listItemsValid = this.validateListItems();

                    //Is the banner valid
                    var bannerValid = this.validateBanner();

                    if(!listFormValid || !tagsFieldValid || !listItemsValid || !bannerValid){
                        //Something is not valid- return
                        return false;
                    }

                    return true;

                },
                _validateField: function(field) {
                    $('#' + field + 'Field').parent().addClass('hasError');
                },
                validateBanner: function(showError) {
                    showError = (showError == undefined) ? true : showError;
                    var listData = getListData();
                    if(listData.image) {
                        $('.banner-container').removeClass('has-error');
                        return true;
                    }
                    if(showError)
                        $('.banner-container').addClass('has-error');
                    return false;
                },
                validateListItems: function() {
                    return ListEditor.validateListItems();
                },
                validateListItemsCount: function() {
                    if(ListEditor.isListItemsEmpty()){
                        $('.no-items-error').removeClass('hide');
                    } else {
                        $('.no-items-error').addClass('hide');
                        return true;
                    }
                    return false;
                }
            }

            function setBannerImage(imageUrl) {
                var bannerContainer = $('.banner-container');
                var photoMarkup = _.template($('#photoMediaTemplate').html())({
                    url: imageUrl
                });
                bannerContainer.html(photoMarkup);
                bannerContainer.data('image-url', imageUrl);
                if(typeof ListValidator != "undefined")
                    ListValidator.validateBanner(false);
            }
            $('.banner-container').click(function() {
                var self = $(this);
                MediaManager.open(function(media) {
                    setBannerImage(media.url);
                }, {
                    mode: 'photo'
                })
            })

            $('#tagsField').on('change', function(){
                $(this).valid();
            })
            $('#categoryField').on('change', function(){
                $(this).valid();
            })

            ListEditor.on('change', function() {
                ListValidator.validateListItemsCount();
            })


            MediaManager.initialize($('#mediaManagerModal'), {
                templates: {
                    'modalContent' : $('#mediaManagerModalTemplate').html(),
                    'quote' : $('#quoteTemplate').html()
                },
                imageUploadOptions: {
                    maxFileSize: parseFloat(SiteListConfig.fileUploadMaxSize),
                    url: '{{route('listUploadImage')}}'
                }
            });
        });
    </script>

@stop
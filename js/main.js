
(function(root, $, EmbedPreview, __, reqres){
    "use strict";

    function formatPhotoData(uploadedFilePath, source) {
        return {
            type: 'photo',
            url: uploadedFilePath,
            source: source
        }
    }

    function formatVideoData(url, embedCode) {
        return {
            type: 'video',
            url: url,
            embedCode: embedCode
        };
    }

    function formatAudioData(url, embedCode) {
        return {
            type: 'audio',
            url: url,
            embedCode: embedCode
        };
    }

    function formatQuoteData(quoteData) {
        return {
            type: 'quote',
            quote: quoteData.quote,
            source: quoteData.source
        };
    }

    var MediaManager = {
        pubsub: null,
        modalElm: null,
        mediaData: null,
        initialize: function(modalElm, options) {
            options = options || {};
            this.options = options;
            this.templates = options.templates;
            this.pubsub = $('<p/>');
            this.modalElm = modalElm;
            this._initializeMenu();
        },
        _getMediaData: function() {
            return reqres.request('media-data');
        },
        open: function(callback, options) {
            this.resetMenu();
            options = options || {};
            if(options.mode == 'photo') {
                this._showSubMenu('photo');
                this.modalElm.find('.back-btn').hide();
            }
            var self = this;
            var modalElm = this.modalElm;
            modalElm.modal('show');
            function onMediaReceived(e, media) {
                callback(media);
                modalElm.modal('hide');
            }
            this.pubsub.on('media-received', onMediaReceived);
            modalElm.on('hidden.bs.modal', function() {
                self.pubsub.off('media-received', onMediaReceived);
            });
        },
        resetMenu: function() {
            this.modalElm.html(_.template(this.templates.modalContent)());
            this._initializeMenu();
        },
        _initializeEvents: function() {
            var self = this;
            this.modalElm.find('.save-media-btn').click(function() {
                self.pubsub.trigger('media-received', self._getMediaData());
            });
            this.modalElm.find('.back-btn').click(function(){
                self.pubsub.trigger('show-main-menu');
            });
            self.pubsub.on('show-main-menu', function() {
                self.modalElm.removeClass('show-sub-menu');
                self.wait();
            })
            self.pubsub.on('show-sub-menu', function() {
                self.modalElm.addClass('show-sub-menu');
                self.wait();
            })
        },
        _initializeMenu: function() {
            this.saveMediaBtn = this.modalElm.find('.save-media-btn');
            this._initializeEvents();
            this._initializePhotoManager();
            this._initializeVideoManager();
            this._initializeSoundcloudAudioManager();
            this._initializeQuoteManager();

            this.pubsub.trigger('show-main-menu');
            reqres.setHandler('media-data', function() {
                return mediaData;
            });
            this.wait();

            var self = this;
            var mainMenu = this.modalElm.find('.main-menu');
            mainMenu.find('.menu-item').click(function() {
                var menuTarget = $(this).data('menu-target');
                self._showSubMenu(menuTarget);
            })
        },
        _showSubMenu: function(menuId) {
            var self = this;
            var subMenuScreens = this.modalElm.find('.sub-menu-screen');
            subMenuScreens.hide();
            self.modalElm.find('.' + menuId+ '-sub-menu').show();
            self.pubsub.trigger('show-sub-menu', menuId);
        },
        _initializePhotoManager: function() {
            var self=  this;
            var modalElm = this.modalElm,
                subMenu = modalElm.find('.photo-sub-menu'),
                photoDropZone = subMenu.find('.image-drop-zone'),
                errorElm = subMenu.find('.image-upload-error'),
                previewElm = subMenu.find('.upload-image-via-url-preview');
            function showImageError(error) {
                errorElm.html(error).removeClass('hide');
            }
            function hideImageError() {
                errorElm.addClass('hide');
            }
            function showPreview(imageUrl) {
                previewElm.removeClass('hide');
                previewElm.html('<img src="'+ imageUrl +'">');
            }

            function hidePreview() {
                previewElm.addClass('hide');
            }
            //if modal is shown
            if(subMenu.length) {
                var myDropzone = new Dropzone(photoDropZone[0], {
                    url: self.options.imageUploadOptions.url,
                    maxFiles: 1,
                    maxFileSize: self.options.imageUploadOptions.maxFileSize,
                    acceptedFiles: 'image/*',
                    dictInvalidFileType: __('uploadedImageInvalid'),
                    dictFileTooBig: __('uploadedImageTooBig'),
                    init: function() {
                        this.on('error', function(File, error, xhr) {
                            this.removeFile(this.files[this.files.length - 1]);
                            switch(error) {
                                case 'invalidOrBig':
                                    error = __('uploadedImageInvalidOrBig');
                                    break;
                                case 'tooBig':
                                    error = __('uploadedImageTooBig');
                                    break;
                                case 'invalidImage':
                                    error = __('uploadedImageInvalid');
                                    break;
                            }

                            if(xhr) {
                                if(xhr.status == 500) {
                                    error = __('uploadedImageTooBig');
                                }
                            }
                            showImageError(error);
                        });
                        this.on("addedfile", function() {
                            if (this.files[1]!=null){
                                this.removeFile(this.files[0]);
                            }
                            hideImageError();
                        });
                    },
                    success: function(file, uploadedFilePath) {
                        self.ready(formatPhotoData(uploadedFilePath));
                    }
                });
                var uploadViaUrlForm = subMenu.find('.upload-image-via-url-form');
                var uploadViaUrlBtn = subMenu.find('.upload-image-via-url-btn');
                uploadViaUrlForm.on('drop', 'input', function (e) {
                    var url = $(e.originalEvent.dataTransfer.getData('text/html')).filter('img').attr('src');
                    if(url){
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).val(url);
                    }
                })
                uploadViaUrlBtn.click(function(e) {
                    var formData = uploadViaUrlForm.serialize();
                    var actionUrl = self.options.imageUploadOptions.url;
                    uploadViaUrlBtn.button('loading');
                    hidePreview();
                    $.post(actionUrl, formData).done(function(uploadedFilePath) {
                        hideImageError(__('uploadedImageInvalid'));
                        //Its ready - send the image url
                        self.ready(formatPhotoData(uploadedFilePath, uploadViaUrlForm.find('[name="source"]').val()));
                        showPreview(uploadedFilePath);
                    }).fail(function(jqXhr) {
                        showImageError(jqXhr.responseText);
                    }).always(function() {
                        uploadViaUrlBtn.button('reset');
                    });
                    e.preventDefault();
                    return false;
                });
            }
        },
        _initializeVideoManager: function() {
            var mediaData;
            var self=  this;
            var modalElm = this.modalElm,
                subMenu = modalElm.find('.video-sub-menu'),
                previewBtnElm = subMenu.find('.preview-btn'),
                videoUrlField = subMenu.find('.video-url-field'),
                videoPreviewBox = subMenu.find('.video-preview');
            previewBtnElm.click(function() {
                var videoUrl = videoUrlField.val();
                var embedLink = EmbedPreview.video.getEmbedLink(videoUrl);
                self.wait();
                if(!videoUrl) {
                    $('.video-url-field-error').text(__('videoLinkInvalid')).removeClass('hide');
                    videoPreviewBox.hide();
                    return;
                } else {
                    $('.video-url-field-error').addClass('hide');
                    var embedCode = '<iframe class="media-video" scrolling="no" style="border: 0px; display: block; margin: 0px auto;" src="' + embedLink + '"></iframe>';
                    videoPreviewBox.html(embedCode).show();
                    self.ready(formatVideoData(embedLink, embedCode));
                }
            });
        },
        _initializeSoundcloudAudioManager: function() {
            var self=  this;
            var modalElm = this.modalElm,
                subMenu = modalElm.find('.audio-sub-menu'),
                previewBtnElm = subMenu.find('.preview-btn'),
                audioUrlField = subMenu.find('.audio-url-field'),
                audioPreviewBox = subMenu.find('.audio-preview');
            previewBtnElm.click(function() {
                var audioUrl = audioUrlField.val();
                self.wait();
                var embedLink = EmbedPreview.audio.soundcloud.getEmbedCode(audioUrl, function(err, embedCode) {
                    if(err) {
                        $('.audio-url-field-error').text(__('audioLinkInvalid')).removeClass('hide');
                        audioPreviewBox.hide();
                        return;
                    } else {
                        $('.audio-url-field-error').addClass('hide');
                        audioPreviewBox.html(embedCode).show();
                        self.ready(formatAudioData(audioUrl, embedCode));
                    }
                });
            });
        },
        _initializeQuoteManager: function() {
            var self=  this;
            var modalElm = this.modalElm,
                subMenu = modalElm.find('.quote-sub-menu'),
                previewBtnElm = subMenu.find('.preview-btn'),
                quoteField = subMenu.find('.quote-field'),
                sourceField = subMenu.find('.source-field'),
                quotePreviewBox = subMenu.find('.quote-preview');
            function onInputChange(){
                var quoteData = {
                    quote: quoteField.val(),
                    source: sourceField.val()
                }
                quotePreviewBox.html(_.template(self.templates.quote)(quoteData)).show();
                if(quoteData.quote && quoteData.source) {
                    self.ready(formatQuoteData(quoteData));
                }
            }

            subMenu.find('input,textarea').on('keyup', onInputChange);
        },
        ready: function(mediaData) {
            reqres.setHandler('media-data', function() {
                return mediaData;
            });
            this.saveMediaBtn.show();
        },
        wait: function() {
            this.saveMediaBtn.hide();
        }
    }

    root.MediaManager = MediaManager;
})(window, $, EmbedPreview, __, new Backbone.Wreqr.RequestResponse(), Dropzone );
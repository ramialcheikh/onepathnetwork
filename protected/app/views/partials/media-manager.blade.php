<div id="mediaManagerModal" class="modal fade media-manager-modal">

</div><!-- /.modal -->

<script id="mediaManagerModalTemplate" type="text/template">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{__('mediaManagerTitle')}}</h4>
            </div>
            <div class="modal-body">
                <div class="main-menu">
                    <ul class="main-menu list-unstyled">
                        <li class="menu-item photo-menu-item" data-menu-target="photo">
                            <i class="fa fa-photo fa-3x photo-icon text-info"></i>
                            <div class="menu-item-text">{{__('photo')}}</div>
                        </li>
                        <li class="menu-item video-menu-item" data-menu-target="video">
                            <i class="fa fa-youtube-square fa-3x text-brand-youtube"></i>
                            <div class="menu-item-text">{{__('youtubeVideo')}}</div>
                        </li>
                        <li class="menu-item video-menu-item" data-menu-target="video">
                            <i class="fa fa-vimeo-square fa-3x text-brand-vimeo"></i>
                            <div class="menu-item-text">{{__('vimeoVideo')}}</div>
                        </li>
                        <li class="menu-item audio-menu-item" data-menu-target="audio">
                            <i class="fa fa-soundcloud fa-3x text-brand-soundcloud"></i>
                            <div class="menu-item-text">{{__('soundcloudAudio')}}</div>
                        </li>
                        <li class="menu-item quote-menu-item" data-menu-target="quote">
                            <i class="fa quote-icon fa-quote-right fa-3x text-warning"></i>
                            <div class="menu-item-text">{{__('quote')}}</div>
                        </li>
                    </ul>
                </div>
                <div class="sub-menu">
                    <div class="sub-menu-screen video-sub-menu">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 media-input-form">
                                <div class="form-group">
                                    <label for="">{{__('videoUrl')}}</label>
                                    <input class="form-control video-url-field" type="text" value=""/>
                                    <div class="text-danger video-url-field-error hide">{{__('invalidFieldInputError')}}</div>
                                </div>
                                <div class="input-group">
                                    <div class="btn btn-info preview-btn">{{__('previewVideo')}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 text-center">
                                <div class="embed-preview video-preview"></div>
                            </div>
                        </div>
                    </div>

                    <div class="sub-menu-screen audio-sub-menu">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 media-input-form">
                                <div class="form-group">
                                    <label for="">{{__('soundcloudUrl')}}</label>
                                    <input class="form-control audio-url-field" type="text" value=""/>
                                    <div class="text-danger audio-url-field-error hide">{{__('invalidFieldInputError')}}</div>
                                </div>
                                <div class="input-group">
                                    <div class="btn btn-info preview-btn">{{__('previewAudio')}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 text-center">
                                <div class="embed-preview audio-preview"></div>
                            </div>
                        </div>
                    </div>

                    <div class="sub-menu-screen quote-sub-menu">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 media-input-form">
                                <div class="form-group">
                                    <label for="">{{__('quote')}}</label>
                                    <input data-model-key="quote" class="form-control quote-field" type="text" value=""/>
                                    <div class="text-danger quote-field-error hide">{{__('emptyFieldError')}}</div>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('source')}}</label>
                                    <input data-model-key="source" class="form-control source-field" type="text" value=""/>
                                    <div class="text-danger source-field-error hide">{{__('emptyFieldError')}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 text-center">
                                <div class="quote-preview">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sub-menu-screen photo-sub-menu">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 media-input-form">
                                <div class="text-center dropzone image-drop-zone">
                                    <div class="dz-message">
                                        {{__('dropOrClickToUpload')}}
                                    </div>
                                </div>
                                <div class="image-upload-error hide alert alert-danger"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 text-center">
                                <div class="photo-preview">
                                    <div class="form-group text-left">
                                        <div class="text-center or-upload-via-url-msg"><b>{{__('orUploadViaUrl')}}</b></div>

                                        <div class="">
                                            <form action="" class="upload-image-via-url-form" onsubmit="return false;">
                                                <div class="form-group">
                                                    <label class="control-label" for="remoteUrl">{{__('imageUrl')}}</label>
                                                    <input name="remoteUrl" type="text" class="form-control" placeholder="{{__('imageUrl')}}" required="required">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label" for="source">{{__('imageSource')}}</label>
                                                    <input name="source" type="text" class="form-control" placeholder="{{__('imageSource')}}" required="required">
                                                </div>
                                                <div type="submit" class="btn btn-primary btn-sm upload-image-via-url-btn" data-loading-text="{{__('uploading')}}">{{__('uploadViaUrl')}}</div>
                                                <div class="upload-image-via-url-preview hide thumbnail"></div>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="btn btn-success back-btn pull-left">{{__('back')}}</div>
                <div class="btn btn-success save-media-btn">{{__('done')}}</div>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{__('cancelBtn')}}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</script>
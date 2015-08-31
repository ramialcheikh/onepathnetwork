@if(!empty($widgets[$section]))
    <div class="row">
        <div class="col-md-12">
            <div class="{{$section}}-widget-section">
                @foreach($widgets[$section] as $widget)
                    {{do_shortcode($widget['content'])}}
                @endforeach
            </div>
        </div>
    </div>
@endif
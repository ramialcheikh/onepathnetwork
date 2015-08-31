@extends('admin/layout')
@section('content')
    <div class="alert alert-success">
        <h4>Success!</h4>
        <p>Sitemap generated!</p>
        <a href="{{route('sitemap')}}" target="_blank">Click here to view sitemap</a>
    </div>
    <div class="alert alert-warning">
        <p><b>Sitemaps are automatically re-generated every hour.</b></p>
        If you want to re-generate it whenever you wish, you can use the "Re-generate sitemap" button on the menu.
    </div>
@stop
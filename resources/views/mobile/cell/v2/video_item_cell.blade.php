@if(isset($video))
    <div class="video_item">
        <a href="{{$video['link']}}">
            <p class="img_box" style="background-image: url({{$video['image']}});"></p>
            <h3>{{$video['title']}}?</h3>
        </a>
    </div>
@endif
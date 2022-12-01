@extends('superadmin::layouts.master')
@section('content')
    <script src="https://unpkg.com/jquery@2.2.4/dist/jquery.js"></script>
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <link href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css"/>
    <style>
        .tagTable{margin-top: 15px;}
        #type{color: #337ab7}
        .listDiv{background-color: #CBCAB4FF; height: 28px;width: 178px;padding: 10px;border-bottom: 1px solid white;}
    </style>
    <?php
    use Illuminate\Support\Facades\Auth;
    $lang = 'FR';
    Auth::user()->setting;
    $setting = json_decode(Auth::user()->setting, 1);
    $lang = isset($setting['lang']) ? $setting['lang'] : $lang;
    ?>
    <script>
        function showSearchBox(id){
            $(document).ready(function(){
                $('#searchBox_' + id).append('<input id="key" type="text" name="search" maxlength = "3">');
                $('#key').attr('id', 'key' + id);
            });
            $("#merge_" + id).hide();
            searchTag(id);
        }
    </script>
    <script>
        function searchTag(id){
            $(document).ready(function(){
                $("#key" + id).keyup(function(){
                    var key = $( "#key"+ id ).val();
                    var count = key.length;
                    if (count === 3){
                        $.get('search/tag',{ key: key},function(data){
                            if (data.data.length === 0){
                                $('#searchBox_' + id).append('<div id="result" class="listDiv"></div>');
                                $('#result').html('No Record Found');
                            }
                            $.each(data.data, function(key, value) {
                                for (let i=0; i <= data.data.length; i++){
                                    if (i === data.data.length){
                                        return false;
                                    }
                                    $('#searchBox_' + id).append('<div id="result" class="listDiv"></div>');
                                    $("#res").show();
                                    var lang =  selectLang();
                                    if (lang === 'EN'){
                                        $('#result').attr('id', 'result' + i).html(data.data[i].tag_EN);
                                    }else{
                                        $('#result').attr('id', 'result' + i).html(data.data[i].tag_FR);
                                    }

                                    $('#result' + i).on('click', function() {
                                        let mergingId = data.data[i].id;
                                        let tag = $("#edit_EN_" + id).text();
                                        mergeTag(tag,mergingId,id);
                                    });
                                }
                            });
                        });
                    }
                });
            });
        }

        function selectLang(){
            let lang = '<?php echo $lang ?>'
            if (lang === 'EN'){
                return 'EN';
            }
            return 'FR';
        }
        function mergeTag(tag,mergingId,id){
            let check = confirm("Are you sure for merging" + tag);
            if (check === true){
                $.ajax({
                    type:'POST',
                    url:"{{ route('merge') }}",
                    data:{tag:tag, id:mergingId,deleteId:id},
                    success:function(data){
                        if (data.status === true){
                            location.reload();
                        }
                    }
                });
            }
        }

    </script>
    <section>
        <div class="container">
            <div class="col-md-4" id="tagsType"style="display:contents; font-size: 15px">
                <div class="col-md-2" ><a  href="{{route('pro-tag')}}">Professional Tags</a></div>
                <div class="col-md-2"><a href="{{route('perso-tag')}}">Personal Tags</a></div>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4"></div>
        </div>
    </section>
    <section>
        <div class="container">
            <a href="export-tags/2"><button type="button" style="margin-top: 10px;" download>XLS Perso</button></a>
            <a href="export-tags/1"><button type="button" style="margin-top: 10px;" download>XLS Pro</button></a>
                <div class="tagTable">
                    <table class="table" id="tagTable ">
                        <thead>
                        <tr>
                            <th scope="col">EN.</th>
                            <th scope="col">FR.</th>
                            <th scope="col">Accept</th>
                            <th scope="col">Reject</th>
                            <th scope="col">Merge</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($persoTags == [])
                            <h4 id="type">Professional  Tags</h4>
                            @foreach($proTags as $tag)
                            <tr>
                                <td id="edit_EN_{{$tag->id}}" onclick="editTagEN('{{$tag->id}}')"> {{$tag->tag_EN}} </td>
                                <td id="edited_EN_{{$tag->id}}" style="display: none;"></td>
                                <td id="button_EN_{{$tag->id}}" style="display: none;"><button onclick="Submit_EN('{{$tag->id}}')" id="submit_EN_{{$tag->id}}">Done</button></td>

                                <td id="edit_FR_{{$tag->id}}" onclick="editTagFR('{{$tag->id}}')"> {{$tag->tag_EN}} </td>
                                <td id="edited_FR_{{$tag->id}}" style="display: none;"></td>
                                <td id="button_FR_{{$tag->id}}" style="display: none;"><button onclick="Submit_FR('{{$tag->id}}')" id="submit_FR_{{$tag->id}}">Done</button></td>

                                <td><button class="accept_tag" id="{{$tag->id}}">accept</button></td>
                                <td><button class="reject_tag" id="{{$tag->id}}">Reject</button></td>
                                <td>
                                    <div id="searchBox_{{$tag->id}}"></div>
                                    <div id="res"></div>
                                    <button id="merge_{{$tag->id}}" onclick="showSearchBox({{$tag->id}})">Merge</button>
                                </td>
                            </tr>
                        @endforeach
                        @elseif($proTags == [])
                            <h4 id="type">Personal Tags</h4>
                            @foreach($persoTags as $tag)
                                <tr>
                                    <td id="edit_EN_{{$tag->id}}" onclick="editTagEN('{{$tag->id}}')"> {{$tag->tag_EN}} </td>
                                    <td id="edited_EN_{{$tag->id}}" style="display: none;"></td>
                                    <td id="button_EN_{{$tag->id}}" style="display: none;"><button onclick="Submit_EN('{{$tag->id}}')" id="submit_EN_{{$tag->id}}">Done</button></td>

                                    <td id="edit_FR_{{$tag->id}}" onclick="editTagFR('{{$tag->id}}')"> {{$tag->tag_EN}} </td>
                                    <td id="edited_FR_{{$tag->id}}" style="display: none;"></td>
                                    <td id="button_FR_{{$tag->id}}" style="display: none;"><button onclick="Submit_FR('{{$tag->id}}')" id="submit_FR_{{$tag->id}}">Done</button></td>
                                    
                                    <td><button class="accept_tag" id="{{$tag->id}}">accept</button></td>
                                    <td><button class="reject_tag" id="{{$tag->id}}">Reject</button></td>
                                    <td>
                                        <div id="searchBox_{{$tag->id}}"></div>
                                        <div id="res"></div>
                                        <button id="merge_{{$tag->id}}" onclick="showSearchBox({{$tag->id}})">Merge</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
        </div>
    </section>

    <script>
        function editTagEN(id){
            var tag_EN = document.getElementById('edit_EN_'+ id).innerHTML;
            document.getElementById('edit_EN_'+ id).style.display = "none";
            document.getElementById("edited_EN_" + id).innerHTML = '<input type="text" value="'+tag_EN+'" id="new_EN_'+id+'">';
            document.getElementById('edited_EN_'+ id).style.display = "block";
            document.getElementById('button_EN_'+ id).style.display = "block";
        }

        function editTagFR(id){
            var tag_FR = document.getElementById('edit_FR_'+ id).innerHTML;
            document.getElementById('edit_FR_'+ id).style.display = "none";
            document.getElementById("edited_FR_" + id).innerHTML = '<input type="text" value="'+tag_FR+'" id="new_FR_'+id+'">';
            document.getElementById('edited_FR_'+ id).style.display = "block";
            document.getElementById('button_FR_'+ id).style.display = "block";
        }

        function Submit_EN(id) {
            var new_value = document.getElementById('new_EN_'+id).value;
            document.getElementById('edit_EN_'+id).innerHTML = new_value;
            document.getElementById('edit_EN_'+ id).style.display = "block"
            document.getElementById('edited_EN_'+ id).style.display = "none";
            document.getElementById('button_EN_'+ id).style.display = "none";
            dataString = 'tag_en='+new_value+'&id='+id;
            $.ajax({
                url: "{{ route('update-tag') }}",
                data: dataString,
                dataType: "json",
                type: 'GET',
                success: function(response) {

                },
                error: function(xhr) {

                },
                complete: function() {}
            });
        }
        function Submit_FR(id) {
            var new_value = document.getElementById('new_FR_'+id).value;
            document.getElementById('edit_FR_'+id).innerHTML = new_value;
            document.getElementById('edit_FR_'+ id).style.display = "block";
            document.getElementById('edited_FR_'+ id).style.display = "none";
            document.getElementById('button_FR_'+ id).style.display = "none";
            dataString = 'tag_fr='+new_value+'&id='+id;
            $.ajax({
                url: "{{ route('update-tag') }}",
                data: dataString,
                dataType: "json",
                type: 'GET',
                success: function(response) {

                },
                error: function(xhr) {

                },
                complete: function() {}
            });
        }

        $(document).on('click', '.accept_tag', function(event) {
            var tag_id = event.target.id;;
            var dataString = 'id='+tag_id;
            $.ajax({
                url: "{{ route('accept-tag') }}",
                data: dataString,
                dataType: "json",
                type: 'GET',
                success: function(response) {

                },
                error: function(xhr) {

                },
                complete: function() {}
            });
        });

        $(document).on('click', '.reject_tag', function(event) {
            var tag_id = event.target.id;;
            var dataString = 'id='+tag_id;
            $.ajax({
                url: "{{ route('reject-tag') }}",
                data: dataString,
                dataType: "json",
                type: 'GET',
                success: function(response) {

                },
                error: function(xhr) {

                },
                complete: function() {}
            });
        });

    </script>

@endsection
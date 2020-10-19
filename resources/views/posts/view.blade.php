@extends('layouts.app')

@section('content')
    <form id="search_form">
       <div class="">
         <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" class="form-control" name="category">
                            <option value=""></option>
                            @foreach($catagories as $category)
                                <option value="{{$category->id}}" > {{$category->name}}</option>
                            @endforeach
                        </select>
         </div>
         <div class="form-group">
             <button type="button" class="btn btn-success" id="search_button">Search</button>
        </div>
        </div>
    </form>
    <div class="card card-default">
        <div class="card-header">
            Posts
        </div>
        <div class="card-body">
            <table class="table" id="post_table">
                <thead>
                   <th>Image</th>
                   <th>Title</th>
                   <th>Description</th>
                   <th>Content</th>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  
  getData();
  function getData(){
    $('#post_table').DataTable().destroy();
    $('#post_table').DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        "filter": false,
        "bSort" : false,
        sorting : false,
        "bLengthChange": false,
        pageLength : 5,
        responsive : true,
        "searching": false,
        "info"     : false,
        "language": {
          "paginate": {
            "previous": "Prev"
          }
        },

       ajax: {
          'url' : 'search-posts',
          'type': 'POST',
          'data':{
            'category_id':$('#category').val(),
            '_token':'{{ csrf_token() }}'
          },
        },
       columns: [
            { name : 'incoterm', data: null, render:function(data, type, row){
                return `<img src="{{ asset('storage/`+row.image+`') }}" width="60px" height="60px" alt="Post Image">`;
             }},
           { data: 'title', name: 'title'},
           { data: 'description', name: 'description'},
           { data: 'content', name: 'content'},
          
       ],
  });
}

$('#search_form').click(function(){
     getData();
});
</script>
@endsection

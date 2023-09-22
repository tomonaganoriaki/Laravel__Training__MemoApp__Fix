@extends('layouts.app')
@section('javascript')
<script>
    function deleteHandle(event){
        event.preventDefault();
    if(window.confirm('本当に削除してよろしいですか？')){
        document.getElementById('delete-form').submit();
    }else{
        alert('キャンセルしました')
    }
    }
</script>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            メモ編集
        </div>
        <form class="card-body" action="{{route('update')}}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{$edit_memo[0]['id']}}">
            <div class="mb-3">
                <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力">{{$edit_memo[0]->content}}</textarea>
            </div>
            @error('content')
            <div class="alert alert-danger">{{ 'メモ内容を入力して下さい！' }}</div>
            @enderror
            @foreach($tags as $t)
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" name="tags[]" id="{{$t['id']}}" value="{{$t['id']}}" {{ in_array($t['id'], $include_tags) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="{{$t['id']}}">{{$t['name']}}</label>
            </div>
            @endforeach
            <input type="text" class="form-control w-50 mb-3" name="new_tag" placeholder="新しいタグを入力">
            <button type="submit" class="btn btn-primary">更新</button>
        </form>
        <form class="card-body" id="delete-form" name="memo_id" action="{{route('destroy')}}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{$edit_memo[0]->id}}">
            <button class="btn btn-primary bg-red" type="submit" onclick="deleteHandle(event);">削除</button>
        </form>
    </div>
@endsection

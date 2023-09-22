<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\MemoTag;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
       
        $tags = Tag::where('user_id', Auth::id())->whereNull('deleted_at')->orderBy('updated_at', 'desc')->get();
        return view('create', compact('tags'));

    }
    public function store(Request $request)
    {
        $posts = $request->all();
        $request = $request->validate([
            'content' => 'required|max:500',
        ]);
    
        $memo_id = DB::transaction(function () use ($posts) {
            $memo_id = Memo::insertGetId(['content' => $posts['content'], 'user_id' => Auth::id()]);
            $tag_exists = Tag::where('user_id', Auth::id())->where('name', $posts['new_tag'])->exists();
            if (!empty($posts['new_tag']) && !$tag_exists) {
                $tag_id = Tag::insertGetId(['name' => $posts['new_tag'], 'user_id' => Auth::id()]);
                MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag_id]);
            }
            if (!empty($posts['tags'])) {
                foreach ($posts['tags'] as $tag) {
                    MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag]);
                }
            }
            return $memo_id; 
        });
    
        return redirect('/home');
    }
    
    
    public function edit($id)
    {

        $edit_memo = Memo::select('memos.*', 'tags.id as tag_id')
            ->leftJoin('memo_tags', 'memos.id', '=', 'memo_tags.memo_id')
            ->leftJoin('tags', 'memo_tags.tag_id', '=', 'tags.id')
            ->where('memos.user_id', Auth::id())
            ->where('memos.id', $id)
            ->whereNull('memos.deleted_at')
            ->get();
        $include_tags = [];
        foreach ($edit_memo as $memo) {
            if (!empty($memo->tag_id)) {
                array_push($include_tags, $memo->tag_id);
            }
        }
        $tags = Tag::where('user_id', Auth::id())->whereNull('deleted_at')->orderBy('updated_at', 'desc')->get();
        return view('edit', compact( 'edit_memo', 'include_tags','tags'));

    }
    public function update(Request $request)
    {
        $posts = $request->all();
        $request = $request->validate([
            'content' => 'required|max:500',
        ]);
        DB::transaction(function () use ($posts) {
            Memo::where('id', $posts['id'])->update(['content' => $posts['content']]);
            MemoTag::where('memo_id', $posts['id'])->delete();
            foreach ($posts['tags'] as $tag) {
                MemoTag::insert(['memo_id' => $posts['id'], 'tag_id' => $tag]);
            }
            $tag_exists = Tag::where('user_id', Auth::id())->where('name', $posts['new_tag'])->exists();
            if (!empty($posts['new_tag']) && !$tag_exists) {
                $tag_id = Tag::insertGetId(['name' => $posts['new_tag'], 'user_id' => Auth::id()]);
                MemoTag::insert(['memo_id' => $posts['id'], 'tag_id' => $tag_id]);
            }

        });



        Memo::where('id', $posts['id'])->update(['content' => $posts['content']]);
        return redirect('/home');
    }
    public function destroy(Request $request)
    {
        $memoId = $request->input('id');
        Memo::where(['id' => $memoId])->update(['deleted_at' => date("Y-m-d H:i:s")]);
        return redirect('/home');
    }
    
}

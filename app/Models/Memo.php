<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Request;

class Memo extends Model
{
    use HasFactory;

    public function getMyMemo(){
        $query_tag = Request::query('tag');
        if(!empty($query_tag)){
            $memos = Memo::where('user_id', Auth::id())
                ->leftJoin('memo_tags', 'memos.id', '=', 'memo_tags.memo_id')
                ->where('memo_tags.tag_id', $query_tag)
                ->whereNull('deleted_at')
                ->orderBy('updated_at', 'desc')
                ->get();
            }else{
                $memos = Memo::where('user_id', Auth::id())
                    ->whereNull('deleted_at')
                    ->orderBy('updated_at', 'desc')
                    ->get();
            }
            return $memos;
    }
}

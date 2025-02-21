<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function getPage(Request $request)
    {
        $pageName = $request->name;
        if($pageName == null){
            return  response()->json([], 404);
        }

        $page = Page::where('name','like', '%'.$pageName.'%')
            ->where('status','published')
            ->first();

        $page->image =  ($page->image != null)? url('/assets/img/page').'/'.$page->image : null;
    
        return response()->json($page);
    }
}

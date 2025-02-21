<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    public function getFaqs(Request $request)
    {
        $page = $request->page;
        $pageSize = $request->page_size;

        $skip = ($page - 1) * $pageSize;

        $faqs = Faq::where('status', 'published')
            ->skip($skip)
            ->take($pageSize)
            ->get();

        return response()->json($faqs);
    }
}

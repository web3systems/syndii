<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;
use App\Models\Chat;
use App\Models\Content;
use App\Models\Template;



class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query)) {
            return response()->json([]);
        }
        
        $searchResults = (new Search())
            ->registerModel(Chat::class, ['name', 'sub_name'])
            ->registerModel(Content::class, ['title'])
            ->registerModel(Template::class, ['name'])
            ->search($query);
        
        $groupedResults = [
            'chats' => [],
            'contents' => [],
            'templates' => []
        ];
        
        foreach ($searchResults->groupByType() as $type => $modelSearchResults) {
            foreach ($modelSearchResults as $searchResult) {
                $modelType = class_basename($searchResult->searchable);
                
                if ($modelType === 'Chat') {
                    $groupedResults['chats'][] = [
                        'id' => $searchResult->searchable->id,
                        'name' => $searchResult->searchable->name,
                        'logo' => $searchResult->searchable->logo,
                        'url' => url('/app/user/chats/' . $searchResult->searchable->chat_code)
                    ];
                } elseif ($modelType === 'Content') {
                    $groupedResults['contents'][] = [
                        'id' => $searchResult->searchable->id,
                        'title' => $searchResult->searchable->title,
                        'icon' => $searchResult->searchable->icon,
                        'url' => url('/app/user/document/result/' . $searchResult->searchable->id . '/show')
                    ];
                } elseif ($modelType === 'Template') {
                    $groupedResults['templates'][] = [
                        'id' => $searchResult->searchable->id,
                        'name' => $searchResult->searchable->name,
                        'icon' => $searchResult->searchable->icon,
                        'url' => url('/app/user/templates/original-template/' . $searchResult->searchable->slug)
                    ];
                }
            }
        }
        
        return response()->json($groupedResults);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\Source;
use App\Models\Author;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function savePreferences(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'category' => 'integer|exists:categories,id',
            'source' => 'integer|exists:sources,id',
            'author' => 'integer|exists:authors,id',
        ]);

        // Sync preferences with pivot tables
        $user->categories()->toggle($request->category);
        $user->sources()->toggle($request->source);
        $user->authors()->toggle($request->author);

        return response()->json(['message' => 'Preferences saved successfully!']);
    }

    public function getPreferences()
    {
        $user = auth()->user();
        $preferences = [
            'categories' => $user->categories()->pluck('category_id')->toArray(),
            'sources' => $user->sources()->pluck('source_id')->toArray(),
            'authors' => $user->authors()->pluck('author_id')->toArray(),
        ];

        return response()->json($preferences);
    }
}


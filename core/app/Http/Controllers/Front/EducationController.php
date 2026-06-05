<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\EducationArticle;
use App\Support\EducationPillarRegistry;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class EducationController extends Controller
{
    public function index()
    {
        $articles = collect();
        if (Schema::hasTable('education_articles')) {
            $articles = EducationArticle::where('published', true)->orderBy('sort_order')->orderByDesc('updated_at')->get();
        }

        return view('front.education.index', [
            'articles' => $articles,
            'pillars' => EducationPillarRegistry::definitions(),
        ]);
    }

    public function pillar(string $slug)
    {
        $definitions = EducationPillarRegistry::definitions();
        abort_unless(isset($definitions[$slug]), 404);
        abort_unless(View::exists('front.education.guides.' . $slug), 404);

        $meta = $definitions[$slug];

        return view('front.education.pillar', [
            'slug' => $slug,
            'meta' => $meta,
        ]);
    }

    public function compliance()
    {
        return view('front.education.compliance');
    }

    public function tool4cs()
    {
        return view('front.education.tool-4cs');
    }

    public function show(string $slug)
    {
        if (! Schema::hasTable('education_articles')) {
            abort(404);
        }

        $article = EducationArticle::where('slug', $slug)
            ->where('published', true)
            ->firstOrFail();
        $related = EducationArticle::where('slug', '!=', $slug)
            ->where('published', true)
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get();

        return view('front.education.show', compact('article', 'related'));
    }
}

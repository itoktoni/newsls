<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Modules\Cms\Models\Category;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\Tag;
use Modules\Cms\Models\Type;
use Modules\Cms\Services\ContainerRenderer;
use Modules\Cms\Services\ContentEntryExtractor;

class PublicController extends Controller
{
    public function api($slug = null)
    {
        $query = Content::with('has_type.has_sections')->where('status', 'published');

        if ($slug) {
            $entry = (clone $query)->where('slug', $slug)->firstOrFail();
        } else {
            $entry = (clone $query)->whereHas('has_type', fn ($q) => $q->where('slug', 'homepage'))
                ->firstOrFail();
        }

        return response()->json(ContentEntryExtractor::extract($entry));
    }

    public function index()
    {
        if (env('DISABLE_FRONTEND', false)) {
            return redirect()->route('login');
        }

        $contentTypes = Type::where('is_active', true)->get();

        $homeEntry = Content::with(['has_type.has_sections'])
            ->whereHas('has_type', fn ($q) => $q->where('slug', 'homepage'))
            ->where('status', 'published')
            ->first();

        $homeData = null;
        $homeHtml = null;
        if ($homeEntry) {
            $homeData = ContentEntryExtractor::extract($homeEntry);
            $homeHtml = ContainerRenderer::render($homeEntry);
        }

        return view('cms::frontend.index', [
            'contentTypes' => $contentTypes,
            'homeEntry' => $homeEntry,
            'homeData' => $homeData,
            'homeHtml' => $homeHtml,
        ]);
    }

    public function page($slug)
    {
        $entry = Content::with('has_type')
            ->whereHas('has_type', function ($q) {
                $q->where('slug', 'page');
            })
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return view('cms::frontend.page', ['entry' => $entry]);
    }

    public function blog()
    {
        $posts = Content::with('has_type')
            ->whereHas('has_type', function ($q) {
                $q->where('slug', 'post');
            })
            ->published()
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('cms::frontend.blog', ['posts' => $posts]);
    }

    public function post($slug)
    {
        $post = Content::with('has_type')
            ->whereHas('has_type', function ($q) {
                $q->where('slug', 'post');
            })
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return view('cms::frontend.post', ['entry' => $post]);
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Content::with('has_type')
            ->whereHas('has_type', function ($q) {
                $q->where('slug', 'post');
            })
            ->where('status', 'published')
            ->whereHas('has_categories', function ($q) use ($category) {
                $q->where('cms_categories.id', $category->id);
            })
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('cms::frontend.blog', [
            'posts' => $posts,
            'category' => $category,
        ]);
    }

    public function tag($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = Content::with('has_type')
            ->whereHas('has_type', function ($q) {
                $q->where('slug', 'post');
            })
            ->where('status', 'published')
            ->whereHas('has_tags', function ($q) use ($tag) {
                $q->where('cms_tags.id', $tag->id);
            })
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('cms::frontend.blog', [
            'posts' => $posts,
            'tag' => $tag,
        ]);
    }

    public function services()
    {
        $entry = Content::with('has_type.has_sections')
            ->whereHas('has_type', fn ($q) => $q->where('slug', 'services'))
            ->published()
            ->first();

        $data = null;
        $html = null;
        if ($entry) {
            $data = ContentEntryExtractor::extract($entry);
            $html = ContainerRenderer::render($entry);
        }

        return view('cms::frontend.services', [
            'entry' => $entry,
            'data' => $data,
            'html' => $html,
        ]);
    }

    public function contact(Request $request)
    {
        $entry = Content::with('has_type.has_sections')
            ->whereHas('has_type', fn ($q) => $q->where('slug', 'contact'))
            ->published()
            ->first();

        $data = null;
        $html = null;
        if ($entry) {
            $data = ContentEntryExtractor::extract($entry);
            $html = ContainerRenderer::render($entry);
        }

        return view('cms::frontend.contact', [
            'entry' => $entry,
            'data' => $data,
            'html' => $html,
        ]);
    }

    public function postContact(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'captcha' => ['required', 'numeric'],
            'captcha_key' => ['required', 'string'],
        ]);

        $key = $request->input('captcha_key');
        $answer = (int) $request->input('captcha');

        if (! $request->session()->has("captcha_$key") || $request->session()->get("captcha_$key") !== $answer) {
            return back()->withErrors(['captcha' => 'Captcha salah.'])->withInput();
        }

        $request->session()->forget("captcha_$key");

        ContactMessage::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Pesan Anda berhasil dikirim. Terima kasih!');
    }

    public function captchaImage(Request $request)
    {
        $key = $request->query('key', uniqid('captcha_', true));

        $num1 = random_int(1, 9);
        $num2 = random_int(1, 9);
        $operators = ['+', '-', '×'];
        $operator = $operators[random_int(0, count($operators) - 1)];

        switch ($operator) {
            case '+':
                $answer = $num1 + $num2;
                break;
            case '-':
                if ($num1 < $num2) {
                    [$num1, $num2] = [$num2, $num1];
                }
                $answer = $num1 - $num2;
                break;
            case '×':
                $answer = $num1 * $num2;
                break;
            default:
                $answer = $num1 + $num2;
        }

        session(["captcha_{$key}" => $answer]);

        $width = 160;
        $height = 64;
        $image = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($image, random_int(200, 240), random_int(200, 240), random_int(200, 240));
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        $text = "{$num1} {$operator} {$num2} = ?";
        $font = public_path('fonts/DejaVuSans.ttf');
        if (! file_exists($font)) {
            $font = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
        }
        if (! file_exists($font)) {
            $font = '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf';
        }
        if (! file_exists($font)) {
            $font = 'C:\\Windows\\Fonts\\arial.ttf';
        }

        $color = imagecolorallocate($image, random_int(0, 80), random_int(0, 80), random_int(0, 80));
        $fontSize = 18;
        $angle = random_int(-8, 8);

        if (file_exists($font)) {
            $bbox = imagettfbbox($fontSize, 0, $font, $text);
            $textWidth = $bbox[2] - $bbox[0];
            $x = (int) (($width - $textWidth) / 2);
            $y = (int) ($height / 2) + 8;
            imagettftext($image, $fontSize, $angle, $x, $y, $color, $font, $text);
        } else {
            $x = 30;
            $y = 42;
            imagestring($image, 5, $x, $y, $text, $color);
        }

        for ($i = 0; $i < 8; $i++) {
            $lineColor = imagecolorallocate($image, random_int(150, 220), random_int(150, 220), random_int(150, 220));
            imageline($image, random_int(0, $width), random_int(0, $height), random_int(0, $width), random_int(0, $height), $lineColor);
        }

        for ($i = 0; $i < 120; $i++) {
            $dotColor = imagecolorallocate($image, random_int(100, 180), random_int(100, 180), random_int(100, 180));
            imagesetpixel($image, random_int(0, $width), random_int(0, $height), $dotColor);
        }

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return response($imageData, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');

        $posts = Content::with('has_type')
            ->whereHas('has_type', fn ($q) => $q->where('slug', 'post'))
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        if ($query) {
            $posts->where(function ($q) use ($query) {
                $q->where('title', 'like', '%'.$query.'%')
                    ->orWhere('content', 'like', '%'.$query.'%')
                    ->orWhere('excerpt', 'like', '%'.$query.'%');
            });
        }

        $posts = $posts->orderByDesc('published_at')->paginate(12);

        return view('cms::frontend.search', [
            'posts' => $posts,
            'query' => $query,
        ]);
    }
}

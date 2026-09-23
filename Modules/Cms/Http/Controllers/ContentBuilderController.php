<?php

namespace Modules\Cms\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\Section;
use Modules\Cms\Models\Field;
use Modules\Cms\Models\Type;

class ContentBuilderController extends Controller
{
    /**
     * Display the dynamic content builder interface
     */
    public function index()
    {
        $types = Type::distinct()->pluck('type');
        $contentBlocks = Content::orderBy('sort_order')->get();
        return view('cms::dynamic-content-builder', compact('types', 'contentBlocks'));
    }

    /**
     * Get form schema for creating/editing content blocks
     */
    public function formSchema(Request $request)
    {
        $contentId = $request->input('content_id');
        $content = $contentId ? Content::find($contentId) : null;

        // Retrieve existing sections and fields
        $sections = Section::where('is_active', true)->get();
        $fields = Field::all();
        $types = Type::all();

        return response()->json([
            'sections' => $sections,
            'fields' => $fields,
            'types' => $types,
            'content' => $content,
        ]);
    }

    /**
     * Store a new content block
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:page,blog,product,ecommerce,custom',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_contents,slug',
            'content' => 'nullable|string',
            'meta' => 'nullable|array',
            'status' => 'in:draft,published,archived',
        ]);

        $validated['content_type_id'] = $request->input('content_type_id', 1);
        $validated['author_id'] = auth()->id() ?? 1;
        $validated['sort_order'] = Content::max('sort_order') + 1;

        $block = Content::create($validated);

        if ($request->filled('sections_order')) {
            $block->meta = array_merge($block->meta ?? [], [
                '_sections_order' => json_decode($request->input('sections_order'), true)
            ]);
            $block->save();
        }

        return redirect()->route('cms.content.edit', $block->id)
            ->with('message', 'Content block created successfully.');
    }

    /**
     * Update an existing content block
     */
    public function update(Request $request, $id)
    {
        $block = Content::find($id);

        $validated = $request->validate([
            'type' => 'required|string|in:page,blog,product,ecommerce,custom',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_contents,slug,'.$block->id,
            'content' => 'nullable|string',
            'meta' => 'nullable|array',
            'status' => 'in:draft,published,archived',
        ]);

        $block->update($validated);

        return redirect()->route('cms.content.edit', $block->id)
            ->with('message', 'Content block updated successfully.');
    }

    /**
     * Delete a content block
     */
    public function destroy($id)
    {
        $block = Content::find($id);
        $block->delete();

        return redirect()->route('cms.content.index')
            ->with('message', 'Content block deleted successfully.');
    }

    /**
     * Reorder content blocks via drag-and-drop
     */
    public function reorder(Request $request)
    {
        $order = $request->input('order', []);

        foreach ($order as $index => $contentId) {
            Content::where('id', $contentId)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get all content blocks (for frontend rendering)
     */
    public function getContentBlocks()
    {
        $blocks = Content::where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('sort_order')
            ->get();

        return response()->json($blocks);
    }
}

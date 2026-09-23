<?php

namespace Modules\Cms\Http\Livewire;

use Livewire\Component;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\Section;
use Modules\Cms\Models\Field;
use Modules\Cms\Models\Type;

class DynamicContentBuilder extends Component
{
    public $contentBlocks;
    public $editingId = null;
    public $showModal = false;

    public $title = '';
    public $slug = '';
    public $content = '';
    public $type = '';
    public $status = 'draft';
    public $meta = [];
    public $sectionsOrder = [];

    protected $listeners = ['sectionAdded', 'sectionUpdated', 'sectionDeleted', 'refreshBuilder' => 'refresh'];

    public function mount()
    {
        $this->contentBlocks = Content::orderBy('sort_order')->get();
    }

    public function create()
    {
        $this->reset([
            'title', 'slug', 'content', 'type', 'status', 'meta', 'sectionsOrder', 'editingId'
        ]);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $block = Content::findOrFail($id);
        $this->editingId = $id;
        $this->title = $block->title;
        $this->slug = $block->slug;
        $this->content = $block->content;
        $this->type = $block->type;
        $this->status = $block->status;
        $this->meta = $block->meta ?? [];
        $this->sectionsOrder = $block->meta['_sections_order'] ?? [];
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|string',
            'status' => 'in:draft,published,archived',
        ]);

        $meta = $this->meta;
        if (!empty($this->sectionsOrder)) {
            $meta['_sections_order'] = $this->sectionsOrder;
        }

        if ($this->editingId) {
            Content::where('id', $this->editingId)->update([
                'title' => $this->title,
                'slug' => $this->slug ?: $this->title,
                'content' => $this->content,
                'type' => $this->type,
                'status' => $this->status,
                'meta' => $meta,
            ]);
        } else {
            $maxOrder = Content::max('sort_order') ?? 0;
            Content::create([
                'title' => $this->title,
                'slug' => $this->slug ?: $this->title,
                'content' => $this->content,
                'type' => $this->type,
                'status' => $this->status,
                'meta' => $meta,
                'author_id' => auth()->id(),
                'content_type_id' => 1,
                'sort_order' => $maxOrder + 1,
            ]);
        }

        $this->showModal = false;
        $this->reset(['title', 'slug', 'content', 'type', 'status', 'meta', 'sectionsOrder', 'editingId']);
        $this->mount();

        $this->dispatch('toast', ['message' => 'Content block saved successfully!']);
    }

    public function delete($id)
    {
        $block = Content::findOrFail($id);
        $block->delete();
        $this->mount();
        $this->dispatch('toast', ['message' => 'Content block deleted!']);
    }

    public function updatedSectionsOrder($value)
    {
        $this->sectionsOrder = json_decode($value, true) ?? [];
    }

    public function reorder()
    {
        $order = request()->input('order', []);
        foreach ($order as $index => $contentId) {
            Content::where('id', $contentId)->update(['sort_order' => $index]);
        }

        $this->mount();
        return response()->json(['success' => true]);
    }

    public function refresh()
    {
        $this->contentBlocks = Content::orderBy('sort_order')->get();
    }

    public function render()
    {
        $types = Type::all();
        $sections = Section::where('is_active', true)->get();
        $fields = Field::all();

        return view('cms::livewire.dynamic-content-builder', [
            'types' => $types,
            'sections' => $sections,
            'fields' => $fields,
        ]);
    }
}

<?php /** @var Modules\Cms\Models\Content $table */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => ucfirst(modules())]]" />
    <div class="content mt-4 lg:mt-0">
        <x-filter :per-page="25" :fields="$fields">
            <x-slot:advanced>
                @foreach ($fields as $key => $advance)
                <x-filter-item :label="$advance" :name="$key"/>
                @endforeach
                <x-button variant="primary" class="btn-block" onclick="applyAdvanced()">Apply</x-button>
                <x-button variant="soft" class="btn-block" onclick="resetAdvanced()">Reset</x-button>
            </x-slot:advanced>
        </x-filter>

        @php
            $tabQuery = request()->query();
            unset($tabQuery['cursor'], $tabQuery['page']);
        @endphp
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-2 mb-4 form-card">
            <div class="flex gap-1 overflow-x-auto">
                @foreach ($typeTabs as $typeTab)
                    @php
                        $tabQuery['filters']['has_type']['slug'] = ['$eq' => $typeTab['slug']];
                        $isActive = ($activeTypeSlug ?? '') === $typeTab['slug'];
                    @endphp
                    <a href="{{ url()->current() . '?' . http_build_query($tabQuery) }}"
                       class="px-4 py-2 rounded-full font-body-sm text-body-sm font-semibold transition-colors whitespace-nowrap {{ $isActive ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                        {{ ucfirst($typeTab['slug']) }}
                    </a>
                @endforeach
            </div>
        </div>

        @php
            $currentSort = request('sort.0', '');
            $sortField = str_replace(':desc','',str_replace(':asc','',$currentSort));
            $sortDir = str_contains($currentSort, ':desc') ? 'desc' : 'asc';
        @endphp

        <x-table>
            <x-slot:head>
                <x-table-checkbox :model="$model" onchange="toggleAll(this)" />
                <th>Actions</th>
                <th>Title</th>
                <th>Slug</th>
                <th>Type</th>
                <th>Status</th>
                <th>Published At</th>
            </x-slot:head>
            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    <td>{{ $table->title }}</td>
                    <td>{{ $table->slug }}</td>
                    <td>{{ $contentTypes[$table->content_type_id] ?? '-' }}</td>
                    <td>
                        @if($table->status === 'published')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                        @elseif($table->status === 'draft')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $table->status }}</span>
                        @endif
                    </td>
                    <td>{{ isset($table->published_at) ? \Illuminate\Support\Carbon::parse($table->published_at)->format('d M Y H:i') : '-' }}</td>
                </tr>
                @endforeach
            </x-slot:body>
            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <x-table-mobile-list>
                    @foreach($data as $table)
                    <x-table-mobile-item :id="$table->field_primary">
                        <x-table-mobile-header :title="$table->title" />
                        <div class="mt-2 space-y-1.5">
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-xs text-on-surface-variant">Type</span>
                                <span class="text-sm font-medium text-right">{{ $contentTypes[$table->content_type_id] ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-xs text-on-surface-variant">Status</span>
                                @if($table->status === 'published')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full border border-green-200 bg-green-50 text-green-700">Published</span>
                                @elseif($table->status === 'draft')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full border border-amber-200 bg-amber-50 text-amber-700">Draft</span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full border border-gray-200 bg-gray-100 text-gray-600">{{ $table->status }}</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-xs text-on-surface-variant">Published</span>
                                <span class="text-sm font-medium text-right">{{ isset($table->published_at) ? \Illuminate\Support\Carbon::parse($table->published_at)->format('d M Y H:i') : '-' }}</span>
                            </div>
                        </div>
                        <x-table-mobile-footer :label="'#' . $table->field_primary">
                            <x-table-action :model="$model" :id="$table->field_primary" />
                        </x-table-mobile-footer>
                    </x-table-mobile-item>
                    @endforeach
                </x-table-mobile-list>
            </x-slot:mobile>
        </x-table>

        <x-pagination :paginator="$data" />
        <x-action :model="$model" :action="['create', 'delete']"/>
    </div>

    <input type="hidden" class="module" value="{{ modules() }}">
    <script src="/js/table.js?v=4"></script>
    <script>initTable('{{ $sortField }}', '{{ $sortDir }}');</script>
</x-layouts::app>

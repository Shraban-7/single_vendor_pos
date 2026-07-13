@extends('admin.layouts.app')
@section('title', 'Categories')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Categories</h1>
        <p class="text-xs text-slate-500">Organize and structure your nested product catalog taxonomies.</p>
    </div>
    <div>
        <button onclick="openCreateModal()" class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>Add Category</span>
        </button>
    </div>
</div>

{{-- Data Dense Nested Structure Container --}}
<div class="space-y-3">
    @foreach($categories as $category)
        <div class="overflow-hidden bg-white border shadow-sm border-slate-200 rounded-xl">

            {{-- Parent Category Header Bar --}}
            <div class="flex justify-between items-center bg-slate-50/80 px-4 py-2.5 border-b border-slate-100">
                <div class="flex items-center min-w-0 gap-3">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="flex-shrink-0 object-cover w-8 h-8 bg-white border rounded border-slate-200">
                    @else
                        <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-xs border rounded bg-slate-100 border-slate-200/60 text-slate-400">
                            <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i>
                        </div>
                    @endif

                    <div class="flex items-center min-w-0 gap-2">
                        <span class="text-sm font-bold truncate text-slate-800">{{ $category->name }}</span>
                        <div class="flex items-center flex-shrink-0 gap-1">
                            @if($category->is_active)
                                <span class="px-1.5 py-0.5 text-[9px] font-medium rounded bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                            @else
                                <span class="px-1.5 py-0.5 text-[9px] font-medium rounded bg-slate-100 text-slate-500 border border-slate-200/60">Inactive</span>
                            @endif

                            @if($category->is_featured)
                                <span class="px-1.5 py-0.5 text-[9px] font-medium rounded bg-indigo-50 text-indigo-700 border border-indigo-100">★ Featured</span>
                            @endif
                            <span class="text-[10px] text-slate-400 font-mono">Order: {{ $category->sort_order }}</span>
                        </div>
                    </div>
                </div>

                {{-- Action Strip --}}
                <div class="flex items-center gap-0.5">
                    <button type="button"
                        onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->parent_id ?? 'null' }}, '{{ $category->icon }}', {{ $category->sort_order }}, {{ $category->is_active ? 'true' : 'false' }}, {{ $category->is_featured ? 'true' : 'false' }}, '{{ $category->image }}')"
                        class="p-1 transition rounded text-slate-400 hover:text-indigo-600 hover:bg-slate-100" title="Edit Category">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </button>

                    <form action="{{ route('admin.categories.delete', $category->id) }}"
                        method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                        @csrf
                        @method('DELETE')
                        <button class="p-1 transition rounded text-slate-400 hover:text-rose-600 hover:bg-slate-100" title="Delete Category">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Children Subcategories Strip --}}
            <div class="bg-white divide-y divide-slate-100">
                @forelse($category->children as $sub)
                    <div class="relative flex items-center justify-between px-4 py-2 pl-8 transition-colors hover:bg-slate-50/40">
                        {{-- Structural Tree Line Accent --}}
                        <div class="absolute top-0 bottom-0 w-3 h-5 border-b border-l left-4 border-slate-200 rounded-bl-md"></div>

                        <div class="flex items-center gap-2.5 min-w-0">
                            @if($sub->image)
                                <img src="{{ asset('storage/' . $sub->image) }}" alt="{{ $sub->name }}" class="flex-shrink-0 object-cover border rounded w-7 h-7 border-slate-200 bg-slate-50">
                            @else
                                <div class="w-7 h-7 bg-slate-50 rounded border border-slate-200/50 flex items-center justify-center flex-shrink-0 text-slate-400 text-[10px]">
                                    <i class="{{ $sub->icon ?? 'fas fa-tag' }}"></i>
                                </div>
                            @endif

                            <div class="flex items-center min-w-0 gap-2">
                                <span class="text-xs font-medium truncate text-slate-700">{{ $sub->name }}</span>
                                <div class="flex items-center flex-shrink-0 gap-1">
                                    @if($sub->is_active)
                                        <span class="px-1 py-0.1 text-[8px] font-medium bg-emerald-50 text-emerald-600 rounded">Active</span>
                                    @else
                                        <span class="px-1 py-0.1 text-[8px] font-medium bg-slate-100 text-slate-400 rounded">Inactive</span>
                                    @endif
                                    <span class="text-[9px] text-slate-400 font-mono">Order: {{ $sub->sort_order }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-0.5">
                            <button type="button"
                                onclick="openEditModal({{ $sub->id }}, '{{ addslashes($sub->name) }}', {{ $sub->parent_id ?? 'null' }}, '{{ $sub->icon }}', {{ $sub->sort_order }}, {{ $sub->is_active ? 'true' : 'false' }}, {{ $sub->is_featured ? 'true' : 'false' }}, '{{ $sub->image }}')"
                                class="p-1 transition rounded text-slate-400 hover:text-indigo-600 hover:bg-slate-100" title="Edit Subcategory">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            </button>

                            <form action="{{ route('admin.categories.delete', $sub->id) }}"
                                method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this subcategory?');">
                                @csrf
                                @method('DELETE')
                                <button class="p-1 transition rounded text-slate-400 hover:text-rose-600 hover:bg-slate-100" title="Delete Subcategory">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-2 pl-8 text-xs italic text-slate-400/90">
                        No nested subcategories attached.
                    </div>
                @endforelse
            </div>

        </div>
    @endforeach
</div>

{{-- Add Category Modal --}}
<div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div onclick="closeCreateModal()" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>

        <div class="relative inline-block w-full max-w-md p-5 my-8 text-left align-middle transition-all bg-white shadow-xl rounded-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Add New Category</h3>
                <button type="button" onclick="closeCreateModal()" class="transition text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-3.5 text-xs">
                    <div>
                        <x-input name="name" type="text" label="Category Name *" placeholder="e.g. Men's Fashion" required class="text-xs h-9" />
                    </div>

                    <div>
                        <label class="block mb-1 text-xs font-semibold text-slate-600">Parent Category</label>
                        <select name="parent_id" class="block w-full px-2.5 h-9 text-xs rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition">
                            <option value="">None (Root Category)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1 text-xs font-semibold text-slate-600">Category Thumbnail</label>
                        <input type="file" name="image" accept="image/*" class="block w-full px-2 py-1 h-9 text-xs rounded-lg border border-slate-200 bg-slate-50/50 focus:outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-[11px] file:font-semibold file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300 transition" onchange="previewCreateImage(event)">
                        <div id="createImagePreview" class="hidden mt-2">
                            <img src="" alt="Preview" class="object-cover w-16 h-16 border rounded border-slate-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input name="icon" type="text" label="Icon Class" placeholder="fas fa-tag" required class="text-xs h-9" />
                        </div>
                        <div>
                            <x-input name="sort_order" type="number" value="0" label="Sort Order" required class="text-xs h-9" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="is_active" checked class="rounded border-slate-300 text-indigo-600 focus:ring-0 w-3.5 h-3.5">
                            <span class="ml-1.5 font-medium text-slate-700">Active Status</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="is_featured" class="rounded border-slate-300 text-indigo-600 focus:ring-0 w-3.5 h-3.5">
                            <span class="ml-1.5 font-medium text-slate-700">Featured Placement</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeCreateModal()" class="px-3 text-xs font-medium transition border rounded-lg h-9 text-slate-600 bg-slate-50 border-slate-200 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 shadow-sm transition">
                        <i data-lucide="save" class="w-3.5 h-3.5"></i>
                        <span>Save Category</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Category Modal --}}
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div onclick="closeEditModal()" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>

        <div class="relative inline-block w-full max-w-md p-5 my-8 text-left align-middle transition-all bg-white shadow-xl rounded-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Edit Category</h3>
                <button type="button" onclick="closeEditModal()" class="transition text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-3.5 text-xs">
                    <div>
                        <x-input name="name" type="text" label="Category Name *" id="edit_name" placeholder="e.g. Men's Fashion" required class="text-xs h-9" />
                    </div>

                    <div>
                        <label class="block mb-1 text-xs font-semibold text-slate-600">Parent Category</label>
                        <select id="edit_parent_id" name="parent_id" class="block w-full px-2.5 h-9 text-xs rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition">
                            <option value="">None (Root Category)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1 text-xs font-semibold text-slate-600">Category Thumbnail</label>
                        <input type="file" name="image" accept="image/*" class="block w-full px-2 py-1 h-9 text-xs rounded-lg border border-slate-200 bg-slate-50/50 focus:outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-[11px] file:font-semibold file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300 transition" onchange="previewEditImage(event)">
                        <div id="editImagePreview" class="mt-2">
                            <img id="edit_image_preview" src="" alt="Preview" class="object-cover w-16 h-16 border rounded border-slate-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input name="icon" type="text" label="Icon Class" id="edit_icon" required class="text-xs h-9" />
                        </div>
                        <div>
                            <x-input name="sort_order" type="number" id="edit_sort_order" label="Sort Order" required class="text-xs h-9" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" id="edit_is_active" name="is_active" class="rounded border-slate-300 text-indigo-600 focus:ring-0 w-3.5 h-3.5">
                            <span class="ml-1.5 font-medium text-slate-700">Active Status</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" id="edit_is_featured" name="is_featured" class="rounded border-slate-300 text-indigo-600 focus:ring-0 w-3.5 h-3.5">
                            <span class="ml-1.5 font-medium text-slate-700">Featured Placement</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeEditModal()" class="px-3 text-xs font-medium transition border rounded-lg h-9 text-slate-600 bg-slate-50 border-slate-200 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                        <i data-lucide="save" class="w-3.5 h-3.5"></i>
                        <span>Update Category</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function openEditModal(id, name, parentId, icon, sortOrder, isActive, isFeatured, image) {
        document.getElementById('editForm').action = '{{ route("admin.categories.index") }}/' + id + '/update';
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_parent_id').value = parentId || '';
        document.getElementById('edit_icon').value = icon;
        document.getElementById('edit_sort_order').value = sortOrder;
        document.getElementById('edit_is_active').checked = isActive;
        document.getElementById('edit_is_featured').checked = isFeatured;

        const imagePreview = document.getElementById('editImagePreview');
        const imagePreviewImg = document.getElementById('edit_image_preview');
        if (image) {
            imagePreviewImg.src = '{{ asset("storage") }}/' + image;
            imagePreview.classList.remove('hidden');
        } else {
            imagePreview.classList.add('hidden');
        }

        const parentSelect = document.getElementById('edit_parent_id');
        Array.from(parentSelect.options).forEach(option => {
            if (option.value == id) {
                option.style.display = 'none';
            } else {
                option.style.display = 'block';
            }
        });

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });

    function previewCreateImage(event) {
        const preview = document.getElementById('createImagePreview');
        const img = preview.querySelector('img');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
        }
    }

    function previewEditImage(event) {
        const img = document.getElementById('edit_image_preview');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                document.getElementById('editImagePreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush

@endsection

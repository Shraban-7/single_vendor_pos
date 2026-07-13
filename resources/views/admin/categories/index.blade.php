@extends('admin.layouts.app')
@section('title', 'Categories')

@section('content')

<div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h3 class="text-2xl font-bold text-slate-800 font-heading">Categories</h3>

        <button onclick="openCreateModal()"
            class="px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-medium rounded-lg hover:from-indigo-600 hover:to-indigo-700 transition shadow-sm">
            <i data-lucide="plus" class="w-4 h-4 fill-current mr-2"></i> Add New Category
        </button>
    </div>

    <div class="space-y-6">
        @foreach($categories as $category)
        <div class="border rounded-lg bg-white">

            {{-- Parent Category --}}
            <div class="flex justify-between items-center bg-slate-100 px-4 py-3 rounded-t-lg">

                <div class="flex items-center gap-3">
                    @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-12 h-12 object-cover rounded-lg border border-slate-300">
                    @else
                    <div class="w-12 h-12 bg-slate-200 rounded-lg flex items-center justify-center">
                        <i class="{{ $category->icon ?? 'fas fa-image' }} text-slate-400"></i>
                    </div>
                    @endif
                    <h3 class="font-semibold text-slate-800">
                        {{ $category->name }}
                    </h3>
                </div>

                <div class="space-x-3">
                    <button type="button"
                        onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->parent_id ?? 'null' }}, '{{ $category->icon }}', {{ $category->sort_order }}, {{ $category->is_active ? 'true' : 'false' }}, {{ $category->is_featured ? 'true' : 'false' }}, '{{ $category->image }}')"
                        class="text-indigo-500 text-sm hover:underline">
                        Edit
                    </button>

                    <form action="{{ route('admin.categories.delete', $category->id) }}"
                        method="POST"
                        class="inline"
                        onsubmit="return confirm('Are you sure you want to delete this category?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-rose-500 text-sm hover:underline">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            {{-- Subcategories --}}
            <div class="divide-y">
                @forelse($category->children as $sub)
                <div class="flex justify-between items-center px-6 py-3">

                    <div class="flex items-center gap-3">
                        @if($sub->image)
                        <img src="{{ asset('storage/' . $sub->image) }}" alt="{{ $sub->name }}" class="w-10 h-10 object-cover rounded-lg border border-slate-300">
                        @else
                        <div class="w-10 h-10 bg-slate-200 rounded-lg flex items-center justify-center">
                            <i class="{{ $sub->icon ?? 'fas fa-image' }} text-slate-400 text-sm"></i>
                        </div>
                        @endif
                        <span class="text-slate-700">
                            {{ $sub->name }}
                        </span>
                    </div>

                    <div class="space-x-3">
                        <button type="button"
                            onclick="openEditModal({{ $sub->id }}, '{{ addslashes($sub->name) }}', {{ $sub->parent_id ?? 'null' }}, '{{ $sub->icon }}', {{ $sub->sort_order }}, {{ $sub->is_active ? 'true' : 'false' }}, {{ $sub->is_featured ? 'true' : 'false' }}, '{{ $sub->image }}')"
                            class="text-indigo-500 text-sm hover:underline">
                            Edit
                        </button>

                        <form action="{{ route('admin.categories.delete', $sub->id) }}"
                            method="POST"
                            class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this subcategory?');">
                            @csrf
                            @method('DELETE')
                            <button class="text-rose-500 text-sm hover:underline">
                                Delete
                            </button>
                        </form>

                    </div>
                </div>
                @empty
                <div class="px-6 py-3 text-sm text-slate-400">
                    No subcategories
                </div>
                @endforelse
            </div>

        </div>
        @endforeach
    </div>

    {{-- Add Category Modal --}}
    <div id="createModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background Backdrop --}}
            <div onclick="closeCreateModal()" class="fixed inset-0 bg-slate-900/75 transition-opacity"></div>

            {{-- Modal Content --}}
            <div class="inline-block w-full max-w-lg p-6 my-8 text-left align-middle bg-white shadow-xl rounded-2xl relative">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900">Add New Category</h3>
                    <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-500 transition">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-input name="name" type="text" label="Category Name *" placeholder="e.g. Men's Fashion" required />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Parent Category</label>
                            <select name="parent_id"
                                class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="">None (Root Category)</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Category Image</label>
                            <input type="file" name="image" accept="image/*"
                                class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                onchange="previewCreateImage(event)">
                            <div id="createImagePreview" class="mt-3 hidden">
                                <img src="" alt="Preview" class="w-24 h-24 object-cover rounded-lg border border-slate-300">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input name="icon" type="text" label="Icon Class" placeholder="fas fa-tag" required />
                                <p class="mt-1 text-xs text-slate-500">FontAwesome icon class</p>
                            </div>
                            <div>
                                <x-input name="sort_order" type="number" value="0" label="Sort Order" placeholder="fas fa-tag" required />
                            </div>
                        </div>

                        <div class="flex items-center gap-6 pt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" checked
                                    class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-2">
                                <span class="ml-2 text-sm text-slate-700">Active</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_featured"
                                    class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-2">
                                <span class="ml-2 text-sm text-slate-700">Featured</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" onclick="closeCreateModal()"
                            class="px-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                            <i data-lucide="save" class="w-4 h-4 fill-current mr-2"></i>Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Category Modal --}}
    <div id="editModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background Backdrop --}}
            <div onclick="closeEditModal()" class="fixed inset-0 bg-slate-900/75 transition-opacity"></div>

            {{-- Modal Content --}}
            <div class="inline-block w-full max-w-lg p-6 my-8 text-left align-middle bg-white shadow-xl rounded-2xl relative">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900">Edit Category</h3>
                    <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-500 transition">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <x-input name="name" type="text" label="Category Name *" id="edit_name" placeholder="e.g. Men's Fashion" required />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Parent Category</label>
                            <select id="edit_parent_id" name="parent_id"
                                class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="">None (Root Category)</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Category Image</label>
                            <input type="file" name="image" accept="image/*"
                                class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                onchange="previewEditImage(event)">
                            <div id="editImagePreview" class="mt-3">
                                <img id="edit_image_preview" src="" alt="Preview" class="w-24 h-24 object-cover rounded-lg border border-slate-300">
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Leave empty to keep current image</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input name="icon" type="text" label="Icon Class" id="edit_icon" required />
                                <p class="mt-1 text-xs text-slate-500">FontAwesome icon class</p>
                            </div>
                            <div>
                                <x-input name="sort_order" type="number" id="edit_sort_order" label="Sort Order" required />
                            </div>
                        </div>

                        <div class="flex items-center gap-6 pt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="edit_is_active" name="is_active"
                                    class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-2">
                                <span class="ml-2 text-sm text-slate-700">Active</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="edit_is_featured" name="is_featured"
                                    class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-2">
                                <span class="ml-2 text-sm text-slate-700">Featured</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                            <i data-lucide="save" class="w-4 h-4 fill-current mr-2"></i>Update Category
                        </button>
                    </div>
                </form>
            </div>
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

        // Update image preview
        const imagePreview = document.getElementById('editImagePreview');
        const imagePreviewImg = document.getElementById('edit_image_preview');
        if (image) {
            imagePreviewImg.src = '{{ asset("storage") }}/' + image;
            imagePreview.classList.remove('hidden');
        } else {
            imagePreview.classList.add('hidden');
        }

        // Hide the parent option that matches the current category
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

    // Close modals on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });

    // Image preview functions
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
@Endpush

@endsection

@extends('admin.layouts.app')
@section('title', 'Banners')

@section('content')
<div>
    {{-- Page Header --}}
    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Banners & Sliders</h1>
            <p class="text-xs text-slate-500">Manage homepage hero sliders and promotional banners</p>
        </div>
        <div>
            <button onclick="openCreateModal()"
                class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Add New Banner</span>
            </button>
        </div>
    </div>

    {{-- Data Dense Banners Table --}}
    <div class="overflow-hidden bg-white border shadow-sm border-slate-200 rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Preview</th>
                        <th class="px-4 py-3">Banner Details</th>
                        <th class="px-4 py-3">Position</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100">
                    @forelse($banners as $banner)
                    <tr class="transition-colors hover:bg-slate-50/60">
                        <td class="px-4 py-2 whitespace-nowrap">
                            <div class="h-14 w-24 rounded overflow-hidden border border-slate-200 bg-slate-100">
                                <img src="{{ asset('storage/' . $banner->image) }}"
                                    class="h-full w-full object-cover"
                                    alt="{{ $banner->title }}">
                            </div>
                        </td>

                        <td class="px-4 py-2">
                            <div class="font-semibold text-slate-800 text-sm">{{ $banner->title }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $banner->subtitle ?? 'No subtitle' }}</div>
                        </td>

                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-indigo-50 text-indigo-700 border border-indigo-100/70">
                                {{ strtoupper($banner->position->value) }}
                            </span>
                        </td>

                        <td class="px-4 py-2.5 whitespace-nowrap">
                            @if($banner->is_active)
                            <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                            @else
                            <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-slate-50 text-slate-500 border border-slate-200/80">Inactive</span>
                            @endif
                        </td>

                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 font-mono text-[11px]">
                            {{ $banner->sort_order }}
                        </td>

                        <td class="px-4 py-2.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-0.5">
                                <button type="button"
                                    onclick="openEditModal({{ $banner->id }}, '{{ addslashes($banner->title) }}', '{{ addslashes($banner->subtitle) }}', '{{ addslashes($banner->description) }}', '{{ addslashes($banner->button_text) }}', '{{ addslashes($banner->button_link) }}', '{{ $banner->image }}', '{{ $banner->mobile_image }}', '{{ $banner->position->value }}', {{ $banner->sort_order }}, {{ $banner->is_active ? 'true' : 'false' }}, '{{ $banner->starts_at?->format('Y-m-d') }}', '{{ $banner->expires_at?->format('Y-m-d') }}')"
                                    class="p-1 transition rounded text-slate-400 hover:text-emerald-600 hover:bg-slate-100"
                                    title="Edit Banner">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>

                                <form action="{{ route('admin.banners.delete', $banner->id) }}" method="POST" onsubmit="return confirm('Delete this banner?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-1 transition rounded text-slate-400 hover:text-rose-600 hover:bg-slate-100"
                                        title="Delete">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center text-slate-500">
                            <div class="flex flex-col items-center max-w-xs mx-auto">
                                <div class="flex items-center justify-center w-12 h-12 mb-3 border rounded-xl bg-slate-50 text-slate-400 border-slate-100">
                                    <i data-lucide="image" class="w-5 h-5"></i>
                                </div>
                                <h3 class="font-bold text-slate-900">No banners found</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Click "Add New" to create your first banner.</p>
                                <button onclick="openCreateModal()" class="inline-block mt-3 text-xs font-semibold text-indigo-600 hover:underline">Create Banner</button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Banner Modal --}}
    <div id="createModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background Backdrop --}}
            <div onclick="closeCreateModal()" class="fixed inset-0 bg-slate-900/75 transition-opacity"></div>

            {{-- Modal Content --}}
            <div class="inline-block w-full max-w-2xl p-6 my-8 text-left align-middle bg-white shadow-xl rounded-2xl relative">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900">Add New Banner</h3>
                    <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-500 transition">
                        <i data-lucide="x"  class="w-6 h-6 fill-current text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <x-input name="title" type="text" label="Banner Title *" placeholder="e.g. Summer Sale 2024" required />
                            </div>

                            <div class="col-span-2">
                                <x-input name="subtitle" type="text" label="Subtitle" placeholder="e.g. Up to 50% Off" />
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                                <textarea name="description" rows="3"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                    placeholder="Banner description..."></textarea>
                            </div>

                            <div>
                                <x-input name="button_text" type="text" label="Button Text" placeholder="e.g. Shop Now" />
                            </div>

                            <div>
                                <x-input name="button_link" type="text" label="Button Link" placeholder="e.g. /shop" />
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Desktop Image *</label>
                                <input type="file" name="image" accept="image/*" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                    onchange="previewCreateImage(event)">
                                <div id="createImagePreview" class="mt-3 hidden">
                                    <img src="" alt="Preview" class="max-h-32 rounded-lg border border-slate-300">
                                </div>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Mobile Image (Optional)</label>
                                <input type="file" name="mobile_image" accept="image/*"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                    onchange="previewCreateMobileImage(event)">
                                <div id="createMobileImagePreview" class="mt-3 hidden">
                                    <img src="" alt="Preview" class="max-h-32 rounded-lg border border-slate-300">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Position *</label>
                                <select name="position" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition">
                                    <option value="hero">Hero Slider</option>
                                    <option value="promotional">Promotional Banner</option>
                                    <option value="category">Category Banner</option>
                                    <option value="festival">Festival Banner</option>
                                </select>
                            </div>

                            <div>
                                <x-input name="sort_order" type="number" value="0" label="Sort Order *" required />
                            </div>

                            <div>
                                <x-input name="starts_at" type="date" label="Start Date" />
                            </div>

                            <div>
                                <x-input name="expires_at" type="date" label="Expiry Date" />
                            </div>

                            <div class="col-span-2">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" checked
                                        class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-2">
                                    <span class="ml-2 text-sm text-slate-700">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t pt-4">
                        <button type="button" onclick="closeCreateModal()"
                            class="px-3.5 h-9 inline-flex items-center text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-3.5 h-9 inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                            <i data-lucide="save" class="w-3.5 h-3.5"></i> Save Banner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Banner Modal --}}
    <div id="editModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background Backdrop --}}
            <div onclick="closeEditModal()" class="fixed inset-0 bg-slate-900/75 transition-opacity"></div>

            {{-- Modal Content --}}
            <div class="inline-block w-full max-w-2xl p-6 my-8 text-left align-middle bg-white shadow-xl rounded-2xl relative">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900">Edit Banner</h3>
                    <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-500 transition">
                        <i data-lucide="x"  class="w-6 h-6 fill-current text-xl"></i>
                    </button>
                </div>

                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <x-input name="title" type="text" label="Banner Title *" id="edit_title" required />
                            </div>

                            <div class="col-span-2">
                                <x-input name="subtitle" type="text" label="Subtitle" id="edit_subtitle" />
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                                <textarea name="description" id="edit_description" rows="3"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                    placeholder="Banner description..."></textarea>
                            </div>

                            <div>
                                <x-input name="button_text" type="text" label="Button Text" id="edit_button_text" />
                            </div>

                            <div>
                                <x-input name="button_link" type="text" label="Button Link" id="edit_button_link" />
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Desktop Image</label>
                                <input type="file" name="image" accept="image/*"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                    onchange="previewEditImage(event)">
                                <div id="editImagePreview" class="mt-3">
                                    <img id="edit_image_preview" src="" alt="Preview" class="max-h-32 rounded-lg border border-slate-300">
                                </div>
                                <p class="mt-1 text-xs text-slate-500">Leave empty to keep current image</p>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Mobile Image (Optional)</label>
                                <input type="file" name="mobile_image" accept="image/*"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                    onchange="previewEditMobileImage(event)">
                                <div id="editMobileImagePreview" class="mt-3">
                                    <img id="edit_mobile_image_preview" src="" alt="Preview" class="max-h-32 rounded-lg border border-slate-300">
                                </div>
                                <p class="mt-1 text-xs text-slate-500">Leave empty to keep current mobile image</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Position *</label>
                                <select name="position" id="edit_position" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition">
                                    <option value="hero">Hero Slider</option>
                                    <option value="promotional">Promotional Banner</option>
                                    <option value="category">Category Banner</option>
                                    <option value="festival">Festival Banner</option>
                                </select>
                            </div>

                            <div>
                                <x-input name="sort_order" type="number" label="Sort Order *" id="edit_sort_order" required />
                            </div>

                            <div>
                                <x-input name="starts_at" type="date" label="Start Date" id="edit_starts_at" />
                            </div>

                            <div>
                                <x-input name="expires_at" type="date" label="Expiry Date" id="edit_expires_at" />
                            </div>

                            <div class="col-span-2">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" id="edit_is_active"
                                        class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-2">
                                    <span class="ml-2 text-sm text-slate-700">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t pt-4">
                        <button type="button" onclick="closeEditModal()"
                            class="px-3.5 h-9 inline-flex items-center text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-3.5 h-9 inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                            <i data-lucide="save" class="w-3.5 h-3.5"></i> Update Banner
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

    function openEditModal(id, title, subtitle, description, buttonText, buttonLink, image, mobileImage, position, sortOrder, isActive, startsAt, expiresAt) {
        document.getElementById('editForm').action = '{{ route("admin.banners.index") }}/' + id + '/update';
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_subtitle').value = subtitle || '';
        document.getElementById('edit_description').value = description || '';
        document.getElementById('edit_button_text').value = buttonText || '';
        document.getElementById('edit_button_link').value = buttonLink || '';
        document.getElementById('edit_position').value = position;
        document.getElementById('edit_sort_order').value = sortOrder;
        document.getElementById('edit_is_active').checked = isActive;
        document.getElementById('edit_starts_at').value = startsAt || '';
        document.getElementById('edit_expires_at').value = expiresAt || '';

        // Update image previews
        const imagePreview = document.getElementById('editImagePreview');
        const imagePreviewImg = document.getElementById('edit_image_preview');
        if (image) {
            imagePreviewImg.src = '{{ asset("storage") }}/' + image;
            imagePreview.classList.remove('hidden');
        } else {
            imagePreview.classList.add('hidden');
        }

        const mobileImagePreview = document.getElementById('editMobileImagePreview');
        const mobileImagePreviewImg = document.getElementById('edit_mobile_image_preview');
        if (mobileImage) {
            mobileImagePreviewImg.src = '{{ asset("storage") }}/' + mobileImage;
            mobileImagePreview.classList.remove('hidden');
        } else {
            mobileImagePreview.classList.add('hidden');
        }

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

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

    function previewCreateMobileImage(event) {
        const preview = document.getElementById('createMobileImagePreview');
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

    function previewEditMobileImage(event) {
        const img = document.getElementById('edit_mobile_image_preview');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                document.getElementById('editMobileImagePreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    // Close modals on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush

@endsection
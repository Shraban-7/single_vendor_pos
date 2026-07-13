<style>
    /* Custom Multiselect Styles */
    .custom-multiselect {
        position: relative;
    }

    .multiselect-trigger {
        min-height: 46px;
        border-radius: 14px;
        border: 2px solid #e5e7eb;
        padding: 8px 12px;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .multiselect-trigger:hover {
        border-color: #d1d5db;
    }

    .multiselect-trigger.active {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .multiselect-selected {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        flex: 1;
    }

    .multiselect-tag {
        background: linear-gradient(135deg, #3b82f6 0%, #3b82f6 100%);
        color: white;
        border-radius: 10px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        animation: slideIn 0.2s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .multiselect-tag-remove {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transition: background 0.2s;
    }

    .multiselect-tag-remove:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .multiselect-placeholder {
        color: #9ca3af;
        font-size: 14px;
    }

    .multiselect-arrow {
        color: #6b7280;
        transition: transform 0.2s;
    }

    .multiselect-trigger.active .multiselect-arrow {
        transform: rotate(180deg);
    }

    .multiselect-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        animation: dropdownSlide 0.2s ease;
    }

    @keyframes dropdownSlide {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .multiselect-dropdown.active {
        display: block;
    }

    .multiselect-search {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        sticky: top;
        background: white;
    }

    .multiselect-search input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
    }

    .multiselect-search input:focus {
        border-color: #3b82f6;
    }

    .multiselect-options {
        padding: 4px;
    }

    .multiselect-option {
        padding: 10px 12px;
        cursor: pointer;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background 0.15s;
        font-size: 14px;
    }

    .multiselect-option:hover {
        background: #f3f4f6;
    }

    .multiselect-option.selected {
        background: #eff6ff;
        color: #2563eb;
        font-weight: 500;
    }

    .multiselect-checkbox {
        width: 18px;
        height: 18px;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s;
    }

    .multiselect-option.selected .multiselect-checkbox {
        background: linear-gradient(135deg, #3b82f6 0%, #3b82f6 100%);
        border-color: #3b82f6;
    }

    .multiselect-checkbox i {
        color: white;
        font-size: 10px;
        display: none;
    }

    .multiselect-option.selected .multiselect-checkbox i {
        display: block;
    }

    .multiselect-no-results {
        padding: 20px;
        text-align: center;
        color: #9ca3af;
        font-size: 14px;
    }

    /* Scrollbar Styles */
    .multiselect-dropdown::-webkit-scrollbar {
        width: 8px;
    }

    .multiselect-dropdown::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .multiselect-dropdown::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .multiselect-dropdown::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Variant Card Animation */
    .variant-card {
        animation: fadeInUp 0.3s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm" id="variantGenerator">

        <!-- Header -->
        <div class="px-6 py-5">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Product Variant Generator</h2>
                <button type="button" onclick="addVariant()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Add Variant Row
                </button>
            </div>
        </div>

        <div class="p-6 pt-0">
            <!-- Selectors -->
            <div class="grid md:grid-cols-3 gap-5 mb-6">

                <!-- Sizes -->
                <div>
                    <div class="custom-multiselect" id="sizeMultiselect">

                        <div class="multiselect-trigger">
                            <div class="multiselect-selected">
                                <span class="multiselect-placeholder">
                                    Select sizes...
                                </span>
                            </div>

                            <i class="fas fa-chevron-down multiselect-arrow"></i>
                        </div>

                        <div class="multiselect-dropdown">

                            <div class="multiselect-search">
                                <input type="text" placeholder="Search sizes..." class="multiselect-search-input">
                            </div>

                            <div class="multiselect-options">

                                @foreach($sizes as $size)
                                    <div class="multiselect-option" data-id="{{ $size->id }}" data-name="{{ $size->name }}">

                                        <div class="multiselect-checkbox">
                                            <i class="fas fa-check"></i>
                                        </div>

                                        <span>{{ $size->name }}</span>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colors -->
                <div>
                    <div class="custom-multiselect" id="colorMultiselect">

                        <div class="multiselect-trigger">
                            <div class="multiselect-selected">
                                <span class="multiselect-placeholder">
                                    Select colors...
                                </span>
                            </div>

                            <i class="fas fa-chevron-down multiselect-arrow"></i>
                        </div>

                        <div class="multiselect-dropdown">

                            <div class="multiselect-search">
                                <input type="text" placeholder="Search colors..." class="multiselect-search-input">
                            </div>

                            <div class="multiselect-options">

                                @foreach($colors as $color)
                                    <div class="multiselect-option" data-id="{{ $color->id }}"
                                        data-name="{{ $color->name }}">

                                        <div class="multiselect-checkbox">
                                            <i class="fas fa-check"></i>
                                        </div>

                                        <span>{{ $color->name }}</span>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-end mb-1">
                    <button type="button" id="generateVariantsBtn"
                        class="w-full px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-layer-group mr-2"></i>
                        Generate Variants
                    </button>
                </div>
            </div>

            <!-- Variants Container -->
            <div id="variantsContainer"
                class="space-y-4 max-h-[300px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent">

                @if(isset($product) && $product->variants->count() > 0)

                    @foreach($product->variants as $i => $variant)

                        <div class="variant-card p-3 border border-gray-200 rounded-2xl bg-gray-50"
                            data-id="{{ $variant->id }}">

                            <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant->id }}">
                            <input type="hidden" name="variants[{{ $i }}][size_id]" value="{{ $variant->size_id }}">
                            <input type="hidden" name="variants[{{ $i }}][color_id]" value="{{ $variant->color_id }}">
                            <div class="grid grid-cols-12 gap-2 items-end">
                                <!-- Size (3 columns) -->
                                <div class="col-span-3 sm:col-span-3">
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Size</label>

                                    <select name="variants[{{ $i }}][size_id]"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                                        <option value="">Select Size</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}" {{ $variant->size_id == $size->id ? 'selected' : '' }}>
                                                {{ $size->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <!-- Color (3 columns) -->
                                <div class="col-span-3 sm:col-span-3">
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Color</label>

                                    <select name="variants[{{ $i }}][color_id]"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                                        <option value="">Select Color</option>
                                        @foreach($colors as $color)
                                            <option value="{{ $color->id }}" {{ $variant->color_id == $color->id ? 'selected' : '' }}>
                                                {{ $color->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <!-- Price (3 columns) -->
                                <div class="col-span-3 sm:col-span-2">
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Price</label>
                                    <input type="text" name="variants[{{ $i }}][price]" value="{{ $variant->price }}"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                                </div>

                                <!-- SKU (2 columns) -->
                                <div class="col-span-2 sm:col-span-3">
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">SKU</label>
                                    <input type="text" name="variants[{{ $i }}][sku]" value="{{ $variant->sku }}"
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                                </div>

                                <!-- Delete Button (1 column - No Extra Space) -->
                                <div class="col-span-1 flex justify-end">
                                    <button type="button"
                                        class="removeVariantBtn inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 active:bg-red-200 transition-colors duration-150">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>


                        </div>

                    @endforeach

                @else

                    <div id="empty-variant"
                        class="text-sm text-gray-500 text-center py-10 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50">
                        Click "Generate Variants" to create combinations.
                    </div>

                @endif

            </div>

            <!-- deleted tracking -->
            <input type="hidden" name="deleted_variants[]" id="deletedVariants">
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const allSizes = Array.from(
            document.querySelectorAll('#sizeMultiselect .multiselect-option')
        ).map(el => ({
            id: el.dataset.id,
            name: el.dataset.name
        }));

        const allColors = Array.from(
            document.querySelectorAll('#colorMultiselect .multiselect-option')
        ).map(el => ({
            id: el.dataset.id,
            name: el.dataset.name
        }));

        class CustomMultiselect {

            constructor(element) {
                this.element = element;
                this.trigger = element.querySelector('.multiselect-trigger');
                this.dropdown = element.querySelector('.multiselect-dropdown');
                this.selectedContainer = element.querySelector('.multiselect-selected');
                this.optionsContainer = element.querySelector('.multiselect-options');
                this.searchInput = element.querySelector('.multiselect-search-input');

                this.selectedItems = [];
                this.init();
            }

            init() {
                this.bindEvents();
            }

            bindEvents() {

                this.trigger.addEventListener('click', (e) => {
                    e.stopPropagation();

                    document.querySelectorAll('.multiselect-dropdown.active')
                        .forEach(el => el !== this.dropdown && el.classList.remove('active'));

                    document.querySelectorAll('.multiselect-trigger.active')
                        .forEach(el => el !== this.trigger && el.classList.remove('active'));

                    this.dropdown.classList.toggle('active');
                    this.trigger.classList.toggle('active');
                });

                this.searchInput.addEventListener('input', (e) => {
                    const value = e.target.value.toLowerCase();

                    this.optionsContainer.querySelectorAll('.multiselect-option').forEach(option => {
                        const text = option.dataset.name.toLowerCase();
                        option.style.display = text.includes(value) ? 'flex' : 'none';
                    });
                });

                this.optionsContainer.addEventListener('click', (e) => {

                    const option = e.target.closest('.multiselect-option');
                    if (!option) return;

                    const item = {
                        id: option.dataset.id,
                        name: option.dataset.name
                    };

                    if (option.classList.contains('selected')) {
                        option.classList.remove('selected');
                        this.selectedItems = this.selectedItems.filter(i => i.id !== item.id);
                    } else {
                        option.classList.add('selected');
                        this.selectedItems.push(item);
                    }

                    this.render();
                });

                this.selectedContainer.addEventListener('click', (e) => {

                    const removeBtn = e.target.closest('.multiselect-tag-remove');
                    if (!removeBtn) return;

                    const tag = removeBtn.closest('.multiselect-tag');
                    const id = tag.dataset.id;

                    this.selectedItems = this.selectedItems.filter(i => i.id != id);

                    this.optionsContainer
                        .querySelector(`[data-id="${id}"]`)
                        ?.classList.remove('selected');

                    this.render();
                });

                document.addEventListener('click', (e) => {
                    if (!this.element.contains(e.target)) {
                        this.dropdown.classList.remove('active');
                        this.trigger.classList.remove('active');
                    }
                });
            }

            render() {

                if (this.selectedItems.length === 0) {
                    this.selectedContainer.innerHTML = `
                    <span class="multiselect-placeholder">Select options...</span>
                `;
                    return;
                }

                this.selectedContainer.innerHTML = this.selectedItems.map(item => `
                <div class="multiselect-tag" data-id="${item.id}">
                    <span>${item.name}</span>
                    <span class="multiselect-tag-remove">
                        <i class="fas fa-times"></i>
                    </span>
                </div>
            `).join('');
            }

            getSelected() {
                return this.selectedItems;
            }

            clear() {
                this.selectedItems = [];
                this.optionsContainer.querySelectorAll('.multiselect-option')
                    .forEach(o => o.classList.remove('selected'));
                this.render();
            }
        }

        const sizeMS = new CustomMultiselect(document.getElementById('sizeMultiselect'));
        const colorMS = new CustomMultiselect(document.getElementById('colorMultiselect'));

        const container = document.getElementById("variantsContainer");
        const btn = document.getElementById("generateVariantsBtn");

        function variantTemplate({ index, size, color, price, allSizes, allColors }) {

            return `
                <div class="variant-card p-3 border border-gray-200 rounded-2xl bg-gray-50">

                    <input type="hidden" name="variants[${index}][size_id]" value="${size.id}">
                    <input type="hidden" name="variants[${index}][color_id]" value="${color.id}">

                    <div class="grid grid-cols-12 gap-2 items-end">

                        <!-- SIZE DROPDOWN (ALL SIZES) -->
                        <div class="col-span-3">
                            <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Size</label>
                            <select name="variants[${index}][size_id]"
                                class="w-full px-2 py-1.5 text-xs border rounded-lg">
                                <option value="">Select Size</option>
                                ${allSizes.map(s => `
                                    <option value="${s.id}" ${s.id == size.id ? 'selected' : ''}>
                                        ${s.name}
                                    </option>
                                `).join('')}

                            </select>
                        </div>

                        <!-- COLOR DROPDOWN (ALL COLORS) -->
                        <div class="col-span-3">
                            <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Color</label>
                            <select name="variants[${index}][color_id]"
                                class="w-full px-2 py-1.5 text-xs border rounded-lg">
                                <option value="">Select Color</option>
                                ${allColors.map(c => `
                                    <option value="${c.id}" ${c.id == color.id ? 'selected' : ''}>
                                        ${c.name}
                                    </option>
                                `).join('')}

                            </select>
                        </div>

                        <!-- PRICE -->
                        <div class="col-span-3">
                            <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Price</label>
                            <input type="text"
                                name="variants[${index}][price]"
                                value="${productPrice}"
                                class="w-full px-2 py-1.5 text-xs border rounded-lg">
                        </div>

                        <!-- SKU -->
                        <div class="col-span-2">
                            <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">SKU</label>
                            <input type="text"
                                name="variants[${index}][sku]"
                                class="w-full px-2 py-1.5 text-xs border rounded-lg">
                        </div>

                        <!-- DELETE -->
                        <div class="col-span-1 flex justify-end">
                            <button type="button"
                                class="removeVariantBtn w-8 h-8 text-red-600 bg-red-50 rounded-lg hover:bg-red-100">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>

                    </div>
                </div>`;
        }

        btn.addEventListener("click", function () {

            const selectedSizes = sizeMS.getSelected();
            const selectedColors = colorMS.getSelected();

            if (!selectedSizes.length || !selectedColors.length) {
                showToast('error', 'Select sizes and colors');
                return;
            }

            const existing = new Set();

            document.querySelectorAll('.variant-card').forEach(card => {
                const s = card.querySelector('[name*="size_id"]')?.value;
                const c = card.querySelector('[name*="color_id"]')?.value;
                if (s && c) existing.add(`${s}_${c}`);
            });

            let index = document.querySelectorAll('.variant-card').length;
            let html = '';

            const price = document.querySelector('[name="price"]')?.value || 0;

            selectedSizes.forEach(size => {
                selectedColors.forEach(color => {

                    const key = `${size.id}_${color.id}`;

                    if (existing.has(key)) {
                        showToast('error', `Duplicate: ${size.name} - ${color.name}`);
                        return;
                    }

                    existing.add(key);

                    html += variantTemplate({
                        index,
                        size,
                        color,
                        price,
                        allSizes,
                        allColors
                    });

                    index++;
                });
            });

            container.insertAdjacentHTML('beforeend', html);

            showToast('success', 'Variants generated successfully');
        });

        container.addEventListener("click", function (e) {

            const btn = e.target.closest(".removeVariantBtn");

            if (!btn) return;

            btn.closest(".variant-card").remove();

            showToast('success', 'Variant removed');
        });

        function isDuplicate(sizeId, colorId, currentCard = null) {

            let exists = false;

            document.querySelectorAll('.variant-card').forEach(card => {

                if (card === currentCard) return;

                const s = card.querySelector('select[name*="[size_id]"]')?.value
                    || card.querySelector('input[name*="[size_id]"]')?.value;

                const c = card.querySelector('select[name*="[color_id]"]')?.value
                    || card.querySelector('input[name*="[color_id]"]')?.value;

                if (s == sizeId && c == colorId) {
                    exists = true;
                }
            });

            return exists;
        }

        container.addEventListener('change', function (e) {

            const el = e.target;

            if (!el.name.includes('size_id') && !el.name.includes('color_id')) return;

            const card = el.closest('.variant-card');

            const size = card.querySelector('select[name*="[size_id]"]')?.value;
            const color = card.querySelector('select[name*="[color_id]"]')?.value;

            if (isDuplicate(size, color, card)) {

                showToast('error', 'This Size + Color combination already exists');

                el.value = el.dataset.previous || el.value;

                return;
            }

            card.querySelectorAll('select').forEach(sel => {
                sel.dataset.previous = sel.value;
            });
        });
    });

    window.addVariant = function () {

        const container = document.getElementById("variantsContainer");

        const allSizes = Array.from(
            document.querySelectorAll('#sizeMultiselect .multiselect-option')
        ).map(el => ({ id: el.dataset.id, name: el.dataset.name }));

        const allColors = Array.from(
            document.querySelectorAll('#colorMultiselect .multiselect-option')
        ).map(el => ({ id: el.dataset.id, name: el.dataset.name }));

        const price = document.querySelector('[name="price"]')?.value || 0;

        const index = document.querySelectorAll('.variant-card').length;

        const html = `
        <div class="variant-card p-3 border border-gray-200 rounded-2xl bg-gray-50">

            <div class="grid grid-cols-12 gap-2 items-end">

                <!-- SIZE -->
                <div class="col-span-3">
                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Size</label>
                    <select name="variants[${index}][size_id]"
                        class="w-full px-2 py-1.5 text-xs border rounded-lg">

                        <option value="">Select size</option>

                        ${allSizes.map(s => `
                            <option value="${s.id}">${s.name}</option>
                        `).join('')}

                    </select>
                </div>

                <!-- COLOR -->
                <div class="col-span-3">
                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Color</label>
                    <select name="variants[${index}][color_id]"
                        class="w-full px-2 py-1.5 text-xs border rounded-lg">

                        <option value="">Select color</option>

                        ${allColors.map(c => `
                            <option value="${c.id}">${c.name}</option>
                        `).join('')}

                    </select>
                </div>

                <!-- PRICE -->
                <div class="col-span-3">
                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Price</label>
                    <input type="text"
                        name="variants[${index}][price]"
                        value="${price}"
                        class="w-full px-2 py-1.5 text-xs border rounded-lg">
                </div>

                <!-- SKU -->
                <div class="col-span-2">
                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">SKU</label>
                    <input type="text"
                        name="variants[${index}][sku]"
                        class="w-full px-2 py-1.5 text-xs border rounded-lg">
                </div>

                <!-- DELETE -->
                <div class="col-span-1 flex justify-end">
                    <button type="button"
                        class="removeVariantBtn w-8 h-8 text-red-600 bg-red-50 rounded-lg hover:bg-red-100">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>

            </div>
        </div>
    `;

        container.insertAdjacentHTML('beforeend', html);

        showToast('success', 'Variant row added');
    };
</script>
 <div class="bg-white rounded-2xl p-5 md:p-6 border border-gray-100 shadow-sm">
     <div class="flex items-center gap-3 mb-5">
         <div class="w-8 h-8 bg-brand-blue text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
         <h2 class="text-lg font-bold text-brand-black">Payment Method</h2>
     </div>

     <div class="space-y-3">
         {{-- Cash on Delivery --}}
         <label class="payment-option block relative cursor-pointer">
             <input type="radio" name="payment_method" value="cod" class="sr-only peer" checked>
             <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-brand-blue peer-checked:bg-brand-blue/5 transition">
                 <div class="flex items-center gap-4">
                     <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                         <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                     </div>
                     <div class="flex-1">
                         <h4 class="font-semibold text-gray-900">Cash on Delivery</h4>
                         <p class="text-xs text-gray-500">Pay when you receive your order</p>
                     </div>
                     <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-brand-blue peer-checked:bg-brand-blue flex items-center justify-center flex-shrink-0">
                         <i class="fas fa-check text-white text-xs"></i>
                     </div>
                 </div>
             </div>
         </label>

         {{-- bKash Payment --}}
         <label class="payment-option block relative cursor-pointer">
             <input type="radio" name="payment_method" value="bkash" class="sr-only peer">
             <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-brand-blue peer-checked:bg-brand-blue/5 transition">
                 <div class="flex items-center gap-4">
                     <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                         <img src="{{ asset('assets/images/bkash.png') }}" alt="bKash" class="h-8">
                     </div>
                     <div class="flex-1">
                         <h4 class="font-semibold text-gray-900">bKash Payment</h4>
                         <p class="text-xs text-gray-500">Send money to our bKash merchant number</p>
                     </div>
                     <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-brand-blue peer-checked:bg-brand-blue flex items-center justify-center flex-shrink-0">
                         <i class="fas fa-check text-white text-xs"></i>
                     </div>
                 </div>
             </div>
         </label>

         {{-- Nagad Payment --}}
         <label class="payment-option block relative cursor-pointer">
             <input type="radio" name="payment_method" value="nagad" class="sr-only peer">
             <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-brand-blue peer-checked:bg-brand-blue/5 transition">
                 <div class="flex items-center gap-4">
                     <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                         <img src="{{ asset('assets/images/nagad.png') }}" alt="Nagad" class="h-8">
                     </div>
                     <div class="flex-1">
                         <h4 class="font-semibold text-gray-900">Nagad Payment</h4>
                         <p class="text-xs text-gray-500">Send money to our Nagad number</p>
                     </div>
                     <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-brand-blue peer-checked:bg-brand-blue flex items-center justify-center flex-shrink-0">
                         <i class="fas fa-check text-white text-xs"></i>
                     </div>
                 </div>
             </div>
         </label>
     </div>

     {{-- bKash Payment Instructions --}}
     <div id="bkashInstructions" class="hidden mt-5 p-5 bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl border border-pink-200">
         <h4 class="font-bold text-pink-800 mb-3 flex items-center gap-2">
             <i class="fas fa-info-circle"></i>
             bKash Payment Instructions
         </h4>
         <ol class="space-y-3 text-sm text-pink-900">
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-pink-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                 <span>Open your <strong>bKash App</strong> or dial <strong>*247#</strong></span>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-pink-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                 <span>Select <strong>"Send Money"</strong></span>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-pink-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                 <div>
                     <span>Enter our bKash Merchant Number:</span>
                     <div class="mt-2 flex items-center gap-2">
                         <code class="bg-white px-4 py-2 rounded-lg font-bold text-pink-700 text-lg">{{ $bkashNumber }}</code>
                         <button type="button" onclick="copyToClipboard('{{ str_replace('-', '', $bkashNumber) }}', this)" class="p-2 hover:bg-pink-200 rounded-lg transition">
                             <i class="far fa-copy text-pink-600"></i>
                         </button>
                     </div>
                 </div>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-pink-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">4</span>
                 <span>Enter the <strong>Total Amount</strong> shown in order summary</span>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-pink-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">5</span>
                 <span>Add <strong>Reference:</strong> Your Phone Number</span>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-pink-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">6</span>
                 <span>Enter your bKash <strong>PIN</strong> to confirm</span>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-pink-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">7</span>
                 <span>Enter the <strong>Transaction ID (TrxID)</strong> below</span>
             </li>
         </ol>

         {{-- Quick Pay Button --}}
         <a href="https://shop.bkash.com/spinner-fashion{{ str_replace('-', '', $bkashNumber) }}/pay" target="_blank" class="mt-4 w-full bg-pink-600 text-white py-3 rounded-xl font-semibold text-sm hover:bg-pink-700 transition flex items-center justify-center gap-2">
             <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/BKash-bKash-Logo.wine.svg/200px-BKash-bKash-Logo.wine.svg.png" alt="bKash" class="h-5 brightness-0 invert">
             Pay with bKash App
         </a>

         <div class="mt-4">
             <label class="block text-sm font-medium text-pink-800 mb-2">Transaction ID (TrxID) <span class="text-red-500">*</span></label>
             <input type="text" name="bkash_trx_id" placeholder="e.g., 8N72KS92JD" class="w-full h-12 px-4 border border-pink-300 rounded-xl text-sm focus:outline-none focus:border-pink-500 bg-white transition uppercase" style="letter-spacing: 1px;">
             <p class="text-xs text-pink-600 mt-1">Enter the TrxID from your bKash confirmation SMS</p>
         </div>
     </div>

     {{-- Nagad Payment Instructions --}}
     <div id="nagadInstructions" class="hidden mt-5 p-5 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border border-orange-200">
         <h4 class="font-bold text-orange-800 mb-3 flex items-center gap-2">
             <i class="fas fa-info-circle"></i>
             Nagad Payment Instructions
         </h4>
         <ol class="space-y-3 text-sm text-orange-900">
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-orange-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                 <span>Open your <strong>Nagad App</strong> or dial <strong>*167#</strong></span>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-orange-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                 <span>Select <strong>"Send Money"</strong></span>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-orange-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                 <div>
                     <span>Enter our Nagad Number:</span>
                     <div class="mt-2 flex items-center gap-2">
                         <code class="bg-white px-4 py-2 rounded-lg font-bold text-orange-700 text-lg">{{ $nagadNumber }}</code>
                         <button type="button" onclick="copyToClipboard('{{ str_replace('-', '', $nagadNumber) }}', this)" class="p-2 hover:bg-orange-200 rounded-lg transition">
                             <i class="far fa-copy text-orange-600"></i>
                         </button>
                     </div>
                 </div>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-orange-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">4</span>
                 <span>Enter the <strong>Total Amount</strong> shown in order summary</span>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-orange-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">5</span>
                 <span>Add <strong>Reference:</strong> Your Phone Number</span>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-orange-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">6</span>
                 <span>Enter your Nagad <strong>PIN</strong> to confirm</span>
             </li>
             <li class="flex gap-3">
                 <span class="w-6 h-6 bg-orange-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">7</span>
                 <span>Enter the <strong>Transaction ID (TrxID)</strong> below</span>
             </li>
         </ol>

         <div class="mt-4">
             <label class="block text-sm font-medium text-orange-800 mb-2">Transaction ID (TrxID) <span class="text-red-500">*</span></label>
             <input type="text" name="nagad_trx_id" placeholder="e.g., N8K72J92" class="w-full h-12 px-4 border border-orange-300 rounded-xl text-sm focus:outline-none focus:border-orange-500 bg-white transition uppercase" style="letter-spacing: 1px;">
             <p class="text-xs text-orange-600 mt-1">Enter the TrxID from your Nagad confirmation SMS</p>
         </div>
     </div>
 </div>
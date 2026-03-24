@extends('admin.layouts.app')

@section('content')
<div class="h-full flex flex-col p-6">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-900">E-Books</h1>
        <p class="text-xs text-slate-500">Events / <span class="font-semibold text-indigo-600">Edit</span></p>
    </div>

    <form action="{{ route('admin.ebooks.update', $ebook->id) }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-[24px] border border-slate-100 shadow-sm flex-1 p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 h-full">
            <div class="space-y-5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">E-Book Title *</label>
                    <input type="text" name="title" value="{{ old('title', $ebook->title) }}" required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Hospital</label>
                    <input type="text" name="hospital" value="{{ old('hospital', $ebook->hospital) }}"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Short Description</label>
                    <textarea name="short_description" rows="3"
                              class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">{{ old('short_description', $ebook->short_description) }}</textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">{{ old('description', $ebook->description) }}</textarea>
                </div>

                <div class="pt-4">
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Pricing Type *</label>
                    <div x-data="{ type: '{{ old('pricing_type', $ebook->pricing_type) }}' }" class="space-y-4">
                        <div class="flex items-center gap-6">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="radio" name="pricing_type" value="free" x-model="type"
                                       class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500/30">
                                <span>Free</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="radio" name="pricing_type" value="paid" x-model="type"
                                       class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500/30">
                                <span>Paid</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-2">Price *</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price', $ebook->price) }}"
                                   :disabled="type === 'free'"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 disabled:bg-slate-50 disabled:text-slate-400">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $ebook->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500/30">
                        Display Active
                    </label>
                </div>

                <div class="pt-4 flex items-center gap-3">
                    <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl bg-[#0f172a] hover:bg-indigo-600 text-white text-xs font-bold shadow-md">
                        Update E-Book
                    </button>
                    <a href="{{ route('admin.ebooks.index') }}"
                       class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">
                        Cancel
                    </a>
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <div class="border border-slate-200 rounded-2xl px-6 py-5">
                    <p class="text-sm font-semibold text-slate-800 mb-4">Images</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-6 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                            <span class="text-xs font-medium text-slate-700">Cover Image</span>
                            <input type="file" name="cover_image" class="hidden" accept="image/*">
                        </label>
                        <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-6 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                            <span class="text-xs font-medium text-slate-700">Banner Image</span>
                            <input type="file" name="banner_image" class="hidden" accept="image/*">
                        </label>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-2xl px-6 py-5">
                    <p class="text-sm font-semibold text-slate-800 mb-4">Material Upload</p>
                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-300 rounded-xl py-6 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors">
                        <span class="text-xs font-medium text-slate-700">Word or PDF or Zip</span>
                        <input type="file" name="material" class="hidden" accept=".pdf,.doc,.docx,.zip">
                    </label>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

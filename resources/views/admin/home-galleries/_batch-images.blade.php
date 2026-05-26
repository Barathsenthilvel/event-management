@if(isset($batchItems) && $batchItems->count() > 0)
    <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-3">
        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-2">
            Images in this upload ({{ $batchItems->count() }})
        </p>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            @foreach ($batchItems as $batchItem)
                <div class="relative rounded-lg border overflow-hidden bg-white {{ (int) $batchItem->id === (int) $item->id ? 'ring-2 ring-indigo-500 border-indigo-400' : 'border-slate-200' }}">
                    <img src="{{ asset('storage/' . ltrim((string) $batchItem->image_path, '/')) }}" alt="" class="h-24 w-full object-cover">
                    <div class="absolute inset-x-0 bottom-0 flex flex-wrap gap-1 p-1.5 bg-gradient-to-t from-black/70 to-transparent">
                        @if($batchItem->is_category_primary)
                            <span class="rounded bg-amber-400 px-1.5 py-0.5 text-[9px] font-bold text-amber-950">Main</span>
                        @endif
                        @if((int) $batchItem->id === (int) $item->id)
                            <span class="rounded bg-indigo-500 px-1.5 py-0.5 text-[9px] font-bold text-white">Editing</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

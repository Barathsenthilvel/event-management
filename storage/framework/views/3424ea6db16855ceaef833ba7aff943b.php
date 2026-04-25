
<?php
    $modeLabels = [
        'whatsapp' => 'WhatsApp',
        'teams' => 'Microsoft Teams',
        'others' => 'Online (other)',
        'direct' => 'In person',
        'phone_call' => 'Phone call',
    ];
?>
<section
    id="dash-upcoming-meetings"
    class="scroll-mt-28 overflow-hidden rounded-2xl border border-[#351c42]/10 bg-white shadow-md"
    aria-labelledby="dash-meetings-title"
>
    <div class="h-1.5 bg-gradient-to-r from-[#965995] via-[#7a4680] to-[#351c42]"></div>
    <div class="p-5 sm:p-7">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#965995]/12 text-[#965995]" aria-hidden="true">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                <div>
                    <h2 id="dash-meetings-title" class="text-lg font-extrabold tracking-tight text-[#351c42] sm:text-xl">Your meeting schedule</h2>
                    <p class="mt-1 max-w-2xl text-sm leading-relaxed text-[#351c42]/65">
                        Sessions from the office: <span class="font-semibold text-[#351c42]/80">open meetings</span> for all members, plus any you were <span class="font-semibold text-[#351c42]/80">personally invited</span> to. Soonest dates appear first.
                    </p>
                </div>
            </div>
        </div>

        <ul class="mt-6 space-y-4" role="list">
            <?php $__currentLoopData = $upcomingMeetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mtg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $sch = $mtg->schedules->first();
                    $modeLabel = $modeLabels[$mtg->meeting_mode] ?? ucfirst(str_replace('_', ' ', (string) $mtg->meeting_mode));
                    $isInvited = $mtg->invites->isNotEmpty();
                    $desc = trim(strip_tags((string) ($mtg->description ?? '')));
                    $coverUrl = $mtg->cover_image_path ? asset('storage/' . ltrim($mtg->cover_image_path, '/')) : null;
                ?>
                <li class="group rounded-2xl border border-[#351c42]/10 bg-gradient-to-br from-[#faf9fc] to-white shadow-sm transition hover:border-[#965995]/35 hover:shadow-md">
                    <div class="flex flex-col gap-4 p-4 sm:flex-row sm:gap-5 sm:p-5">
                        
                        <div class="flex shrink-0 gap-3 sm:flex-col sm:items-center sm:text-center">
                            <?php if($sch && $sch->meeting_date): ?>
                                <div class="flex h-[4.5rem] w-[4.5rem] shrink-0 flex-col items-center justify-center rounded-2xl border border-[#965995]/25 bg-white shadow-inner sm:h-[5rem] sm:w-[5rem]">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#965995]"><?php echo e($sch->meeting_date->format('M')); ?></span>
                                    <span class="text-2xl font-black tabular-nums leading-none text-[#351c42]"><?php echo e($sch->meeting_date->format('j')); ?></span>
                                    <span class="text-[10px] font-semibold text-[#351c42]/55"><?php echo e($sch->meeting_date->format('D')); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="flex h-[4.5rem] w-[4.5rem] shrink-0 items-center justify-center rounded-2xl border border-dashed border-[#351c42]/20 bg-white/80 text-[10px] font-bold text-[#351c42]/45">TBA</div>
                            <?php endif; ?>
                        </div>

                        
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <?php if($mtg->status === 'live'): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black uppercase tracking-wide text-emerald-800">
                                        <span class="relative flex h-1.5 w-1.5">
                                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        </span>
                                        Live now
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex rounded-full bg-[#f6f3e9] px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-[#351c42]/70">Upcoming</span>
                                <?php endif; ?>
                                <?php if($isInvited): ?>
                                    <span class="inline-flex rounded-full bg-[#965995]/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-[#965995]">Invited</span>
                                <?php else: ?>
                                    <span class="inline-flex rounded-full bg-[#351c42]/8 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-[#351c42]/55">Open</span>
                                <?php endif; ?>
                                <span class="inline-flex items-center rounded-full bg-white px-2 py-0.5 text-[10px] font-semibold text-[#351c42]/70 ring-1 ring-[#351c42]/10"><?php echo e($modeLabel); ?></span>
                            </div>

                            <h3 class="mt-2 text-base font-extrabold leading-snug text-[#351c42] sm:text-lg"><?php echo e($mtg->title); ?></h3>

                            <?php if($sch): ?>
                                <p class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-sm font-semibold text-[#351c42]/80">
                                    <time datetime="<?php echo e($sch->meeting_date?->format('Y-m-d')); ?>"><?php echo e($sch->meeting_date?->format('l, j F Y')); ?></time>
                                    <span class="text-[#351c42]/35" aria-hidden="true">·</span>
                                    <span>
                                        <?php echo e(\Illuminate\Support\Carbon::parse($sch->from_time)->format('g:i A')); ?>

                                        –
                                        <?php echo e(\Illuminate\Support\Carbon::parse($sch->to_time)->format('g:i A')); ?>

                                    </span>
                                </p>
                            <?php endif; ?>

                            <?php if($desc !== ''): ?>
                                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-[#351c42]/65"><?php echo e(\Illuminate\Support\Str::limit($desc, 200)); ?></p>
                            <?php endif; ?>
                        </div>

                        
                        <div class="flex shrink-0 flex-col items-stretch gap-3 sm:w-44 sm:items-end">
                            <?php if($coverUrl): ?>
                                <div class="hidden overflow-hidden rounded-xl border border-[#351c42]/10 sm:block sm:h-20 sm:w-full">
                                    <img src="<?php echo e($coverUrl); ?>" alt="" class="h-full w-full object-cover" loading="lazy" width="176" height="80" />
                                </div>
                            <?php endif; ?>
                            <?php if(!empty($mtg->meeting_link)): ?>
                                <a
                                    href="<?php echo e($mtg->meeting_link); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#351c42] px-4 py-3 text-center text-sm font-extrabold text-[#fddc6a] shadow-sm transition hover:brightness-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#965995] focus-visible:ring-offset-2 sm:w-auto sm:min-w-[9rem]"
                                >
                                    <svg class="h-4 w-4 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Join meeting
                                </a>
                            <?php else: ?>
                                <span class="inline-flex w-full items-center justify-center rounded-xl border border-dashed border-[#351c42]/25 bg-white/80 px-4 py-3 text-center text-xs font-bold text-[#351c42]/55 sm:w-auto">Link not published yet</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</section>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\partials\dashboard-meetings.blade.php ENDPATH**/ ?>
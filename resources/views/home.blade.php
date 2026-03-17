<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management | Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #F8FAFC; }
    </style>
</head>
<body class="min-h-screen">
    <header class="bg-white border-b border-slate-100">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </div>
                <span class="text-slate-900 font-bold text-lg tracking-tight">EVENT<span class="text-slate-500 font-light italic">.MANAGEMENT</span></span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('member.login') }}"
                    class="px-5 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white rounded-2xl font-bold shadow-sm transition-all">
                    Member Login
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-14">
        <div class="bg-white border border-slate-100 rounded-3xl p-10 shadow-sm">
            <h1 class="text-4xl font-bold text-slate-900 tracking-tight">Welcome</h1>
            <p class="mt-3 text-slate-600 text-lg">Please click <span class="font-bold">Member Login</span> to continue.</p>
        </div>
    </main>
</body>
</html>


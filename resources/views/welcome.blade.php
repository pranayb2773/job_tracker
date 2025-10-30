<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ config('app.name') }} - Track Your Job Applications with AI</title>

        <link rel="icon" href="/favicon.ico" sizes="any" />
        <link rel="icon" href="/favicon.svg" type="image/svg+xml" />
        <link rel="apple-touch-icon" href="/apple-touch-icon.png" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased min-h-screen bg-white dark:bg-zinc-950">
        <!-- Navigation -->
        <div class="sticky top-0 z-50 bg-white/95 dark:bg-zinc-950/95 backdrop-blur-sm border-b border-zinc-200 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <a href="/" class="flex items-center gap-3 group">
                        <div class="size-9 rounded-lg bg-gradient-to-br from-zinc-900 to-zinc-700 dark:from-zinc-100 dark:to-zinc-300 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <flux:icon.briefcase variant="solid" class="size-5 text-white dark:text-zinc-900" />
                        </div>
                        <span class="text-lg font-bold text-zinc-900 dark:text-white">Job Tracker</span>
                    </a>

                    @if (Route::has('login'))
                        <div class="flex items-center gap-3">
                            @auth
                                <flux:button href="{{ url('/dashboard') }}" variant="ghost" size="sm">
                                    Dashboard
                                </flux:button>
                            @else
                                <flux:button href="{{ route('login') }}" variant="ghost" size="sm">
                                    Log in
                                </flux:button>

                                @if (Route::has('register'))
                                    <flux:button href="{{ route('register') }}" variant="primary" size="sm">
                                        Get Started Free
                                    </flux:button>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-b from-white to-zinc-50 dark:from-zinc-950 dark:to-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
            <!-- Background decoration -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-zinc-100 dark:bg-zinc-900 rounded-full blur-3xl opacity-50"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-zinc-100 dark:bg-zinc-900 rounded-full blur-3xl opacity-50"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Column: Content -->
                    <div class="space-y-8">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-sm">
                            <flux:icon.sparkles variant="solid" class="size-4 text-zinc-600 dark:text-zinc-400" />
                            <span class="text-zinc-700 dark:text-zinc-300">AI-Powered Job Search Assistant</span>
                        </div>

                        <div class="space-y-4">
                            <flux:heading size="xl" class="!text-5xl sm:!text-6xl !leading-tight">
                                Track Every <span class="text-transparent bg-clip-text bg-gradient-to-r from-zinc-900 to-zinc-600 dark:from-zinc-100 dark:to-zinc-400">Job Application</span> in One Place
                            </flux:heading>
                            <flux:subheading class="text-lg text-zinc-600 dark:text-zinc-400">
                                From application to offer — stay organized, get AI-powered CV insights, and land your dream job faster with our intelligent tracking system.
                            </flux:subheading>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            @if (Route::has('register'))
                                <flux:button href="{{ route('register') }}" variant="primary" icon-trailing="arrow-right">
                                    Start Tracking for Free
                                </flux:button>
                            @endif
                            <flux:button href="#features" variant="ghost">
                                Learn More
                            </flux:button>
                        </div>
                    </div>

                    <!-- Right Column: Visual -->
                    <div class="relative lg:block hidden">
                        <div class="relative">
                            <!-- Mock application cards -->
                            <div class="space-y-3">
                                <flux:card class="transform hover:-translate-y-1 transition-transform">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">AC</span>
                                            </div>
                                            <div>
                                                <flux:heading size="sm">Senior Developer</flux:heading>
                                                <flux:text variant="subtle" class="text-xs">Acme Corp</flux:text>
                                            </div>
                                        </div>
                                        <flux:badge color="lime" size="sm">Interview</flux:badge>
                                    </div>
                                </flux:card>

                                <flux:card class="transform hover:-translate-y-1 transition-transform">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">TI</span>
                                            </div>
                                            <div>
                                                <flux:heading size="sm">Full Stack Engineer</flux:heading>
                                                <flux:text variant="subtle" class="text-xs">Tech Innovators</flux:text>
                                            </div>
                                        </div>
                                        <flux:badge color="zinc" size="sm">Applied</flux:badge>
                                    </div>
                                </flux:card>

                                <flux:card class="transform hover:-translate-y-1 transition-transform">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">DS</span>
                                            </div>
                                            <div>
                                                <flux:heading size="sm">Lead Developer</flux:heading>
                                                <flux:text variant="subtle" class="text-xs">Digital Solutions</flux:text>
                                            </div>
                                        </div>
                                        <flux:badge color="green" size="sm">Offer</flux:badge>
                                    </div>
                                </flux:card>
                            </div>

                            <!-- Floating ATS score card -->
                            <div class="absolute -right-8 -bottom-8">
                                <flux:card class="shadow-lg">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <flux:icon.sparkles variant="solid" class="size-4 text-yellow-500" />
                                            <flux:text class="text-xs font-medium">ATS Score</flux:text>
                                        </div>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-2xl font-bold text-zinc-900 dark:text-white">87</span>
                                            <span class="text-sm text-zinc-500">/100</span>
                                        </div>
                                        <div class="w-24 h-1.5 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-green-500 rounded-full" style="width: 87%"></div>
                                        </div>
                                    </div>
                                </flux:card>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="border-b bg-zinc-50 dark:bg-zinc-900/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="text-center space-y-2">
                        <div class="text-3xl sm:text-4xl font-bold text-zinc-900 dark:text-white">24</div>
                        <flux:subheading>Active Applications</flux:subheading>
                    </div>
                    <div class="text-center space-y-2">
                        <div class="text-3xl sm:text-4xl font-bold text-zinc-900 dark:text-white">5</div>
                        <flux:subheading>Upcoming Interviews</flux:subheading>
                    </div>
                    <div class="text-center space-y-2">
                        <div class="text-3xl sm:text-4xl font-bold text-zinc-900 dark:text-white">87%</div>
                        <flux:subheading>Average ATS Score</flux:subheading>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="text-center mb-12 space-y-3">
                <flux:heading size="xl">Everything You Need to Land Your Dream Job</flux:heading>
                <flux:subheading>Powerful features to streamline your job search</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <flux:card class="hover:shadow-md transition-shadow">
                    <div class="space-y-3">
                        <div class="size-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                            <flux:icon.clipboard-document-list variant="outline" class="size-5" />
                        </div>
                        <div class="space-y-2">
                            <flux:heading size="lg">Application Tracking</flux:heading>
                            <flux:text>
                                Track applications through the complete hiring lifecycle with status workflows, priority levels, and tagging.
                            </flux:text>
                        </div>
                    </div>
                </flux:card>

                <!-- Feature 2 -->
                <flux:card class="hover:shadow-md transition-shadow">
                    <div class="space-y-3">
                        <div class="size-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                            <flux:icon.sparkles variant="outline" class="size-5" />
                        </div>
                        <div class="space-y-2">
                            <flux:heading size="lg">AI-Powered CV Analysis</flux:heading>
                            <flux:text>
                                Get comprehensive ATS scoring across 8 dimensions with actionable recommendations powered by AI.
                            </flux:text>
                        </div>
                    </div>
                </flux:card>

                <!-- Feature 3 -->
                <flux:card class="hover:shadow-md transition-shadow">
                    <div class="space-y-3">
                        <div class="size-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                            <flux:icon.document-text variant="outline" class="size-5" />
                        </div>
                        <div class="space-y-2">
                            <flux:heading size="lg">Document Management</flux:heading>
                            <flux:text>
                                Store and organize resumes, cover letters, and portfolios with secure file storage and metadata tracking.
                            </flux:text>
                        </div>
                    </div>
                </flux:card>

                <!-- Feature 4 -->
                <flux:card class="hover:shadow-md transition-shadow">
                    <div class="space-y-3">
                        <div class="size-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                            <flux:icon.arrows-right-left variant="outline" class="size-5" />
                        </div>
                        <div class="space-y-2">
                            <flux:heading size="lg">Status Workflow</flux:heading>
                            <flux:text>
                                Visualize your pipeline from Applied → Screening → Interview → Offer → Accepted/Rejected.
                            </flux:text>
                        </div>
                    </div>
                </flux:card>

                <!-- Feature 5 -->
                <flux:card class="hover:shadow-md transition-shadow">
                    <div class="space-y-3">
                        <div class="size-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                            <flux:icon.magnifying-glass variant="outline" class="size-5" />
                        </div>
                        <div class="space-y-2">
                            <flux:heading size="lg">Advanced Filtering</flux:heading>
                            <flux:text>
                                Quickly find applications with powerful search and filtering by status, priority, dates, and tags.
                            </flux:text>
                        </div>
                    </div>
                </flux:card>

                <!-- Feature 6 -->
                <flux:card class="hover:shadow-md transition-shadow">
                    <div class="space-y-3">
                        <div class="size-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                            <flux:icon.moon variant="outline" class="size-5" />
                        </div>
                        <div class="space-y-2">
                            <flux:heading size="lg">Beautiful UI</flux:heading>
                            <flux:text>
                                Modern interface with dark mode support, real-time updates, and responsive design for all devices.
                            </flux:text>
                        </div>
                    </div>
                </flux:card>
            </div>
        </div>

        <!-- Workflow Section -->
        <div class="border-y bg-white dark:bg-zinc-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
                <div class="text-center mb-16 space-y-3">
                    <flux:heading size="xl">Your Job Search Pipeline</flux:heading>
                    <flux:subheading>Track every step from application to offer</flux:subheading>
                </div>

                <!-- Desktop workflow -->
                <div class="hidden md:block">
                    <div class="relative max-w-6xl mx-auto">
                        <!-- Connecting line -->
                        <div class="absolute top-12 left-0 right-0 h-0.5 bg-zinc-200 dark:bg-zinc-800"></div>

                        <div class="relative grid grid-cols-5 gap-4">
                            <!-- Step 1 -->
                            <div class="flex flex-col items-center space-y-4">
                                <div class="relative">
                                    <div class="size-24 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                                        <flux:icon.paper-airplane variant="solid" class="size-10 text-white" />
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 size-8 rounded-full bg-white dark:bg-zinc-950 border-2 border-blue-500 flex items-center justify-center">
                                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400">1</span>
                                    </div>
                                </div>
                                <div class="text-center space-y-1">
                                    <flux:heading size="sm">Applied</flux:heading>
                                    <flux:text variant="subtle" class="text-xs">Submit your application</flux:text>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="flex flex-col items-center space-y-4">
                                <div class="relative">
                                    <div class="size-24 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                                        <flux:icon.document-magnifying-glass variant="solid" class="size-10 text-white" />
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 size-8 rounded-full bg-white dark:bg-zinc-950 border-2 border-purple-500 flex items-center justify-center">
                                        <span class="text-sm font-bold text-purple-600 dark:text-purple-400">2</span>
                                    </div>
                                </div>
                                <div class="text-center space-y-1">
                                    <flux:heading size="sm">Screening</flux:heading>
                                    <flux:text variant="subtle" class="text-xs">Initial review phase</flux:text>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="flex flex-col items-center space-y-4">
                                <div class="relative">
                                    <div class="size-24 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                                        <flux:icon.chat-bubble-left-right variant="solid" class="size-10 text-white" />
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 size-8 rounded-full bg-white dark:bg-zinc-950 border-2 border-amber-500 flex items-center justify-center">
                                        <span class="text-sm font-bold text-amber-600 dark:text-amber-400">3</span>
                                    </div>
                                </div>
                                <div class="text-center space-y-1">
                                    <flux:heading size="sm">Interview</flux:heading>
                                    <flux:text variant="subtle" class="text-xs">Schedule & prepare</flux:text>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="flex flex-col items-center space-y-4">
                                <div class="relative">
                                    <div class="size-24 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                                        <flux:icon.document-check variant="solid" class="size-10 text-white" />
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 size-8 rounded-full bg-white dark:bg-zinc-950 border-2 border-green-500 flex items-center justify-center">
                                        <span class="text-sm font-bold text-green-600 dark:text-green-400">4</span>
                                    </div>
                                </div>
                                <div class="text-center space-y-1">
                                    <flux:heading size="sm">Offer</flux:heading>
                                    <flux:text variant="subtle" class="text-xs">Receive offers</flux:text>
                                </div>
                            </div>

                            <!-- Step 5 -->
                            <div class="flex flex-col items-center space-y-4">
                                <div class="relative">
                                    <div class="size-24 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                                        <flux:icon.check-badge variant="solid" class="size-10 text-white" />
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 size-8 rounded-full bg-white dark:bg-zinc-950 border-2 border-teal-500 flex items-center justify-center">
                                        <span class="text-sm font-bold text-teal-600 dark:text-teal-400">5</span>
                                    </div>
                                </div>
                                <div class="text-center space-y-1">
                                    <flux:heading size="sm">Accepted</flux:heading>
                                    <flux:text variant="subtle" class="text-xs">Land your dream job!</flux:text>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile workflow -->
                <div class="md:hidden space-y-4">
                    <flux:card>
                        <div class="flex items-center gap-4">
                            <div class="size-16 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                                <flux:icon.paper-airplane variant="solid" class="size-7 text-white" />
                            </div>
                            <div class="flex-1">
                                <flux:heading size="sm">Applied</flux:heading>
                                <flux:text variant="subtle" class="text-xs">Submit your application</flux:text>
                            </div>
                            <flux:badge color="blue" size="sm">1</flux:badge>
                        </div>
                    </flux:card>

                    <flux:card>
                        <div class="flex items-center gap-4">
                            <div class="size-16 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                <flux:icon.document-magnifying-glass variant="solid" class="size-7 text-white" />
                            </div>
                            <div class="flex-1">
                                <flux:heading size="sm">Screening</flux:heading>
                                <flux:text variant="subtle" class="text-xs">Initial review phase</flux:text>
                            </div>
                            <flux:badge color="purple" size="sm">2</flux:badge>
                        </div>
                    </flux:card>

                    <flux:card>
                        <div class="flex items-center gap-4">
                            <div class="size-16 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center flex-shrink-0">
                                <flux:icon.chat-bubble-left-right variant="solid" class="size-7 text-white" />
                            </div>
                            <div class="flex-1">
                                <flux:heading size="sm">Interview</flux:heading>
                                <flux:text variant="subtle" class="text-xs">Schedule & prepare</flux:text>
                            </div>
                            <flux:badge color="amber" size="sm">3</flux:badge>
                        </div>
                    </flux:card>

                    <flux:card>
                        <div class="flex items-center gap-4">
                            <div class="size-16 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center flex-shrink-0">
                                <flux:icon.document-check variant="solid" class="size-7 text-white" />
                            </div>
                            <div class="flex-1">
                                <flux:heading size="sm">Offer</flux:heading>
                                <flux:text variant="subtle" class="text-xs">Receive offers</flux:text>
                            </div>
                            <flux:badge color="green" size="sm">4</flux:badge>
                        </div>
                    </flux:card>

                    <flux:card>
                        <div class="flex items-center gap-4">
                            <div class="size-16 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center flex-shrink-0">
                                <flux:icon.check-badge variant="solid" class="size-7 text-white" />
                            </div>
                            <div class="flex-1">
                                <flux:heading size="sm">Accepted</flux:heading>
                                <flux:text variant="subtle" class="text-xs">Land your dream job!</flux:text>
                            </div>
                            <flux:badge color="teal" size="sm">5</flux:badge>
                        </div>
                    </flux:card>
                </div>
            </div>
        </div>

        <!-- AI Analysis Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <div class="space-y-3">
                        <flux:heading size="xl">AI-Powered CV Analysis</flux:heading>
                        <flux:subheading>
                            Get instant feedback on your resume with our comprehensive ATS scoring system. Powered by state-of-the-art AI from Anthropic, OpenAI, and Google.
                        </flux:subheading>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <flux:icon.check-circle variant="solid" class="size-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" />
                            <div class="space-y-1">
                                <flux:heading size="sm">8 Scoring Dimensions</flux:heading>
                                <flux:text variant="subtle">Comprehensive analysis across all important criteria</flux:text>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <flux:icon.check-circle variant="solid" class="size-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" />
                            <div class="space-y-1">
                                <flux:heading size="sm">Actionable Recommendations</flux:heading>
                                <flux:text variant="subtle">Top 3 improvements to boost your ATS score</flux:text>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <flux:icon.check-circle variant="solid" class="size-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" />
                            <div class="space-y-1">
                                <flux:heading size="sm">Database Caching</flux:heading>
                                <flux:text variant="subtle">99% cost reduction through smart caching</flux:text>
                            </div>
                        </div>
                    </div>
                </div>

                <flux:card>
                    <div class="space-y-5">
                        <flux:heading size="lg">Sample ATS Score</flux:heading>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <flux:text>Overall Score</flux:text>
                                    <flux:heading size="lg">87/100</flux:heading>
                                </div>
                                <div class="w-full h-2.5 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-600 rounded-full transition-all" style="width: 87%"></div>
                                </div>
                            </div>

                            <flux:separator />

                            <div class="space-y-2.5">
                                <div class="flex justify-between text-sm">
                                    <flux:text>Metadata & Contact</flux:text>
                                    <flux:text class="font-medium">95/100</flux:text>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <flux:text>Presentation & Formatting</flux:text>
                                    <flux:text class="font-medium">90/100</flux:text>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <flux:text>Content Quality</flux:text>
                                    <flux:text class="font-medium">85/100</flux:text>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <flux:text>Keyword Relevance</flux:text>
                                    <flux:text class="font-medium">80/100</flux:text>
                                </div>
                            </div>
                        </div>
                    </div>
                </flux:card>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="border-t bg-zinc-50 dark:bg-zinc-900/50">
            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 py-16">
                <div class="space-y-6">
                    <div class="space-y-3">
                        <flux:heading size="xl">Ready to Land Your Dream Job?</flux:heading>
                        <flux:subheading>
                            Join thousands of job seekers who are staying organized and getting hired faster with Job Tracker.
                        </flux:subheading>
                    </div>
                    @if (Route::has('register'))
                        <flux:button href="{{ route('register') }}" variant="primary">
                            Get Started for Free
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t bg-zinc-50 dark:bg-zinc-900/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12">
                    <!-- Brand Section -->
                    <div class="md:col-span-5 space-y-4">
                        <a href="/" class="flex items-center gap-3 group w-fit">
                            <div class="size-9 rounded-lg bg-gradient-to-br from-zinc-900 to-zinc-700 dark:from-zinc-100 dark:to-zinc-300 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <flux:icon.briefcase variant="solid" class="size-5 text-white dark:text-zinc-900" />
                            </div>
                            <span class="text-lg font-bold text-zinc-900 dark:text-white">Job Tracker</span>
                        </a>
                        <flux:text variant="subtle" class="max-w-sm">
                            Track your job applications with AI-powered insights and stay organized throughout your job search journey.
                        </flux:text>
                    </div>

                    <!-- Features Links -->
                    <div class="md:col-span-3 space-y-4">
                        <flux:heading size="sm" class="text-zinc-900 dark:text-white">Product</flux:heading>
                        <div class="space-y-3">
                            <div><a href="#features" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Features</a></div>
                            <div><a href="#workflow" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Workflow</a></div>
                            <div><a href="#ai-analysis" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">AI Analysis</a></div>
                        </div>
                    </div>

                    <!-- Get Started -->
                    <div class="md:col-span-4 space-y-4">
                        <flux:heading size="sm" class="text-zinc-900 dark:text-white">Get Started</flux:heading>
                        <div class="space-y-4">
                            <flux:text variant="subtle" class="text-sm">
                                Start tracking your job applications today.
                            </flux:text>
                            @if (Route::has('register'))
                                <flux:button href="{{ route('register') }}" variant="primary" size="sm" class="w-full sm:w-auto">
                                    Create Free Account
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>

                <flux:separator class="my-8" />

                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <flux:text variant="subtle" class="text-sm">
                        &copy; {{ date('Y') }} Job Tracker. All rights reserved.
                    </flux:text>
                    <flux:text variant="subtle" class="text-sm">
                        Built with Laravel
                    </flux:text>
                </div>
            </div>
        </div>
    </body>
</html>

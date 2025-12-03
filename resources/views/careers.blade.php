@extends('layouts.app')

@section('title', 'Careers - Pradytecai')
@section('description', 'Join the Pradytecai team. We are looking for talented individuals to help us build the future of enterprise software.')

@section('content')
    <!-- Hero Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-sky-50 via-white to-indigo-50">
        <div class="w-full mx-auto text-center hero-animate">
            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">Join Our Team</h1>
            <p class="text-xl text-gray-600">
                Build the future of enterprise software with us
            </p>
        </div>
    </section>

    <!-- Why Work With Us -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="w-full mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Work at Pradytecai?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    We offer a dynamic work environment where innovation and growth are encouraged
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white border-2 border-gray-200 rounded-xl p-8">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Innovation First</h3>
                    <p class="text-gray-600">
                        Work with cutting-edge technologies and contribute to innovative solutions that 
                        make a real impact on businesses.
                    </p>
                </div>

                <div class="bg-white border-2 border-gray-200 rounded-xl p-8">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Continuous Learning</h3>
                    <p class="text-gray-600">
                        Access to training programs, conferences, and resources to help you grow your 
                        skills and advance your career.
                    </p>
                </div>

                <div class="bg-white border-2 border-gray-200 rounded-xl p-8">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Great Team</h3>
                    <p class="text-gray-600">
                        Collaborate with talented, passionate professionals in a supportive and inclusive 
                        work environment.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Open Positions -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50">
        <div class="w-full mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Open Positions</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    We're always looking for talented individuals to join our team
                </p>
            </div>

            <div class="space-y-6">
                <!-- Position 1 -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-8 hover:shadow-lg transition-shadow">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="mb-4 md:mb-0">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Senior Software Developer</h3>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">Full-time</span>
                                <span class="px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm">Remote</span>
                                <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-sm">Laravel</span>
                            </div>
                            <p class="text-gray-600">
                                We're looking for an experienced Laravel developer to join our team. You'll work on 
                                building and maintaining our enterprise software platforms.
                            </p>
                        </div>
                        <a href="/contact?position=Senior+Software+Developer" class="md:ml-6 bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition whitespace-nowrap">
                            Apply Now
                        </a>
                    </div>
                </div>

                <!-- Position 2 -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-8 hover:shadow-lg transition-shadow">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="mb-4 md:mb-0">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">DevOps Engineer</h3>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">Full-time</span>
                                <span class="px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm">Hybrid</span>
                                <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-sm">AWS</span>
                            </div>
                            <p class="text-gray-600">
                                Join our infrastructure team to help scale our cloud platforms and ensure 
                                maximum uptime and performance.
                            </p>
                        </div>
                        <a href="/contact?position=DevOps+Engineer" class="md:ml-6 bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition whitespace-nowrap">
                            Apply Now
                        </a>
                    </div>
                </div>

                <!-- Position 3 -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-8 hover:shadow-lg transition-shadow">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="mb-4 md:mb-0">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Product Manager</h3>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">Full-time</span>
                                <span class="px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm">On-site</span>
                            </div>
                            <p class="text-gray-600">
                                Lead product development initiatives and work closely with engineering teams 
                                to deliver exceptional software solutions.
                            </p>
                        </div>
                        <a href="/contact?position=Product+Manager" class="md:ml-6 bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition whitespace-nowrap">
                            Apply Now
                        </a>
                    </div>
                </div>

                <!-- No Open Positions Message -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-8 text-center">
                    <p class="text-gray-600 mb-4">
                        Don't see a position that matches your skills? We're always interested in hearing 
                        from talented individuals.
                    </p>
                    <a href="/contact" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                        Send Us Your Resume
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="w-full mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Benefits & Perks</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Competitive Salary</h3>
                    <p class="text-sm text-gray-600">Market-competitive compensation packages</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Flexible Hours</h3>
                    <p class="text-sm text-gray-600">Work-life balance with flexible scheduling</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Learning Budget</h3>
                    <p class="text-sm text-gray-600">Annual budget for courses and conferences</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Team Events</h3>
                    <p class="text-sm text-gray-600">Regular team building and social events</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-sky-50 via-white to-indigo-50">
        <div class="w-full mx-auto text-center hero-animate delay-md">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Ready to Join Us?</h2>
            <p class="text-xl text-gray-600 mb-8">
                Get in touch to learn more about opportunities at Pradytecai.
            </p>
            <a href="/contact" class="inline-block bg-indigo-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition">
                Contact Us
            </a>
        </div>
    </section>
@endsection

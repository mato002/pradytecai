@extends('layouts.app')

@section('title', 'Terms, Privacy & Cookie Policy - Pradytecai')
@section('description', 'Read our terms of service, privacy policy, and cookie policy.')

@section('content')
    <!-- Hero Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-sky-50 via-white to-indigo-50">
        <div class="w-full mx-auto text-center hero-animate">
            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">Legal Policies</h1>
            <p class="text-xl text-gray-600">
                Terms of Service, Privacy Policy, and Cookie Policy
            </p>
        </div>
    </section>

    <!-- Policies Content -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="w-full mx-auto">
            <!-- Navigation Tabs -->
            <div class="border-b border-slate-200 mb-8">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <button onclick="showSection('terms')" class="policy-tab active border-b-2 border-indigo-600 py-4 px-1 text-sm font-medium text-indigo-600">
                        Terms of Service
                    </button>
                    <button onclick="showSection('privacy')" class="policy-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-slate-300">
                        Privacy Policy
                    </button>
                    <button onclick="showSection('cookies')" class="policy-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-slate-300">
                        Cookie Policy
                    </button>
                </nav>
            </div>

            <!-- Terms of Service -->
            <div id="terms-section" class="policy-section">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Terms of Service</h2>
                <div class="prose max-w-none space-y-6 text-gray-600">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">1. Acceptance of Terms</h3>
                        <p>
                            By accessing and using Pradytecai's services, you accept and agree to be bound by the terms 
                            and provision of this agreement. If you do not agree to abide by the above, please do not 
                            use this service.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">2. Use License</h3>
                        <p>
                            Permission is granted to temporarily use Pradytecai's services for personal, non-commercial 
                            transitory viewing only. This is the grant of a license, not a transfer of title, and under 
                            this license you may not:
                        </p>
                        <ul class="list-disc pl-6 mt-2 space-y-1">
                            <li>Modify or copy the materials</li>
                            <li>Use the materials for any commercial purpose or for any public display</li>
                            <li>Attempt to reverse engineer any software contained on Pradytecai's services</li>
                            <li>Remove any copyright or other proprietary notations from the materials</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">3. Service Availability</h3>
                        <p>
                            We strive to maintain high availability of our services. However, we do not guarantee 
                            uninterrupted access and may perform maintenance that temporarily affects service availability.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">4. User Responsibilities</h3>
                        <p>
                            Users are responsible for maintaining the confidentiality of their account credentials and 
                            for all activities that occur under their account. Users must notify us immediately of any 
                            unauthorized use of their account.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">5. Limitation of Liability</h3>
                        <p>
                            Pradytecai shall not be liable for any indirect, incidental, special, consequential, or 
                            punitive damages resulting from your use of or inability to use the service.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">6. Changes to Terms</h3>
                        <p>
                            Pradytecai reserves the right to revise these terms of service at any time. By continuing to 
                            use the service after changes are made, you agree to be bound by the revised terms.
                        </p>
                    </div>

                    <p class="text-sm text-gray-500 mt-8">
                        Last updated: January 2025
                    </p>
                </div>
            </div>

            <!-- Privacy Policy -->
            <div id="privacy-section" class="policy-section hidden">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Privacy Policy</h2>
                <div class="prose max-w-none space-y-6 text-gray-600">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">1. Information We Collect</h3>
                        <p>
                            We collect information that you provide directly to us, including:
                        </p>
                        <ul class="list-disc pl-6 mt-2 space-y-1">
                            <li>Name and contact information (email, phone number)</li>
                            <li>Company information</li>
                            <li>Payment information (processed securely through third-party providers)</li>
                            <li>Account credentials and preferences</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">2. How We Use Your Information</h3>
                        <p>We use the information we collect to:</p>
                        <ul class="list-disc pl-6 mt-2 space-y-1">
                            <li>Provide, maintain, and improve our services</li>
                            <li>Process transactions and send related information</li>
                            <li>Send technical notices, updates, and support messages</li>
                            <li>Respond to your comments and questions</li>
                            <li>Monitor and analyze trends and usage</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">3. Information Sharing</h3>
                        <p>
                            We do not sell, trade, or rent your personal information to third parties. We may share 
                            your information only in the following circumstances:
                        </p>
                        <ul class="list-disc pl-6 mt-2 space-y-1">
                            <li>With your consent</li>
                            <li>To comply with legal obligations</li>
                            <li>To protect our rights and safety</li>
                            <li>With service providers who assist in our operations</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">4. Data Security</h3>
                        <p>
                            We implement appropriate technical and organizational measures to protect your personal 
                            information against unauthorized access, alteration, disclosure, or destruction.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">5. Your Rights</h3>
                        <p>You have the right to:</p>
                        <ul class="list-disc pl-6 mt-2 space-y-1">
                            <li>Access your personal information</li>
                            <li>Correct inaccurate data</li>
                            <li>Request deletion of your data</li>
                            <li>Object to processing of your data</li>
                            <li>Data portability</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">6. Contact Us</h3>
                        <p>
                            If you have questions about this Privacy Policy, please contact us at 
                            <a href="mailto:mathiasodhis@gmail.com" class="text-indigo-600 hover:underline">mathiasodhis@gmail.com</a>
                        </p>
                    </div>

                    <p class="text-sm text-gray-500 mt-8">
                        Last updated: January 2025
                    </p>
                </div>
            </div>

            <!-- Cookie Policy -->
            <div id="cookies-section" class="policy-section hidden">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Cookie Policy</h2>
                <div class="prose max-w-none space-y-6 text-gray-600">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">1. What Are Cookies?</h3>
                        <p>
                            Cookies are small text files that are placed on your device when you visit our website. 
                            They help us provide you with a better experience by remembering your preferences and 
                            understanding how you use our services.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">2. Types of Cookies We Use</h3>
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-semibold text-gray-900">Essential Cookies</h4>
                                <p>
                                    These cookies are necessary for the website to function properly. They enable core 
                                    functionality such as security, network management, and accessibility.
                                </p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Analytics Cookies</h4>
                                <p>
                                    These cookies help us understand how visitors interact with our website by collecting 
                                    and reporting information anonymously.
                                </p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Functional Cookies</h4>
                                <p>
                                    These cookies allow the website to remember choices you make and provide enhanced, 
                                    personalized features.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">3. Managing Cookies</h3>
                        <p>
                            You can control and manage cookies in various ways. Most web browsers allow you to refuse 
                            or accept cookies. However, disabling cookies may affect the functionality of our website.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">4. Third-Party Cookies</h3>
                        <p>
                            We may use third-party services that set cookies on your device. These services help us 
                            analyze website usage and improve our services. We do not control these third-party cookies.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">5. Updates to This Policy</h3>
                        <p>
                            We may update this Cookie Policy from time to time. We will notify you of any changes by 
                            posting the new policy on this page.
                        </p>
                    </div>

                    <p class="text-sm text-gray-500 mt-8">
                        Last updated: January 2025
                    </p>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        function showSection(section) {
            // Hide all sections
            document.querySelectorAll('.policy-section').forEach(sec => {
                sec.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.policy-tab').forEach(tab => {
                tab.classList.remove('active', 'border-blue-600', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected section
            document.getElementById(section + '-section').classList.remove('hidden');
            
            // Add active class to clicked tab
            event.target.classList.add('active', 'border-blue-600', 'text-blue-600');
            event.target.classList.remove('border-transparent', 'text-gray-500');
        }
    </script>
    @endpush
@endsection

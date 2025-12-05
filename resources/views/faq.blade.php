@extends('layouts.app')

@section('title', 'Frequently Asked Questions - Pradytecai')
@section('description', 'Find answers to common questions about Pradytecai products, services, pricing, and support.')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/85 via-purple-900/80 to-pink-900/85"></div>
        {{-- Subtle pattern overlay --}}
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0VjIySDI0djEySDEyVjM0aDEyVjQ2aDEyVjM0em0wLTEyVjEwSDI0djEySDEyVjIySDBWMTBoMTJWMEgyNHYxMHoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-20"></div>

        <div class="relative z-10 w-full mx-auto text-center hero-animate">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">Frequently Asked Questions</h1>
            <p class="text-xl text-slate-200">
                Find answers to common questions about our products and services
            </p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="section-bg-overlay section-bg-overlay-light"></div>
        <div class="section-content w-full mx-auto max-w-4xl">
            <div class="space-y-6">
                <!-- General Questions -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">General Questions</h2>
                    <div class="space-y-4">
                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">What is Pradytecai?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>Pradytecai is a leading provider of enterprise software solutions specializing in communication platforms and financial management systems. We offer products like BulkSMS CRM for multi-channel messaging and Prady Mfi for microfinance management.</p>
                            </div>
                        </div>

                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">What products does Pradytecai offer?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>We offer several products including:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1">
                                    <li><strong>BulkSMS CRM:</strong> Multi-channel messaging platform for SMS, WhatsApp, and Email</li>
                                    <li><strong>Prady Mfi:</strong> Comprehensive microfinance management system</li>
                                    <li><strong>Enterprise Solutions:</strong> Custom software development and cloud infrastructure</li>
                                </ul>
                            </div>
                        </div>

                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">How can I get started?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>Getting started is easy! You can:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1">
                                    <li>Visit our <a href="/products" class="text-indigo-600 hover:underline">Products page</a> to explore our solutions</li>
                                    <li>Try our <a href="https://demo.pradytecai.com" target="_blank" class="text-indigo-600 hover:underline">live demo</a> for Prady Mfi</li>
                                    <li><a href="/contact" class="text-indigo-600 hover:underline">Contact us</a> to discuss your specific needs</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Questions -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Product Questions</h2>
                    <div class="space-y-4">
                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">What is BulkSMS CRM?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>BulkSMS CRM is a comprehensive multi-channel messaging platform that enables businesses to send SMS, WhatsApp, and Email campaigns at scale. It includes contact management, campaign builder, real-time analytics, and API integration capabilities.</p>
                            </div>
                        </div>

                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">What is Prady Mfi?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>Prady Mfi is a comprehensive microfinance management system designed to streamline loan processing, client management, and financial reporting. It's perfect for microfinance institutions looking to digitize and automate their operations.</p>
                            </div>
                        </div>

                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">Do you offer custom development?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>Yes! We offer custom enterprise software solutions tailored to your specific business needs. Our team can build bespoke applications, integrate with existing systems, and provide cloud infrastructure solutions. <a href="/contact" class="text-indigo-600 hover:underline">Contact us</a> to discuss your requirements.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Support -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Pricing & Support</h2>
                    <div class="space-y-4">
                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">What are your pricing plans?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>Our pricing varies based on your specific needs and usage. We offer flexible pricing plans for all our products. Please <a href="/contact" class="text-indigo-600 hover:underline">contact our sales team</a> to discuss pricing options that best fit your business requirements.</p>
                            </div>
                        </div>

                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">What kind of support do you provide?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>We provide comprehensive support including:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1">
                                    <li>24/7 monitoring and technical support</li>
                                    <li>Regular system updates and security patches</li>
                                    <li>User training and documentation</li>
                                    <li>API documentation and integration support</li>
                                    <li>Dedicated account management for enterprise clients</li>
                                </ul>
                            </div>
                        </div>

                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">How secure are your platforms?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>Security is our top priority. Our platforms feature:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1">
                                    <li>Enterprise-grade encryption</li>
                                    <li>Regular security audits and compliance checks</li>
                                    <li>99.9% uptime guarantee</li>
                                    <li>Data backup and disaster recovery</li>
                                    <li>Compliance with industry standards and regulations</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Questions -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Technical Questions</h2>
                    <div class="space-y-4">
                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">Do you provide API access?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>Yes, we provide comprehensive REST API access for all our platforms. Our APIs are well-documented and allow you to integrate our services with your existing systems. API documentation and credentials are available through your account dashboard.</p>
                            </div>
                        </div>

                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">Can I integrate with my existing systems?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>Absolutely! We offer comprehensive integration services to connect our platforms with your existing systems. Our team can help with API integration, third-party connectors, and data synchronization. <a href="/contact" class="text-indigo-600 hover:underline">Contact us</a> to discuss your integration needs.</p>
                            </div>
                        </div>

                        <div class="faq-item bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <button class="faq-question w-full text-left flex items-center justify-between focus:outline-none" onclick="toggleFaq(this)">
                                <span class="text-lg font-semibold text-gray-900">What is your uptime guarantee?</span>
                                <svg class="faq-icon w-5 h-5 text-indigo-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed">
                                <p>We guarantee 99.9% uptime across all our core platforms. Our infrastructure is designed for maximum reliability with redundant systems, automated failover, and 24/7 monitoring to ensure your services remain available when you need them.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Still have questions? -->
            <div class="mt-16 text-center bg-gradient-to-br from-indigo-50 to-sky-100 rounded-xl p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Still have questions?</h3>
                <p class="text-gray-600 mb-6">Can't find the answer you're looking for? Our team is here to help.</p>
                <a href="/contact" class="inline-block bg-indigo-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition">
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    <script>
        function toggleFaq(button) {
            const item = button.closest('.faq-item');
            const answer = item.querySelector('.faq-answer');
            const icon = item.querySelector('.faq-icon');
            
            const isOpen = !answer.classList.contains('hidden');
            
            // Close all other FAQs
            document.querySelectorAll('.faq-item').forEach(faq => {
                if (faq !== item) {
                    faq.querySelector('.faq-answer').classList.add('hidden');
                    faq.querySelector('.faq-icon').classList.remove('rotate-180');
                }
            });
            
            // Toggle current FAQ
            if (isOpen) {
                answer.classList.add('hidden');
                icon.classList.remove('rotate-180');
            } else {
                answer.classList.remove('hidden');
                icon.classList.add('rotate-180');
            }
        }
    </script>
@endsection


<?php

return [
    'company' => 'Prady Technologies Ltd',
    'tagline' => 'Doing It Differently',
    'contact' => [
        // Hardcoded for now — later replace with admin General Settings values.
        'phone' => '+254 722 295 194',
        'phone_href' => 'tel:+254722295194',
        'email' => 'marketing@pradytecai.com',
        'email_href' => 'mailto:marketing@pradytecai.com',
        'emails' => [
            [
                'label' => 'Marketing',
                'email' => 'marketing@pradytecai.com',
            ],
        ],
        'location' => 'Nairobi, Kenya',
        'hours' => 'Mon – Fri: 8:00 AM – 6:00 PM EAT',
    ],
    'solutions' => [
        [
            'name' => 'Microfinance System',
            'short' => 'Lending & repayment management for microfinance institutions.',
            'icon' => 'piggy',
            'href' => '/products#prady-microfinance',
        ],
        [
            'name' => 'SACCO System',
            'short' => 'Member savings, loans & cooperative management platform.',
            'icon' => 'users',
            'href' => '/products#sacco-system',
        ],
        [
            'name' => 'Custom Software',
            'short' => 'Bespoke software solutions tailored to your business needs.',
            'icon' => 'cog',
            'href' => '/services',
        ],
        [
            'name' => 'HR System',
            'short' => 'Employee management, payroll & HR operations streamlined.',
            'icon' => 'hr',
            'href' => '/products',
        ],
    ],
    'trust' => [
        [
            'label' => 'Secure by Design',
            'detail' => 'Security-first platforms',
            'icon' => 'shield',
        ],
        [
            'label' => 'Reliable Platforms',
            'detail' => 'Built for continuous operations',
            'icon' => 'clock',
        ],
        [
            'label' => 'Built for African Businesses',
            'detail' => 'Practical systems for local markets',
            'icon' => 'building',
        ],
    ],
    'products' => [
        [
            'slug' => 'prady-microfinance',
            'name' => 'Prady Microfinance',
            'short' => 'Complete operating platform for MFIs and lenders covering loans, collections, payments and institutional operations.',
            'description' => 'A complete operating platform for microfinance institutions and lenders, covering customers, loans, collections, payments, M-Pesa, accounting, reporting, HR, controls and institutional operations.',
            'market' => 'MFIs, credit companies and lenders',
            'icon' => 'finance',
        ],
        [
            'slug' => 'rafiki-loan',
            'name' => 'Rafiki Loan',
            'short' => 'Digital lending marketplace connecting borrowers with institutions and capital providers.',
            'description' => 'A digital lending marketplace connecting people seeking financing with institutions or people offering financing. Supports discovery, matching and the journey between borrower and finance provider.',
            'market' => 'Borrowers, lenders and capital providers',
            'icon' => 'handshake',
        ],
        [
            'slug' => 'gps-hosting',
            'name' => 'GPS Hosting & Tracking Platform',
            'short' => 'GPS tracking and hosting for vehicle trackers, fleets and GPS resellers.',
            'description' => 'A GPS tracking and hosting platform for compatible vehicle trackers, fleets and GPS resellers, including support for Prady-branded tracking devices.',
            'market' => 'Vehicle owners, fleet operators, logistics businesses and GPS resellers',
            'icon' => 'location',
        ],
        [
            'slug' => 'property-management',
            'name' => 'Property Management System',
            'short' => 'Manage properties, tenants, leases, rent, arrears and maintenance in one place.',
            'description' => 'A platform for managing properties, units, tenants, leases, rent, payments, arrears, maintenance and property performance.',
            'market' => 'Landlords, property managers and real-estate businesses',
            'icon' => 'building',
        ],
        [
            'slug' => 'spareme',
            'name' => 'SpareMe',
            'short' => 'Vehicle-first automotive commerce matching spare parts to exact vehicles.',
            'description' => 'A vehicle-first automotive commerce and intelligence ecosystem. Vehicle owners identify their exact vehicle while dealers manage inventory and compatible spare parts can be matched to vehicles.',
            'market' => 'Vehicle owners, spare-parts dealers, mechanics, suppliers and garages',
            'icon' => 'car',
        ],
        [
            'slug' => 'live-commerce',
            'name' => 'Live Commerce / Social Selling Platform',
            'short' => 'Social-commerce platform for live shows, audiences and lasting product shelves.',
            'description' => 'A social-commerce platform where sellers and creators can announce live shows, attract audiences and showcase products during and after live broadcasts.',
            'market' => 'Social sellers, creators, SMEs and online merchants',
            'icon' => 'live',
        ],
        [
            'slug' => 'sacco-system',
            'name' => 'SACCO System',
            'short' => 'Digital operating system for SACCOs covering membership, savings and loans.',
            'description' => 'A digital operating system for SACCO and member-based financial institutions covering membership, savings, contributions, loans, accounts, governance and institutional administration.',
            'market' => 'SACCOs and member-based financial institutions',
            'icon' => 'users',
        ],
        [
            'slug' => 'chama-system',
            'name' => 'Chama System',
            'short' => 'Simple digital platform for chamas, investment clubs and welfare groups.',
            'description' => 'A simplified digital platform for investment groups and chamas covering members, contributions, welfare, loans, projects/investments, commitments, documents and transparent group accounts.',
            'market' => 'Chamas, investment clubs and welfare groups',
            'icon' => 'group',
        ],
        [
            'slug' => 'mtalii-travel-wallet',
            'name' => 'Mtalii Travel Wallet',
            'short' => 'Tourism-focused wallet for payments, FX and local merchant acceptance.',
            'description' => 'A tourism-focused wallet and travel platform designed around tourists and local merchants, including payments, FX, merchant acceptance and tourism-oriented financial services.',
            'market' => 'Tourists, tour operators, guides and local merchants',
            'icon' => 'wallet',
        ],
    ],
];

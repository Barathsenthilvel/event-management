<?php

return [
    'title' => 'GNAT Association — Give Hope, Support Communities',

    /** Shown as event organizer on the public site and member portal (not the admin login name). */
    'event_organizer_name' => env('EVENT_ORGANIZER_NAME', 'GNAT Association'),

    'logo' => [
        'src' => 'images/logo.png',
        'alt' => 'GNAT Association',
    ],

    /*
    | Inbox for public contact form submissions (defaults to contact.email).
    | Set CONTACT_FORM_TO in .env to override, e.g. secretary@example.org
    */
    'contact_form_to' => env('CONTACT_FORM_TO'),

    /*
    | Shown on Privacy Policy, Terms & Conditions, and related public pages.
    | Override with LEGAL_EFFECTIVE_DATE in .env (e.g. "January 1, 2026").
    */
    'legal' => [
        'effective_date' => env('LEGAL_EFFECTIVE_DATE', 'May 11, 2026'),
    ],

    'contact' => [
        'email' => 'gnat9715@gmail.com',
        'address' => 'No. 36/76, Thiruveethi Amman Kovil 2nd Street, Aminjikarai, Chennai- 600029',
        'phones' => [
            ['tel' => '+919585144633', 'label' => '+91 95851 44633'],
            ['tel' => '+919585144833', 'label' => '+91 95851 44833'],
            ['tel' => '+919585144933', 'label' => '+91 95851 44933'],
            ['tel' => '+919488357270', 'label' => '+91 94883 57270'],
        ],
        'maps_query' => 'No+36%2F76+Thiruveethi+Amman+Kovil+2nd+Street+Aminjikarai+Chennai+600029',
    ],

    'nav' => [
        ['label' => 'Home', 'href' => '/home'],
        ['label' => 'Activity', 'href' => '/activity'],
        ['label' => 'About Us', 'href' => '/about'],
        ['label' => 'Events', 'href' => '/campaign'],
        ['label' => 'Blog', 'href' => '/stories'],
        ['label' => 'Gallery', 'href' => '/photos'],
        ['label' => 'Jobs', 'href' => '/careers'],
        ['label' => 'Contact Us', 'href' => '/contact'],
    ],

    'hero' => [
        'badge' => 'Graduate Nurses Association of Tamil Nadu - GNAT',
        'headline_line1' => 'Giving Hope. Building Community. Creating Impact.',
        'headline_line2' => 'Grow Stronger Communities',
        'description_html' => '<strong class="text-white block mb-2">Built on 3 Core Principles:</strong><span class="block text-white/90">For the Nurses.</span><span class="block text-white/90">By the Nurses.</span><span class="block text-white/90">Of the Nurses.</span>',
        // 'description_html' => '<strong class="text-white">GNAT Association</strong> brings people together to create meaningful impact across communities.',
        'registered_count' => 2603,
        'registered_label' => 'Members Registered',
        'avatar_image' => 'images/testimonials-images/thumb-10.2.webp',
    ],

    'volunteer_cards' => [
        [
            'title' => 'Community Impact',
            'bullets' => [
                'Support real people with hands-on help.',
                'Build stronger neighborhoods through teamwork.',
                'Turn ideas into measurable outcomes.',
            ],
        ],
        [
            'title' => 'Skill Growth',
            'bullets' => [
                'Learn through workshops and mentorship.',
                'Get hands-on experience with real tasks.',
                'Improve communication and leadership.',
            ],
        ],
        [
            'title' => 'Sustainable Work',
            'bullets' => [
                'Help create eco-friendly initiatives.',
                'Use resources wisely and efficiently.',
                'Track impact and improve over time.',
            ],
        ],
        [
            'title' => 'Team Support',
            'bullets' => [
                'Work with friendly, motivated teammates.',
                'Get clear guidance and quick feedback.',
                'Share ideas and improve together.',
            ],
        ],
        [
            'title' => 'Leadership Path',
            'bullets' => [
                'Take responsibility in real projects.',
                'Build confidence through guided challenges.',
                'Grow from volunteer to mentor.',
            ],
        ],
    ],

    'banners' => [
        [
            'href' => '/campaign',
            'src' => 'gnat-images/b1.png',
            'alt' => 'Community education and outreach',
            'eyebrow' => 'EVENTS',
            'title' => 'Together we go further',
            'text' => 'Thank-you gatherings and impact stories from Chennai neighborhoods.',
        ],
        [
            'href' => '/give',
            'src' => 'gnat-images/b2.png',
            'alt' => 'Programs and fundraising support',
            'eyebrow' => 'PROGRAMS',
            'title' => 'Support that reaches every family',
            'text' => 'Transparent campaigns and accountable giving across education, health, and community.',
        ],
        [
            'href' => '/photos',
            'src' => 'gnat-images/1000989137.png',
            'alt' => 'Volunteers at community events',
            'eyebrow' => 'COMMUNITY',
            'title' => 'Moments that inspire action',
            'text' => 'Field photos from outreach, learning spaces, and celebrations with people we serve.',
        ],
    ],

    'testimonials_intro' => [
        'eyebrow' => 'What Members Say',
        'title' => 'Member Stories & Real Support',
        'text' => 'Hear directly from Graduate Nurses across Tamil Nadu about how GNAT Association stepped forward to support their career, rights, and professional dignity.',
    ],

    'testimonials' => [
        [
            'name' => 'Rajesh',
            'role' => 'GNAT Member',
            'text' => 'When I joined a private hospital, I submitted my original certificates. Later, they refused to return them unless I paid. A friend suggested GNAT. One of the Executive Leaders immediately intervened, spoke to management, and helped me get my original certificates back. I sincerely thank GNAT for the timely support!',
            'stars' => 5,
        ],
        [
            'name' => 'Siva Sankari',
            'role' => 'GNAT Member',
            'text' => 'I was finding it very hard to get a good job with the right salary. I reached out to a GNAT leader. They supported me and helped me find a good job with good payment. Now I am working well and also continuing my education. Thank you GNAT for real support!',
            'stars' => 5,
        ],
        [
            'name' => 'Rajesh',
            'role' => 'GNAT Member',
            'text' => 'I strongly recommend every nurse to become a member of GNAT. It is a place where you get real support and guidance, even beyond your job. Thank you, GNAT.',
            'stars' => 5,
        ],
        [
            'name' => 'Siva Sankari',
            'role' => 'GNAT Member',
            'text' => 'I reached out to a GNAT leader. They supported me and helped me find a good job with good payment. Now I am working well & continuing education. Strongly recommend joining GNAT!',
            'stars' => 5,
        ],
    ],

    'testimonial_profile_image' => 'images/testimonials-images/thumb-10.2.webp',

    'testimonial_stack_cards' => [
        [
            'image' => 'images/events/event-1-1.jpg',
            'quote' => 'When I joined a private hospital, management refused to return my original certificates. A friend suggested GNAT. An Executive Leader immediately intervened and helped get my certificates back. Real support when I needed it most!',
            'name' => 'Rajesh',
            'role' => 'GNAT Member',
            'rating' => '5.0',
            'play' => false,
        ],
        [
            'image' => 'images/events/event-1-2.jpg',
            'quote' => 'I was finding it hard to get a good job with the right salary. GNAT leaders supported me and helped me find a good job with good payment. Now I am working well and continuing my education. Thank you GNAT!',
            'name' => 'Siva Sankari',
            'role' => 'GNAT Member',
            'rating' => '5.0',
            'play' => false,
        ],
    ],

    'about' => [
        'main_image' => 'gnat-images/1000989135.png',
        'accent_image' => 'gnat-images/1000989136.png',
        'third_image' => 'gnat-images/1000989137.png',
        'eyebrow' => 'ABOUT GNAT ASSOCIATION',
        'title_lines' => ['Empowering Nurses,', 'Across Tamil Nadu'],
        'title_highlight' => 'Transforming Care',
        'intro_lines' => [
            'Graduate Nurses Association of Tamil Nadu believes that qualified nurses are the backbone of the healthcare delivery system; they function as the orbit of the caring process by shouldering major care needs in hospitals and communities.',
            'Built on F.A.C.T.—Fidelity, Assertiveness, Commitment, and Teamwork—to safeguard nurses\' moral, legal, and professional rights.',
        ],
        'impact_line' => 'Established April 30, 2015 under Society Act 27 of 1975.',
        'principles_heading' => 'GNAT Core Principles',
        'principles' => [
            ['label' => 'For the Nurses', 'meaning' => 'Welfare, Rights & Support'],
            ['label' => 'By the Nurses', 'meaning' => 'Ground-Reality Leadership'],
            ['label' => 'Of the Nurses', 'meaning' => 'Our Collective Family'],
        ],
    ],

    'events' => [
        [
            'summary_date' => '03 Sep',
            'summary_title' => "Let's Education For Children Get Good Life",
            'time' => '10:00 AM - 2:00 PM',
            'image' => 'images/events/event-1-1.jpg',
            'badge_day' => '03',
            'badge_month' => 'SEP',
            'badge_rounded' => 'rounded-full',
            'description' => 'Dicta Sunt Explicabo. Nemo Enim Ipsam Voluptatem Quia Voluptas Sit Aspernaturaut Odit Aut Fugit, Sed Quia Consequuntur.',
            'organizer' => 'Ashton Porter',
            'venue' => '350 5th Avenue, New York, NY 10118',
            'seat_mode' => 'limited',
            'seat_filled' => 12,
            'seat_limit' => 50,
        ],
        [
            'summary_date' => '10 Sep',
            'summary_title' => 'Start A Fundraiser For Yourself In World',
            'time' => '10:00 AM - 2:00 PM',
            'image' => 'images/events/event-1-2.jpg',
            'badge_day' => '10',
            'badge_month' => 'SEP',
            'badge_rounded' => 'rounded-xl',
            'description' => 'Practical steps to start your fundraiser, keep momentum, and communicate impact clearly to your community.',
            'organizer' => 'Ashton Porter',
            'venue' => 'Virtual Session (Online)',
            'seat_mode' => 'unlimited',
        ],
        [
            'summary_date' => '24 Sep',
            'summary_title' => 'Volunteer Training: Communication & Impact',
            'time' => '10:00 AM - 2:00 PM',
            'image' => 'images/events/event-1-3.jpg',
            'badge_day' => '24',
            'badge_month' => 'SEP',
            'badge_rounded' => 'rounded-full',
            'badge_bg' => 'bg-[#ffffff]',
            'description' => 'Learn communication techniques and how to turn volunteer actions into measurable impact.',
            'organizer' => 'Ashton Porter',
            'venue' => '350 5th Avenue, New York, NY 10118',
            'seat_mode' => 'limited',
            'seat_filled' => 8,
            'seat_limit' => 30,
        ],
    ],

    'donate' => [
        'intro_title' => 'Featured campaigns',
        'intro_kicker' => 'Association',
        'intro_text' => 'Explore active GNAT Association programs—swipe or use the arrows. Every project is designed for transparent, accountable community support.',
        'goal' => 500,
        'default_amount' => 100,
        'bar_percent_demo' => 52,
        'amounts' => [10, 25, 50, 100, 250],
    ],

    'activities' => [
        'badge' => 'Activity',
        'title' => 'Programs & pathways',
        'subtitle' => 'Community education and outreach',
        'intro' => 'GNAT Association brings together fundraising, education, health support, and career pathways so members and partners can serve Chennai communities with clear impact and trusted delivery.',
        'items' => [
            [
                'num' => '01',
                'slug' => 'quick-fundraising',
                'label' => 'Quick Fundraising',
                'description' => 'Quick Fundraising lets supporters start or join GNAT campaigns online so gifts reach schools, health programs, and community outreach in Aminjikarai without delay.',
                'route' => 'donations.index',
                'button' => 'View campaigns',
            ],
            [
                'num' => '02',
                'slug' => 'school-education',
                'label' => 'School & Education Support',
                'description' => 'School & Education Support provides books, safe classrooms, and meals so children in our communities can stay in school and learn with dignity.',
                'route' => 'contact',
                'button' => 'Get in touch',
            ],
            [
                'num' => '03',
                'slug' => 'medical-treatment',
                'label' => 'Medical Treatment',
                'description' => 'Medical Treatment connects donors to health camps and treatment help for families who cannot afford care, so support reaches verified needs when it matters most.',
                'route' => 'donations.index',
                'button' => 'Support health programs',
            ],
            [
                'num' => '04',
                'slug' => 'careers',
                'label' => 'Careers & opportunities',
                'description' => 'Careers & opportunities help members build skills, volunteer, and grow through meaningful association work that serves Chennai communities.',
                'route' => 'home.careers',
                'button' => 'Explore careers',
            ],
            [
                'num' => '05',
                'slug' => 'job-openings',
                'label' => 'Job openings & applications',
                'description' => 'Job openings & applications share current roles from GNAT and partners so members can apply online and find work that fits their skills.',
                'route' => 'home.careers',
                'button' => 'View job openings',
            ],
            [
                'num' => '06',
                'slug' => 'fundraising-goals',
                'label' => 'Fundraising Goals',
                'description' => 'Fundraising Goals showcase featured donation drives on the site so every gift supports transparent programs from classrooms to health outreach across our communities.',
                'route' => 'home.give',
                'button' => 'Donate now',
            ],
        ],
    ],

    /** @deprecated Use activities.items — kept for backward compatibility */
    'services' => [
        ['num' => '01', 'label' => 'Quick Fundraising', 'slug' => 'quick-fundraising', 'route' => 'donations.index'],
        ['num' => '02', 'label' => 'School & Education Support', 'slug' => 'school-education', 'route' => 'contact'],
        ['num' => '03', 'label' => 'Medical Treatment', 'slug' => 'medical-treatment', 'route' => 'donations.index'],
        ['num' => '04', 'label' => 'Careers & opportunities', 'slug' => 'careers', 'route' => 'home.careers'],
        ['num' => '05', 'label' => 'Job openings & applications', 'slug' => 'job-openings', 'route' => 'home.careers'],
        ['num' => '06', 'label' => 'Fundraising Goals', 'slug' => 'fundraising-goals', 'route' => 'home.give'],
    ],

    'blog' => [
        'posts' => [
            ['image' => 'images/events/event-1-2.jpg', 'tag' => 'Forest', 'day' => '09', 'month' => 'Jan', 'year' => '2026', 'title' => 'Waste Management', 'excerpt' => 'Energy consulting involves providing of advice and guidance on energy', 'comments' => 367],
            ['image' => 'images/events/event-1-3.jpg', 'tag' => 'Recycle', 'day' => '24', 'month' => 'Feb', 'year' => '2026', 'title' => 'Waste Management', 'excerpt' => 'Energy consulting involves providing of advice and guidance on energy', 'comments' => 367],
            ['image' => 'images/events/event-1-1.jpg', 'tag' => 'Forest', 'day' => '15', 'month' => 'Mar', 'year' => '2026', 'title' => 'Waste Management', 'excerpt' => 'Energy consulting involves providing of advice and guidance on energy', 'comments' => 367],
            ['image' => 'images/events/event-1-2.jpg', 'tag' => 'Forest', 'day' => '29', 'month' => 'Apr', 'year' => '2026', 'title' => 'Waste Management', 'excerpt' => 'Energy consulting involves providing of advice and guidance on energy', 'comments' => 367],
        ],
    ],

    'gallery' => [
        'filters' => [
            ['key' => 'all', 'label' => 'All'],
            ['key' => 'programs', 'label' => 'Programs'],
            ['key' => 'events', 'label' => 'Events'],
            ['key' => 'community', 'label' => 'Community'],
        ],
        'items' => [
            ['cat' => 'programs', 'layout' => 'hero', 'image' => 'images/events/event-1-1.jpg', 'alt' => 'School and education support program', 'eyebrow' => 'Programs', 'title' => 'Learning & school support', 'text' => 'Books, meals, and safe classrooms for children in Chennai.'],
            ['cat' => 'events', 'layout' => 'wide', 'image' => 'images/events/event-1-2.jpg', 'alt' => 'Fundraising and outreach event', 'eyebrow' => 'Events', 'title' => 'Annual drive'],
            ['cat' => 'community', 'layout' => 'cell', 'image' => 'images/events/event-1-3.jpg', 'alt' => 'Community volunteers together', 'eyebrow' => 'Community', 'title' => 'Volunteer day'],
            ['cat' => 'programs', 'layout' => 'cell', 'image' => 'images/events/event-1-2.jpg', 'alt' => 'Health and wellness outreach', 'eyebrow' => 'Programs', 'title' => 'Health camp'],
            ['cat' => 'events', 'layout' => 'banner', 'image' => 'images/events/event-1-1.jpg', 'alt' => 'Celebration at community event', 'eyebrow' => 'Events', 'title' => 'Together we go further', 'text' => 'Thank-you gatherings and impact stories from Chennai neighborhoods.'],
            ['cat' => 'community', 'layout' => 'cell', 'image' => 'images/events/event-1-3.jpg', 'alt' => 'Children at community program', 'eyebrow' => 'Community', 'title' => 'Youth circle'],
            ['cat' => 'programs', 'layout' => 'cell', 'image' => 'images/events/event-1-1.jpg', 'alt' => 'Donation supplies distribution', 'eyebrow' => 'Programs', 'title' => 'Relief kits'],
        ],
    ],

    'jobs' => [
        'eyebrow' => 'Careers',
        'title' => 'Build a Career with Purpose',
        'text' => 'Explore opportunities to work with a team dedicated to strengthening communities. Together, we create lasting impact. Email your résumé to',
    ],
];

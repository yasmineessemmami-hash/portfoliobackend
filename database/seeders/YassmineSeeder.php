<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SiteSetting;
use App\Models\HomeHero;
use App\Models\AboutHero;
use App\Models\AboutIntroduction;
use App\Models\AboutService;
use App\Models\AboutWorkProcess;
use App\Models\AboutValue;
use App\Models\AboutStat;
use App\Models\SkillCategory;
use App\Models\Skill;
use App\Models\Project;
use App\Models\ContactSocialLink;

class YassmineSeeder extends Seeder
{
    public function run(): void
    {
        // Settings
        $setting = SiteSetting::first() ?? new SiteSetting();
        $setting->full_name = 'Yassmine Es-Semmami';
        $setting->contact_email = 'yassmine@example.com';
        $setting->save();

        // HomeHero
        HomeHero::truncate();
        HomeHero::create([
            'status_text' => 'Available for work',
            'status_active' => true,
            'full_name' => "Hi, I'm Yassmine.",
            'role_title' => 'Computer Engineering Student & Full-Stack Developer',
            'headline' => 'Computer Engineering Student & Full-Stack Developer',
            'subheadline' => 'I build modern web applications with React, Laravel, SQL and Java, combining clean interfaces, solid development practices and a passion for solving real-world problems.'
        ]);

        // AboutHero
        AboutHero::truncate();
        AboutHero::create([
            'title' => 'About Me',
            'subtitle' => 'A Computer Engineering Student Building Modern Digital Experiences',
            'description' => 'I’m Yassmine Es-Semmami, a Computer Engineering student and Full-Stack Developer based in Marrakech, Morocco.'
        ]);

        // AboutIntroduction
        AboutIntroduction::truncate();
        AboutIntroduction::create([
            'availability_active' => true,
            'availability_text' => 'Available',
            'full_name' => 'Yassmine Es-Semmami',
            'role_title' => 'Learning. Building. Improving.',
            'paragraphs' => [
                'I am currently pursuing a Master\'s degree in Computer Engineering after completing my Bachelor\'s degree in the same field. Through academic projects and internships, I have gained hands-on experience in web development, full-stack application development, database management, and software engineering.',
                'I enjoy transforming ideas and requirements into practical digital solutions. I am particularly interested in React, Laravel, SQL and Java, while continuously developing my technical skills through independent learning and real-world projects.',
                'I value clean development, teamwork, organization and continuous improvement.',
                'Current Status: Master\'s Student in Computer Engineering',
                'Education: Bachelor\'s Degree in Computer Engineering + Master\'s studies',
                'Location: Marrakech, Morocco',
                'Focus: Full-Stack Web Development'
            ],
            'tech_stack' => ['React', 'Laravel', 'SQL', 'Java', 'JavaScript']
        ]);

        // AboutStats
        AboutStat::truncate();
        AboutStat::create([
            'key' => 'internships',
            'value' => '2',
            'label' => 'Internship Experiences',
            'sort_order' => 1
        ]);
        AboutStat::create([
            'key' => 'projects',
            'value' => '3+',
            'label' => 'Major Featured Projects',
            'sort_order' => 2
        ]);

        // AboutService (Highlight Cards)
        AboutService::truncate();
        AboutService::create([
            'title' => 'Full-Stack Development',
            'description' => 'I develop complete web applications across the frontend and backend, connecting modern user interfaces with application logic and databases. My experience includes React, Laravel and SQL.',
            'features' => [],
            'sort_order' => 1
        ]);
        AboutService::create([
            'title' => 'Web Development',
            'description' => 'I enjoy creating responsive and user-friendly websites and web applications using HTML, CSS, JavaScript, React, PHP and Laravel, with a focus on clean interfaces and practical functionality.',
            'features' => [],
            'sort_order' => 2
        ]);
        AboutService::create([
            'title' => 'Software Engineering',
            'description' => 'My computer engineering background has given me a strong foundation in programming, software development, databases, system concepts and application design. I approach projects by focusing on structure, logic and maintainability.',
            'features' => [],
            'sort_order' => 3
        ]);
        AboutService::create([
            'title' => 'Problem Solving',
            'description' => 'I enjoy understanding requirements, analyzing technical problems and finding practical solutions. I am comfortable learning independently, adapting to new technologies and improving my skills through hands-on projects.',
            'features' => [],
            'sort_order' => 4
        ]);

        // AboutWorkProcess
        AboutWorkProcess::truncate();
        AboutWorkProcess::create([
            'step' => '01',
            'title' => 'Understand',
            'description' => 'I begin by understanding the project objectives, user needs, requirements and constraints before choosing the appropriate technical approach.',
            'sort_order' => 1
        ]);
        AboutWorkProcess::create([
            'step' => '02',
            'title' => 'Plan',
            'description' => 'I organize the project structure, define the main functionalities and think about the architecture, technologies and database requirements before development.',
            'sort_order' => 2
        ]);
        AboutWorkProcess::create([
            'step' => '03',
            'title' => 'Build',
            'description' => 'I develop the application step by step, working across the frontend and backend when required, while keeping the interface clear and the code organized.',
            'sort_order' => 3
        ]);
        AboutWorkProcess::create([
            'step' => '04',
            'title' => 'Improve',
            'description' => 'I test the functionality, identify problems, make improvements and refine the user experience. I also continue learning throughout the development process.',
            'sort_order' => 4
        ]);

        // AboutValue
        AboutValue::truncate();
        AboutValue::create([
            'key' => 'lucide-book-open',
            'title' => 'Continuous Learning',
            'description' => 'I am motivated by continuous learning and enjoy discovering new technologies and improving my development skills.',
            'sort_order' => 1
        ]);
        AboutValue::create([
            'key' => 'lucide-users',
            'title' => 'Teamwork',
            'description' => 'I enjoy collaborating with others, exchanging ideas and contributing to team projects.',
            'sort_order' => 2
        ]);
        AboutValue::create([
            'key' => 'lucide-calendar',
            'title' => 'Organization',
            'description' => 'I value organization, time management and completing tasks within deadlines.',
            'sort_order' => 3
        ]);
        AboutValue::create([
            'key' => 'lucide-refresh-cw',
            'title' => 'Adaptability',
            'description' => 'I am comfortable learning new tools and adapting to different technical requirements and project environments.',
            'sort_order' => 4
        ]);

        // Skills
        SkillCategory::truncate();
        Skill::truncate();
        
        $frontend = SkillCategory::create(['key' => 'frontend', 'icon_key' => 'lucide-layout', 'title' => 'Frontend', 'description' => 'Frontend technologies', 'sort_order' => 1]);
        Skill::create(['category_id' => $frontend->id, 'name' => 'React', 'sort_order' => 1]);
        Skill::create(['category_id' => $frontend->id, 'name' => 'JavaScript', 'sort_order' => 2]);
        Skill::create(['category_id' => $frontend->id, 'name' => 'HTML', 'sort_order' => 3]);
        Skill::create(['category_id' => $frontend->id, 'name' => 'CSS', 'sort_order' => 4]);

        $backend = SkillCategory::create(['key' => 'backend', 'icon_key' => 'lucide-server', 'title' => 'Backend', 'description' => 'Backend technologies', 'sort_order' => 2]);
        Skill::create(['category_id' => $backend->id, 'name' => 'Laravel', 'sort_order' => 1]);
        Skill::create(['category_id' => $backend->id, 'name' => 'PHP', 'sort_order' => 2]);
        Skill::create(['category_id' => $backend->id, 'name' => 'Java', 'sort_order' => 3]);

        $db = SkillCategory::create(['key' => 'database', 'icon_key' => 'lucide-database', 'title' => 'Database', 'description' => 'Database technologies', 'sort_order' => 3]);
        Skill::create(['category_id' => $db->id, 'name' => 'SQL', 'sort_order' => 1]);
        Skill::create(['category_id' => $db->id, 'name' => 'MySQL', 'sort_order' => 2]);
        Skill::create(['category_id' => $db->id, 'name' => 'Microsoft SQL Server', 'sort_order' => 3]);

        $prog = SkillCategory::create(['key' => 'programming', 'icon_key' => 'lucide-code', 'title' => 'Programming', 'description' => 'Programming Languages', 'sort_order' => 4]);
        Skill::create(['category_id' => $prog->id, 'name' => 'C', 'sort_order' => 1]);
        Skill::create(['category_id' => $prog->id, 'name' => 'C++', 'sort_order' => 2]);
        Skill::create(['category_id' => $prog->id, 'name' => 'C#', 'sort_order' => 3]);
        Skill::create(['category_id' => $prog->id, 'name' => 'Java', 'sort_order' => 4]);
        Skill::create(['category_id' => $prog->id, 'name' => 'Python', 'sort_order' => 5]);

        $mod = SkillCategory::create(['key' => 'modeling', 'icon_key' => 'lucide-settings', 'title' => 'Modeling & Development', 'description' => 'Modeling and tools', 'sort_order' => 5]);
        Skill::create(['category_id' => $mod->id, 'name' => 'UML', 'sort_order' => 1]);
        Skill::create(['category_id' => $mod->id, 'name' => 'Merise', 'sort_order' => 2]);
        Skill::create(['category_id' => $mod->id, 'name' => 'DevOps', 'sort_order' => 3]);
        Skill::create(['category_id' => $mod->id, 'name' => 'ERP', 'sort_order' => 4]);
        Skill::create(['category_id' => $mod->id, 'name' => 'Computer Networking & Security', 'sort_order' => 5]);

        // Projects
        if (Project::count() == 0) {
            Project::create([
                'title' => 'Modular E-Learning Platform with Granular License Management',
                'description' => 'A modular e-learning platform designed to manage online learning content and granular access licenses.',
                'image' => '',
                'image_type' => 'url',
                'key' => 'elearning',
                'tech_stack' => ['Laravel', 'React', 'SQL'],
                'github_url' => '',
                'live_url' => '',
                'is_featured' => true,
                'sort_order' => 1
            ]);
            Project::create([
                'title' => 'Management Consulting Web Platform',
                'description' => 'A web platform designed for a management consulting company to support digital management and business activities. Developed during the internship at Next Challenge, Casablanca.',
                'image' => '',
                'image_type' => 'url',
                'key' => 'consulting',
                'tech_stack' => ['Laravel', 'React', 'SQL'],
                'github_url' => '',
                'live_url' => '',
                'is_featured' => true,
                'sort_order' => 2
            ]);
            Project::create([
                'title' => 'Workbridge — Coworking Space Management System',
                'description' => 'A coworking space management system developed to support space booking, event participation, and package purchases.',
                'image' => '',
                'image_type' => 'url',
                'key' => 'workbridge',
                'tech_stack' => ['HTML', 'CSS', 'JavaScript', 'PHP', 'SQL'],
                'github_url' => '',
                'live_url' => '',
                'is_featured' => true,
                'sort_order' => 3
            ]);
            Project::create([
                'title' => 'Coffee Shop Website',
                'description' => 'A simple web website developed for a coffee shop.',
                'image' => '',
                'image_type' => 'url',
                'key' => 'coffeeshop',
                'tech_stack' => ['HTML', 'CSS', 'JavaScript'],
                'github_url' => '',
                'live_url' => '',
                'is_featured' => true,
                'sort_order' => 4
            ]);
            Project::create([
                'title' => 'Library Management Application',
                'description' => 'A library management application developed as part of academic work.',
                'image' => '',
                'image_type' => 'url',
                'key' => 'library',
                'tech_stack' => ['VB.NET'],
                'github_url' => '',
                'live_url' => '',
                'is_featured' => false,
                'sort_order' => 5
            ]);
            Project::create([
                'title' => 'Web Applications & Academic Projects',
                'description' => 'Various static and dynamic web applications developed during academic projects.',
                'image' => '',
                'image_type' => 'url',
                'key' => 'academic',
                'tech_stack' => ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL', 'React'],
                'github_url' => '',
                'live_url' => '',
                'is_featured' => false,
                'sort_order' => 6
            ]);
        }
        
        // Update github link
        ContactSocialLink::truncate();
        ContactSocialLink::create([
            'label' => 'GitHub',
            'url' => 'https://github.com/yasmineessemmami-hash',
            'icon_key' => 'lucide-github',
            'sort_order' => 1
        ]);

        // Meta Page for About
        \App\Models\MetaPage::updateOrCreate(
            ['page' => 'about', 'locale' => 'en'],
            [
                'slug' => 'about',
                'title' => 'About Yassmine | Full-Stack Developer & Computer Engineering Student',
                'description' => 'Learn about Yassmine Es-Semmami, a Full-Stack Developer based in Marrakech specializing in React, Laravel, and building modern web applications.',
                'keywords' => ['Yassmine Es-Semmami', 'Full-Stack Developer', 'Computer Engineering', 'React Developer Morocco', 'Laravel Developer', 'Portfolio']
            ]
        );

        // ServicesHero
        \App\Models\ServicesHero::truncate();
        \App\Models\ServicesHero::create([
            'title' => 'My Services',
            'subtitle' => 'What I Do',
            'description' => 'I provide comprehensive web development services, combining clean interfaces with robust backend logic to build modern, functional digital experiences.'
        ]);

        // ServiceItem
        \App\Models\ServiceItem::truncate();
        \App\Models\ServiceItem::create([
            'title' => 'Full-Stack Web Development',
            'description' => 'End-to-end development of custom web applications using modern technologies.',
            'icon_key' => 'lucide-layout-template',
            'color' => 'primary',
            'features' => ['Custom Frontend Design', 'Responsive Layouts', 'Database Architecture', 'Server-side Logic'],
            'sort_order' => 1
        ]);
        \App\Models\ServiceItem::create([
            'title' => 'API Development',
            'description' => 'Building secure, scalable RESTful APIs to power web and mobile applications.',
            'icon_key' => 'lucide-server',
            'color' => 'secondary',
            'features' => ['RESTful Architecture', 'Authentication & Security', 'Third-party Integrations', 'Data Validation'],
            'sort_order' => 2
        ]);
        \App\Models\ServiceItem::create([
            'title' => 'UI/UX Implementation',
            'description' => 'Translating design concepts into responsive, interactive, and accessible web interfaces.',
            'icon_key' => 'lucide-palette',
            'color' => 'accent',
            'features' => ['Interactive UI Components', 'CSS Animations', 'Mobile-First Approach', 'Accessibility Standards'],
            'sort_order' => 3
        ]);

        // WhyChooseMeItem
        \App\Models\WhyChooseMeItem::truncate();
        \App\Models\WhyChooseMeItem::create([
            'title' => 'Clean Code',
            'description' => 'I prioritize writing maintainable, organized, and well-structured code following best practices.',
            'icon_key' => 'lucide-code',
            'sort_order' => 1
        ]);
        \App\Models\WhyChooseMeItem::create([
            'title' => 'Problem-Solving Approach',
            'description' => 'I analyze requirements carefully to deliver logical and practical technical solutions.',
            'icon_key' => 'lucide-lightbulb',
            'sort_order' => 2
        ]);
        \App\Models\WhyChooseMeItem::create([
            'title' => 'Continuous Learner',
            'description' => 'I constantly explore new technologies to bring the latest innovations to your projects.',
            'icon_key' => 'lucide-book-open',
            'sort_order' => 3
        ]);

        // DeliverableItem
        \App\Models\DeliverableItem::truncate();
        \App\Models\DeliverableItem::create([
            'title' => 'Source Code',
            'description' => 'Complete, well-commented source code delivered via a secure version control repository.',
            'icon_key' => 'lucide-github',
            'sort_order' => 1
        ]);
        \App\Models\DeliverableItem::create([
            'title' => 'Technical Documentation',
            'description' => 'Clear guidelines on how to install, configure, and maintain the application.',
            'icon_key' => 'lucide-file-text',
            'sort_order' => 2
        ]);
        \App\Models\DeliverableItem::create([
            'title' => 'Deployment Assistance',
            'description' => 'Help with setting up the application on your chosen hosting or cloud environment.',
            'icon_key' => 'lucide-cloud',
            'sort_order' => 3
        ]);

        // Meta Page for Services
        \App\Models\MetaPage::updateOrCreate(
            ['page' => 'services', 'locale' => 'en'],
            [
                'slug' => 'services',
                'title' => 'Services | Yassmine Es-Semmami - Full-Stack Developer',
                'description' => 'Explore the web development services offered by Yassmine Es-Semmami, including full-stack development, API creation, and UI/UX implementation.',
                'keywords' => ['Web Development Services', 'Full-Stack Development', 'API Development', 'React Developer', 'Laravel Developer', 'Freelance Developer']
            ]
        );

        // Meta Page for Projects
        \App\Models\MetaPage::updateOrCreate(
            ['page' => 'projects', 'locale' => 'en'],
            [
                'slug' => 'projects',
                'title' => 'Projects | Yassmine Es-Semmami - Full-Stack Developer',
                'description' => 'Browse the portfolio and projects of Yassmine Es-Semmami. Includes modern web applications, e-learning platforms, and full-stack solutions built with React, Laravel, and SQL.',
                'keywords' => ['Yassmine Es-Semmami Projects', 'Full-Stack Portfolio', 'React Projects', 'Laravel Applications', 'Web Development Portfolio', 'Software Engineering Projects']
            ]
        );

        // Meta Page for Home
        \App\Models\MetaPage::updateOrCreate(
            ['page' => 'home', 'locale' => 'en'],
            [
                'slug' => 'home',
                'title' => 'Yassmine Es-Semmami | Computer Engineering Student & Full-Stack Developer',
                'description' => 'Portfolio of Yassmine Es-Semmami, a Full-Stack Developer and Computer Engineering student based in Marrakech, specializing in React, Laravel, and Java.',
                'keywords' => ['Yassmine Es-Semmami', 'Full-Stack Developer', 'Computer Engineering', 'React', 'Laravel', 'Java', 'Portfolio', 'Marrakech']
            ]
        );

        // Meta Page for Skills
        \App\Models\MetaPage::updateOrCreate(
            ['page' => 'skills', 'locale' => 'en'],
            [
                'slug' => 'skills',
                'title' => 'Technical Skills | Yassmine Es-Semmami',
                'description' => 'Technical skills and expertise of Yassmine Es-Semmami. Proficient in Frontend (React), Backend (Laravel, PHP, Java), Databases (SQL), and Software Engineering.',
                'keywords' => ['Yassmine Es-Semmami Skills', 'React', 'Laravel', 'Java', 'PHP', 'SQL', 'UML', 'Software Engineering']
            ]
        );

        // Meta Page for Contact
        \App\Models\MetaPage::updateOrCreate(
            ['page' => 'contact', 'locale' => 'en'],
            [
                'slug' => 'contact',
                'title' => 'Contact | Yassmine Es-Semmami',
                'description' => 'Get in touch with Yassmine Es-Semmami for web development projects, freelance opportunities, or professional networking.',
                'keywords' => ['Contact Yassmine', 'Hire Full-Stack Developer', 'Freelance Web Developer', 'React Developer Contact']
            ]
        );

        // ProjectsHero
        \App\Models\ProjectsHero::truncate();
        \App\Models\ProjectsHero::create([
            'title' => 'Featured Projects',
            'subtitle' => 'My Work',
            'description' => 'Selected projects developed during my academic and professional journey, showcasing my full-stack web development and software engineering skills.'
        ]);

        // SkillsHero
        \App\Models\SkillsHero::truncate();
        \App\Models\SkillsHero::create([
            'title' => 'Technical Skills',
            'subtitle' => 'My Expertise',
            'description' => 'A comprehensive overview of the technologies, languages, and tools I use to build robust and modern applications.'
        ]);

        // ContactHero
        \App\Models\ContactHero::truncate();
        \App\Models\ContactHero::create([
            'title' => 'Get in Touch',
            'subtitle' => 'Contact Me',
            'description' => 'I am always open to discussing new projects, creative ideas, or opportunities to be part of your visions.'
        ]);

        // ContactInfo
        \App\Models\ContactInfo::truncate();
        \App\Models\ContactInfo::create([
            'label' => 'Email',
            'value' => 'yasmineessemmami@gmail.com',
            'icon_key' => 'lucide-mail',
            'type' => 'email',
            'sort_order' => 1
        ]);
        \App\Models\ContactInfo::create([
            'label' => 'Phone',
            'value' => '0707221287',
            'icon_key' => 'lucide-phone',
            'type' => 'phone',
            'sort_order' => 2
        ]);
        \App\Models\ContactInfo::create([
            'label' => 'Address',
            'value' => 'Marrakech, Morocco',
            'icon_key' => 'lucide-map-pin',
            'type' => 'address',
            'sort_order' => 3
        ]);
        \App\Models\ContactInfo::create([
            'label' => 'Business Hours',
            'value' => 'Monday - Friday, 9:00 AM to 6:00 PM',
            'icon_key' => 'lucide-clock',
            'type' => 'hours',
            'sort_order' => 4
        ]);

        // Meta Page for FAQ
        \App\Models\MetaPage::updateOrCreate(
            ['page' => 'faq', 'locale' => 'en'],
            [
                'slug' => 'faq',
                'title' => 'FAQ | Yassmine Es-Semmami',
                'description' => 'Frequently asked questions about my services, availability, and web development processes.',
                'keywords' => ['FAQ', 'Web Development FAQ', 'Freelance Developer FAQ', 'Yassmine Es-Semmami']
            ]
        );

        // FAQHero
        \App\Models\FAQHero::truncate();
        \App\Models\FAQHero::create([
            'title' => 'Frequently Asked Questions',
            'subtitle' => 'Common Queries',
            'description' => 'Here are some common questions about my skills, availability, and how I approach development projects.'
        ]);

        // FAQItems
        \App\Models\FAQItem::truncate();
        \App\Models\FAQItem::create([
            'question' => 'What technologies do you specialize in?',
            'answer' => 'I specialize in full-stack web development using React for the frontend and Laravel (PHP) or Java for the backend, along with robust SQL database management.',
            'sort_order' => 1
        ]);
        \App\Models\FAQItem::create([
            'question' => 'Are you currently available for freelance projects?',
            'answer' => 'Yes, I am always open to discussing new freelance projects and opportunities. Feel free to reach out via the Contact page to discuss your project requirements.',
            'sort_order' => 2
        ]);
        \App\Models\FAQItem::create([
            'question' => 'What is your typical development process?',
            'answer' => 'My process involves four main steps: Understanding the requirements, Planning the architecture and design, Building the application iteratively, and Improving through testing and refinements.',
            'sort_order' => 3
        ]);
        \App\Models\FAQItem::create([
            'question' => 'Do you provide ongoing support after a project is completed?',
            'answer' => 'Yes, I can provide technical documentation and deployment assistance. Ongoing maintenance and updates can also be arranged depending on the project scope.',
            'sort_order' => 4
        ]);

        // Meta Page for Blog
        \App\Models\MetaPage::updateOrCreate(
            ['page' => 'blog', 'locale' => 'en'],
            [
                'slug' => 'blog',
                'title' => 'Blog | Yassmine Es-Semmami - Full-Stack Developer',
                'description' => 'Read my latest articles and thoughts on web development, computer engineering, React, Laravel, and more.',
                'keywords' => ['Yassmine Es-Semmami Blog', 'Web Development Blog', 'React Articles', 'Laravel Tutorials', 'Software Engineering Blog']
            ]
        );

        // BlogHero
        \App\Models\BlogHero::truncate();
        \App\Models\BlogHero::create([
            'title' => 'My Blog',
            'subtitle' => 'Thoughts & Tutorials',
            'description' => 'I occasionally write about web development, software engineering concepts, and my experiences learning new technologies.'
        ]);

        // BlogAuthor
        if (\App\Models\BlogAuthor::count() == 0) {
            $author = \App\Models\BlogAuthor::create([
                'name' => 'Yassmine Es-Semmami',
                'avatar' => '',
                'bio' => 'Computer Engineering Student & Full-Stack Developer based in Marrakech, Morocco.',
                'social_links' => [
                    ['label' => 'GitHub', 'url' => 'https://github.com/yasmineessemmami-hash', 'icon_key' => 'lucide-github']
                ]
            ]);
        } else {
            $author = \App\Models\BlogAuthor::first();
        }

        // BlogPost
        if (\App\Models\BlogPost::count() == 0) {
            \App\Models\BlogPost::create([
                'title' => 'Getting Started with Laravel and React',
                'slug' => 'getting-started-laravel-react',
                'content' => [
                    ['type' => 'paragraph', 'content' => 'Building modern web applications often requires a robust backend and a dynamic frontend. Combining Laravel and React is one of the most popular full-stack choices.']
                ],
                'image' => '',
                'media_type' => 'image',
                'category' => 'Web Development',
                'tags' => ['Laravel', 'React', 'Full-Stack'],
                'author_id' => $author->id,
                'published_at' => now()->subDays(5),
                'is_featured' => true
            ]);
            
            \App\Models\BlogPost::create([
                'title' => 'My Journey in Computer Engineering',
                'slug' => 'my-journey-computer-engineering',
                'content' => [
                    ['type' => 'paragraph', 'content' => 'Studying Computer Engineering has given me a deep understanding of how software and hardware interact, building a strong foundation for my career as a developer.']
                ],
                'image' => '',
                'media_type' => 'image',
                'category' => 'Education',
                'tags' => ['Computer Engineering', 'Student Life', 'Career'],
                'author_id' => $author->id,
                'published_at' => now()->subDays(15),
                'is_featured' => false
            ]);
        }
    }
}


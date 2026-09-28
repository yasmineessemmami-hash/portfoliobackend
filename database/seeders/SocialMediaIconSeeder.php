<?php

namespace Database\Seeders;

use App\Models\SocialMediaIcon;
use Illuminate\Database\Seeder;

class SocialMediaIconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $icons = [
            // Code/Development Platforms
            [
                'key' => 'github',
                'icon' => 'FaGithub',
                'name' => 'GitHub',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'gitlab',
                'icon' => 'FaGitlab',
                'name' => 'GitLab',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'bitbucket',
                'icon' => 'FaBitbucket',
                'name' => 'Bitbucket',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'stackOverflow',
                'icon' => 'FaStackOverflow',
                'name' => 'Stack Overflow',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'dev',
                'icon' => 'FaDev',
                'name' => 'Dev.to',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'medium',
                'icon' => 'FaMedium',
                'name' => 'Medium',
                'library' => 'react-icons/fa6',
            ],

            // Professional & Social Networks
            [
                'key' => 'linkedin',
                'icon' => 'FaLinkedin',
                'name' => 'LinkedIn',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'x',
                'icon' => 'FaXTwitter',
                'name' => 'X (Twitter)',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'facebook',
                'icon' => 'FaFacebook',
                'name' => 'Facebook',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'instagram',
                'icon' => 'FaInstagram',
                'name' => 'Instagram',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'youtube',
                'icon' => 'FaYoutube',
                'name' => 'YouTube',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'tiktok',
                'icon' => 'FaTiktok',
                'name' => 'TikTok',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'reddit',
                'icon' => 'FaReddit',
                'name' => 'Reddit',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'pinterest',
                'icon' => 'FaPinterest',
                'name' => 'Pinterest',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'snapchat',
                'icon' => 'FaSnapchat',
                'name' => 'Snapchat',
                'library' => 'react-icons/fa6',
            ],

            // Design/Portfolio Platforms
            [
                'key' => 'behance',
                'icon' => 'FaBehance',
                'name' => 'Behance',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'dribbble',
                'icon' => 'FaDribbble',
                'name' => 'Dribbble',
                'library' => 'react-icons/fa6',
            ],

            // Communication
            [
                'key' => 'email',
                'icon' => 'FaEnvelope',
                'name' => 'Email',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'phone',
                'icon' => 'FaPhone',
                'name' => 'Phone',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'whatsApp',
                'icon' => 'FaWhatsapp',
                'name' => 'WhatsApp',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'telegram',
                'icon' => 'FaTelegram',
                'name' => 'Telegram',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'discord',
                'icon' => 'FaDiscord',
                'name' => 'Discord',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'skype',
                'icon' => 'FaSkype',
                'name' => 'Skype',
                'library' => 'react-icons/fa6',
            ],
            [
                'key' => 'signal',
                'icon' => 'SiSignal',
                'name' => 'Signal',
                'library' => 'react-icons/si',
            ],
            [
                'key' => 'slack',
                'icon' => 'FaSlack',
                'name' => 'Slack',
                'library' => 'react-icons/fa6',
            ],

            // Freelance Platforms
            [
                'key' => 'upwork',
                'icon' => 'SiUpwork',
                'name' => 'Upwork',
                'library' => 'react-icons/si',
            ],
            [
                'key' => 'fiverr',
                'icon' => 'SiFiverr',
                'name' => 'Fiverr',
                'library' => 'react-icons/si',
            ],
            [
                'key' => 'freelancer',
                'icon' => 'SiFreelancer',
                'name' => 'Freelancer',
                'library' => 'react-icons/si',
            ],

            // General
            [
                'key' => 'website',
                'icon' => 'FaGlobe',
                'name' => 'Website',
                'library' => 'react-icons/fa6',
            ],
        ];

        // Delete all existing social media icons
        SocialMediaIcon::query()->delete();

        // Insert icons
        foreach ($icons as $icon) {
            SocialMediaIcon::create($icon);
        }

        $this->command->info('Social media icons seeded successfully! Total: ' . count($icons));
    }
}

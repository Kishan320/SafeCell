<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\BusinessSetting;
use App\Models\BusinessPage;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Translation;
use App\Models\Tag;
use App\Models\Banner;
use App\Models\DealOfTheDay;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\User;
use App\Models\Review;

class SafeCellMasterSeeder extends Seeder
{
    private string $fontBold = '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf';
    private string $fontReg = '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf';

    public function run(): void
    {
        $this->command->info("Starting SafeCell Serum Master Seeding...");

        $this->prepareDirectories();
        $this->updateBusinessSettings();
        $this->updateBusinessPages();
        $this->updateShop();
        $this->generateLogos();
        $this->generateBanners();

        $categories = $this->seedCategories();
        $brands = $this->seedBrands();
        $products = $this->seedProducts($categories, $brands);
        $this->seedPromotions($products);
        $this->seedCustomersAndReviews($products);

        Cache::flush();
        $this->command->info("SafeCell Serum Master Seeding completed successfully!");
    }

    private function prepareDirectories(): void
    {
        $dirs = [
            storage_path('app/public/company'),
            storage_path('app/public/category'),
            storage_path('app/public/brand'),
            storage_path('app/public/banner'),
            storage_path('app/public/product'),
            storage_path('app/public/product/thumbnail'),
            storage_path('app/public/product/meta'),
            storage_path('app/public/review'),
        ];
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }
    }

    private function hexColor($im, string $hex): int
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return imagecolorallocate($im, $r, $g, $b);
    }

    private function updateBusinessSettings(): void
    {
        $settings = [
            'company_name' => 'SafeCell',
            'company_email' => 'care@safecell.com',
            'company_phone' => '+1 (800) 723-3235',
            'shop_address' => 'SafeCell Dermatology & Cellular Research Labs, 100 Innovation Way, Suite 400, San Francisco, CA 94105',
            'company_copyright_text' => '© 2026 SafeCell Laboratories Inc. All rights reserved.',
            'company_web_logo' => 'safecell-web-logo.png',
            'company_mobile_logo' => 'safecell-mobile-logo.png',
            'company_footer_logo' => 'safecell-footer-logo.png',
            'company_fav_icon' => 'safecell-fav-icon.png',
            'announcement' => json_encode([
                'status' => '1',
                'color' => '#0f172a',
                'text_color' => '#ffffff',
                'announcement' => '✨ Welcome to SafeCell Serum Lab | Free Express Shipping on Orders Over $50 | 100% Dermatologist Formulated'
            ]),
            'company_reliability' => json_encode([
                [
                    'item' => 'delivery_info',
                    'title' => 'Fast Express Cellular Care Delivery',
                    'image' => '',
                    'status' => 1
                ],
                [
                    'item' => 'safe_payment',
                    'title' => 'Secure & Encrypted SafeCell Checkout',
                    'image' => '',
                    'status' => 1
                ],
                [
                    'item' => 'return_policy',
                    'title' => '30-Day Clinical Efficacy Guarantee',
                    'image' => '',
                    'status' => 1
                ],
                [
                    'item' => 'authentic_product',
                    'title' => '100% Dermatologist Formulated Bio-Actives',
                    'image' => '',
                    'status' => 1
                ]
            ]),
        ];

        foreach ($settings as $type => $value) {
            BusinessSetting::updateOrCreate(['type' => $type], ['value' => $value]);
        }
    }

    private function updateBusinessPages(): void
    {
        $aboutUsHtml = '<h2>Welcome to SafeCell Cellular Laboratories</h2>'
            . '<p>SafeCell is a clinical-grade dermatological research laboratory and formulation studio dedicated exclusively to high-potency, bio-available skin and cellular serums. Our mission is to bridge molecular biology and topical skincare to deliver targeted cellular repair, radiant renewal, and lasting dermal resilience.</p>'
            . '<h3>Our Serum Science & Philosophy</h3>'
            . '<ul>'
            . '<li><strong>Clinically Proven Bio-Actives:</strong> Formulated with multi-molecular weight Hyaluronic Acids, pure L-Ascorbic Acid, bioactive Peptides, encapsulated Retinol, and skin-identical Ceramides.</li>'
            . '<li><strong>Cellular Penetration Technology:</strong> Optimized pH delivery systems and micro-emulsions ensure key actives penetrate deep into epidermal layers without causing surface irritation.</li>'
            . '<li><strong>100% Clean, Cruelty-Free & Dermatologist Approved:</strong> Free from artificial parabens, synthetic fragrances, sulfates, and mineral oils. Certified cruelty-free and dermatologically validated on all sensitive skin types.</li>'
            . '</ul>'
            . '<p>Experience the transformative power of cellular serums tailored precisely to your skin’s unique cellular cycle.</p>';

        BusinessPage::updateOrCreate(['slug' => 'about-us'], [
            'title' => 'About SafeCell Serum Lab',
            'description' => $aboutUsHtml,
        ]);
    }

    private function updateShop(): void
    {
        Shop::updateOrCreate(['id' => 1], [
            'seller_id' => 0,
            'author_type' => 'admin',
            'name' => 'SafeCell Official Serum Store',
            'slug' => 'safecell-official-serum-store',
            'address' => 'SafeCell Dermatology & Cellular Research Labs, San Francisco, CA',
            'contact' => '+1 (800) 723-3235',
            'image' => 'safecell-shop-logo.png',
            'banner' => 'safecell-shop-banner.png',
            'image_storage_type' => 'public',
            'banner_storage_type' => 'public',
        ]);
    }

    private function generateLogos(): void
    {
        $compDir = storage_path('app/public/company');

        // Web Logo (400 x 90)
        $w = 400; $h = 90;
        $im = imagecreatetruecolor($w, $h);
        imagealphablending($im, false);
        imagesavealpha($im, true);
        $trans = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefilledrectangle($im, 0, 0, $w, $h, $trans);
        imagealphablending($im, true);

        $cTeal = $this->hexColor($im, '0284c7');
        $cDark = $this->hexColor($im, '0f172a');
        $cWhite = $this->hexColor($im, 'ffffff');

        // Droplet / Shield Icon
        imagefilledellipse($im, 45, 45, 54, 54, $cTeal);
        imagefilledellipse($im, 45, 45, 44, 44, $this->hexColor($im, '0369a1'));
        imagefilledellipse($im, 45, 45, 22, 22, $cWhite);
        imagefilledellipse($im, 45, 45, 12, 12, $cTeal);

        imagettftext($im, 26, 0, 85, 50, $cDark, $this->fontBold, 'SafeCell');
        imagettftext($im, 9, 0, 87, 70, $this->hexColor($im, '64748b'), $this->fontBold, 'CELLULAR SKINCARE LABS');
        imagepng($im, "$compDir/safecell-web-logo.png");
        imagedestroy($im);

        // Mobile Logo (240 x 70)
        $w = 240; $h = 70;
        $im = imagecreatetruecolor($w, $h);
        imagealphablending($im, false);
        imagesavealpha($im, true);
        $trans = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefilledrectangle($im, 0, 0, $w, $h, $trans);
        imagealphablending($im, true);

        $cTeal = $this->hexColor($im, '0284c7');
        $cDark = $this->hexColor($im, '0f172a');
        $cWhite = $this->hexColor($im, 'ffffff');

        imagefilledellipse($im, 35, 35, 42, 42, $cTeal);
        imagefilledellipse($im, 35, 35, 34, 34, $this->hexColor($im, '0369a1'));
        imagefilledellipse($im, 35, 35, 16, 16, $cWhite);

        imagettftext($im, 20, 0, 68, 42, $cDark, $this->fontBold, 'SafeCell');
        imagepng($im, "$compDir/safecell-mobile-logo.png");
        imagedestroy($im);

        // Footer Logo (white text for dark footer backgrounds)
        $w = 400; $h = 90;
        $im = imagecreatetruecolor($w, $h);
        imagealphablending($im, false);
        imagesavealpha($im, true);
        $trans = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefilledrectangle($im, 0, 0, $w, $h, $trans);
        imagealphablending($im, true);

        $cTeal = $this->hexColor($im, '38bdf8');
        $cWhite = $this->hexColor($im, 'ffffff');

        imagefilledellipse($im, 45, 45, 54, 54, $cTeal);
        imagefilledellipse($im, 45, 45, 44, 44, $this->hexColor($im, '0284c7'));
        imagefilledellipse($im, 45, 45, 22, 22, $cWhite);
        imagefilledellipse($im, 45, 45, 12, 12, $cTeal);

        imagettftext($im, 26, 0, 85, 50, $cWhite, $this->fontBold, 'SafeCell');
        imagettftext($im, 9, 0, 87, 70, $this->hexColor($im, '94a3b8'), $this->fontBold, 'CELLULAR SKINCARE LABS');
        imagepng($im, "$compDir/safecell-footer-logo.png");
        imagedestroy($im);

        // Favicon (64 x 64)
        $im = imagecreatetruecolor(64, 64);
        imagealphablending($im, false);
        imagesavealpha($im, true);
        $trans = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefilledrectangle($im, 0, 0, 64, 64, $trans);
        imagealphablending($im, true);
        imagefilledellipse($im, 32, 32, 60, 60, $cTeal);
        imagefilledellipse($im, 32, 32, 48, 48, $this->hexColor($im, '0284c7'));
        imagefilledellipse($im, 32, 32, 24, 24, $cWhite);
        imagefilledellipse($im, 32, 32, 12, 12, $cTeal);
        imagepng($im, "$compDir/safecell-fav-icon.png");
        imagedestroy($im);

        // Shop Logo (300 x 300)
        $im = imagecreatetruecolor(300, 300);
        $bg = $this->hexColor($im, '0f172a');
        imagefill($im, 0, 0, $bg);
        imagefilledellipse($im, 150, 120, 120, 120, $cTeal);
        imagefilledellipse($im, 150, 120, 96, 96, $this->hexColor($im, '0369a1'));
        imagefilledellipse($im, 150, 120, 48, 48, $cWhite);
        imagettftext($im, 22, 0, 105, 220, $cWhite, $this->fontBold, 'SafeCell');
        imagettftext($im, 11, 0, 92, 250, $this->hexColor($im, '38bdf8'), $this->fontBold, 'OFFICIAL STORE');
        imagepng($im, "$compDir/safecell-shop-logo.png");
        imagedestroy($im);

        // Shop Banner (1200 x 300)
        $im = imagecreatetruecolor(1200, 300);
        $bg = $this->hexColor($im, '090d16');
        imagefill($im, 0, 0, $bg);
        imagefilledrectangle($im, 0, 280, 1200, 300, $cTeal);
        imagettftext($im, 36, 0, 80, 130, $cWhite, $this->fontBold, 'SafeCell Cellular Research Laboratories');
        imagettftext($im, 16, 0, 80, 180, $this->hexColor($im, '94a3b8'), $this->fontReg, 'Official Direct-From-Lab Store • 100% Freshly Formulated Potent Bio-Serums');
        imagepng($im, "$compDir/safecell-shop-banner.png");
        imagedestroy($im);
    }

    private function generateBanners(): void
    {
        $bannerDir = storage_path('app/public/banner');

        // Main Banner 1 (1200 x 400)
        $this->createHeroBanner(
            "$bannerDir/banner-hero-1.png",
            1200, 400,
            '0f172a', '1e293b',
            'ADVANCED CELLULAR BIO-SERUMS',
            'Precision-Formulated Actives Targeting Collagen, Hyaluronic & Cellular Renewal',
            'EXPLORE RADIANCE & REPAIR COLLECTION',
            '0284c7'
        );

        // Main Banner 2 (1200 x 400)
        $this->createHeroBanner(
            "$bannerDir/banner-hero-2.png",
            1200, 400,
            '2b1810', '451a03',
            'PURE 20% VITAMIN C + FERULIC RADIANCE',
            'Instant Antioxidant Protection, Dark Spot Correction & Dermal Luminescence',
            'DISCOVER VITAMIN C ELIXIRS • 20% OFF',
            'd97706'
        );

        // Main Banner 3 (1200 x 400)
        $this->createHeroBanner(
            "$bannerDir/banner-hero-3.png",
            1200, 400,
            '042f2e', '134e4a',
            'MULTI-PEPTIDE + COPPER CELLULAR MATRIX',
            'Clinically Proven to Smooth Fine Lines and Restore Elasticity at the Dermal Level',
            'SHOP CLINICAL PEPTIDES',
            '0d9488'
        );

        // Main Section Banner (2000 x 500)
        $this->createHeroBanner(
            "$bannerDir/banner-section-1.png",
            2000, 500,
            '090d16', '1e1b4b',
            'SAFECELL CELLULAR FORMULATIONS: THE SCIENCE OF AGLESS RADIANCE',
            '100% Bio-Active • Zero Parabens • Clinically Validated Dermatology • Made in California',
            'DISCOVER THE FORMULAS',
            '6366f1'
        );

        // Footer Banner 1 (1000 x 500)
        $this->createHeroBanner(
            "$bannerDir/banner-footer-1.png",
            1000, 500,
            '082f49', '0c4a6e',
            'TRIPLE HYALURONIC ACID & CERAMIDE BARRIER',
            'Deep Cellular Hydration & Skin Lipid Replenishing Infusion',
            'SHOP HYDRATION ESSENTIALS',
            '0284c7'
        );

        // Footer Banner 2 (1000 x 500)
        $this->createHeroBanner(
            "$bannerDir/banner-footer-2.png",
            1000, 500,
            '3b0764', '581c87',
            'TARGETED EYE CONTOUR: CAFFEINE 5% + MATRIXYL',
            'De-Puff Under-Eyes & Smooth Crow’s Feet in 14 Days',
            'SHOP EYE SERUMS',
            'a855f7'
        );

        // Top Side Banner (500 x 500)
        $this->createHeroBanner(
            "$bannerDir/banner-topside-1.png",
            500, 500,
            '0f172a', '1e293b',
            'SAFECELL TOP PICKS',
            'Best-Selling Dermatologist Serums',
            'ORDER NOW',
            '0284c7'
        );

        // Insert Banners in DB
        DB::table('banners')->truncate();

        $bannersData = [
            [
                'photo' => 'banner-hero-1.png',
                'banner_type' => 'Main Banner',
                'theme' => 'default',
                'published' => 1,
                'url' => url('/products?category_id=1&data_from=category&page=1'),
                'resource_type' => 'category',
                'resource_id' => 1,
                'title' => 'Advanced Cellular Bio-Serums',
                'sub_title' => 'Precision Formulations for Youthful Radiance',
                'button_text' => 'Shop Now',
                'background_color' => '#0f172a',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'photo' => 'banner-hero-2.png',
                'banner_type' => 'Main Banner',
                'theme' => 'default',
                'published' => 1,
                'url' => url('/products?name=vitamin+c&data_from=search&page=1'),
                'resource_type' => 'product',
                'resource_id' => 1,
                'title' => 'Pure 20% Vitamin C + Ferulic Radiance',
                'sub_title' => 'Clinical Brightening & Antioxidant Protection',
                'button_text' => 'Explore 20% Off',
                'background_color' => '#2b1810',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'photo' => 'banner-hero-3.png',
                'banner_type' => 'Main Banner',
                'theme' => 'default',
                'published' => 1,
                'url' => url('/products?category_id=2&data_from=category&page=1'),
                'resource_type' => 'category',
                'resource_id' => 2,
                'title' => 'Multi-Peptide + Copper Cellular Matrix',
                'sub_title' => 'Dermal Elasticity & Firmness Restored',
                'button_text' => 'Discover Peptides',
                'background_color' => '#042f2e',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'photo' => 'banner-section-1.png',
                'banner_type' => 'Main Section Banner',
                'theme' => 'default',
                'published' => 1,
                'url' => url('/products'),
                'resource_type' => 'category',
                'resource_id' => 1,
                'title' => 'SafeCell Cellular Formulations',
                'sub_title' => '100% Bio-Active Skincare Science',
                'button_text' => 'View All Serums',
                'background_color' => '#090d16',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'photo' => 'banner-footer-1.png',
                'banner_type' => 'Footer Banner',
                'theme' => 'default',
                'published' => 1,
                'url' => url('/products?category_id=3&data_from=category&page=1'),
                'resource_type' => 'category',
                'resource_id' => 3,
                'title' => 'Triple Hyaluronic & Ceramide Barrier',
                'sub_title' => 'Deep Moisture Lock',
                'button_text' => 'Shop Hydration',
                'background_color' => '#082f49',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'photo' => 'banner-footer-2.png',
                'banner_type' => 'Footer Banner',
                'theme' => 'default',
                'published' => 1,
                'url' => url('/products?category_id=5&data_from=category&page=1'),
                'resource_type' => 'category',
                'resource_id' => 5,
                'title' => 'Targeted Eye Contour Serums',
                'sub_title' => '5% Caffeine + Peptides',
                'button_text' => 'Shop Eye Care',
                'background_color' => '#3b0764',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'photo' => 'banner-topside-1.png',
                'banner_type' => 'Top Side Banner',
                'theme' => 'default',
                'published' => 1,
                'url' => url('/products'),
                'resource_type' => 'category',
                'resource_id' => 1,
                'title' => 'SafeCell Top Picks',
                'sub_title' => 'Best Sellers',
                'button_text' => 'Order Now',
                'background_color' => '#0f172a',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('banners')->insert($bannersData);
    }

    private function createHeroBanner(
        string $filePath, int $w, int $h,
        string $bgHex1, string $bgHex2,
        string $title, string $subtitle, string $badge, string $accentHex
    ): void {
        $im = imagecreatetruecolor($w, $h);
        $bg1 = $this->hexColor($im, $bgHex1);
        $bg2 = $this->hexColor($im, $bgHex2);
        $accent = $this->hexColor($im, $accentHex);
        $white = $this->hexColor($im, 'ffffff');
        $gray = $this->hexColor($im, 'cbd5e1');

        // Gradient simulation
        for ($i = 0; $i < $w; $i++) {
            $ratio = $i / $w;
            $r1 = hexdec(substr($bgHex1, 0, 2)); $g1 = hexdec(substr($bgHex1, 2, 2)); $b1 = hexdec(substr($bgHex1, 4, 2));
            $r2 = hexdec(substr($bgHex2, 0, 2)); $g2 = hexdec(substr($bgHex2, 2, 2)); $b2 = hexdec(substr($bgHex2, 4, 2));
            $r = (int)($r1 + ($r2 - $r1) * $ratio);
            $g = (int)($g1 + ($g2 - $g1) * $ratio);
            $b = (int)($b1 + ($b2 - $b1) * $ratio);
            $c = imagecolorallocate($im, $r, $g, $b);
            imageline($im, $i, 0, $i, $h, $c);
        }

        // Draw decorative subtle circles / cellular rings
        imageellipse($im, (int)($w * 0.85), (int)($h * 0.5), (int)($h * 0.9), (int)($h * 0.9), $accent);
        imageellipse($im, (int)($w * 0.85), (int)($h * 0.5), (int)($h * 0.6), (int)($h * 0.6), $accent);
        imageellipse($im, (int)($w * 0.85), (int)($h * 0.5), (int)($h * 0.3), (int)($h * 0.3), $accent);

        // Draw Serum Bottle silhouette on the right side
        $bx = (int)($w * 0.85);
        $by = (int)($h * 0.5);
        $bw = (int)($h * 0.22);
        $bh = (int)($h * 0.6);
        imagefilledrectangle($im, $bx - (int)($bw / 2), $by - (int)($bh * 0.3), $bx + (int)($bw / 2), $by + (int)($bh * 0.45), $white);
        imagefilledrectangle($im, $bx - (int)($bw * 0.2), $by - (int)($bh * 0.45), $bx + (int)($bw * 0.2), $by - (int)($bh * 0.3), $accent);
        imagefilledellipse($im, $bx, (int)($by - ($bh * 0.47)), (int)($bw * 0.35), (int)($bw * 0.35), $white);

        // Accent tag bar
        $tagY = (int)($h * 0.26);
        imagefilledrectangle($im, 60, $tagY - 18, 60 + (strlen($badge) * 9), $tagY + 8, $accent);
        imagettftext($im, 9, 0, 70, $tagY, $white, $this->fontBold, strtoupper($badge));

        // Title
        $titleSize = $w > 1500 ? 28 : ($w > 800 ? 22 : 14);
        $titleY = (int)($h * 0.48);
        imagettftext($im, $titleSize, 0, 60, $titleY, $white, $this->fontBold, $title);

        // Subtitle
        $subSize = $w > 1500 ? 14 : ($w > 800 ? 12 : 9);
        $subY = (int)($h * 0.64);
        imagettftext($im, $subSize, 0, 60, $subY, $gray, $this->fontReg, $subtitle);

        // SafeCell watermark
        imagettftext($im, 10, 0, 60, (int)($h * 0.85), $accent, $this->fontBold, 'SafeCell • Cellular Science Formulation');

        imagepng($im, $filePath);
        imagedestroy($im);
    }

    private function seedCategories(): array
    {
        DB::table('categories')->truncate();
        $catDir = storage_path('app/public/category');

        $mainCategories = [
            1 => [
                'name' => 'Face & Radiance Serums',
                'slug' => 'face-and-radiance-serums',
                'color' => 'd97706',
                'accent' => 'fef3c7',
                'sub' => [
                    'Vitamin C & Brightening Actives',
                    'Niacinamide & Pore Refining Drops',
                    'Alpha Arbutin & Dark Spot Corrector',
                    'Glycolic & Lactic Glow Resurfacing',
                ]
            ],
            2 => [
                'name' => 'Anti-Aging & Cellular Repair',
                'slug' => 'anti-aging-and-cellular-repair',
                'color' => '4f46e5',
                'accent' => 'e0e7ff',
                'sub' => [
                    'Pure Retinol & Retinoid Complex',
                    'Multi-Peptide & Copper Tripeptide',
                    'Epidermal Growth Factor (EGF)',
                    'Collagen & Elasticity Boosters',
                ]
            ],
            3 => [
                'name' => 'Hydration & Barrier Recovery',
                'slug' => 'hydration-and-barrier-recovery',
                'color' => '0284c7',
                'accent' => 'e0f2fe',
                'sub' => [
                    'Multi-Molecular Hyaluronic Acid 2%',
                    'Ceramide Complex & Lipid Replenishing',
                    'Centella Asiatica (Cica) & Soothing',
                    'Polyglutamic Acid Deep Moisture Lock',
                ]
            ],
            4 => [
                'name' => 'Clarifying & Blemish Serums',
                'slug' => 'clarifying-and-blemish-serums',
                'color' => '059669',
                'accent' => 'd1fae5',
                'sub' => [
                    'Salicylic Acid (BHA) 2% Pore Clearing',
                    'Azelaic Acid 10% Redness Calming',
                    'Tea Tree & Zinc PCA Sebum Balancing',
                    'Gentle AHA+BHA Peeling Formulations',
                ]
            ],
            5 => [
                'name' => 'Targeted Eye & Specialty Serums',
                'slug' => 'targeted-eye-and-specialty-serums',
                'color' => 'be185d',
                'accent' => 'fce7f3',
                'sub' => [
                    'Caffeine 5% + EGCG Eye Contour',
                    'Matrixyl 3000 Wrinkle Relaxer',
                    'Natural Bakuchiol Retinol Alternative',
                    'Multi-Peptide Scalp & Hair Density',
                ]
            ],
        ];

        $seededCategories = [];
        $seededSubcategories = [];
        $catIdCounter = 1;

        foreach ($mainCategories as $mainIndex => $catData) {
            $iconFile = "cat-{$mainIndex}.png";
            $this->createCategoryIcon("$catDir/$iconFile", $catData['name'], $catData['color'], $catData['accent']);

            $mainCatId = DB::table('categories')->insertGetId([
                'id' => $catIdCounter++,
                'name' => $catData['name'],
                'slug' => $catData['slug'],
                'icon' => $iconFile,
                'icon_storage_type' => 'public',
                'parent_id' => 0,
                'position' => 0,
                'home_status' => 1,
                'priority' => $mainIndex,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $seededCategories[$mainIndex] = [
                'id' => $mainCatId,
                'name' => $catData['name'],
                'sub' => [],
            ];

            foreach ($catData['sub'] as $subIdx => $subName) {
                $subSlug = Str::slug($subName);
                $subIconFile = "subcat-{$mainCatId}-" . ($subIdx + 1) . ".png";
                $this->createCategoryIcon("$catDir/$subIconFile", $subName, $catData['color'], $catData['accent']);

                $subCatId = DB::table('categories')->insertGetId([
                    'id' => $catIdCounter++,
                    'name' => $subName,
                    'slug' => $subSlug,
                    'icon' => $subIconFile,
                    'icon_storage_type' => 'public',
                    'parent_id' => $mainCatId,
                    'position' => 1,
                    'home_status' => 1,
                    'priority' => $subIdx + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $seededCategories[$mainIndex]['sub'][] = [
                    'id' => $subCatId,
                    'name' => $subName,
                ];
                $seededSubcategories[] = [
                    'id' => $subCatId,
                    'main_id' => $mainCatId,
                    'name' => $subName,
                    'color' => $catData['color'],
                ];
            }
        }

        return [
            'main' => $seededCategories,
            'flat_sub' => $seededSubcategories,
        ];
    }

    private function createCategoryIcon(string $path, string $name, string $colorHex, string $accentHex): void
    {
        $im = imagecreatetruecolor(500, 500);
        $bg = $this->hexColor($im, 'ffffff');
        imagefill($im, 0, 0, $bg);

        $color = $this->hexColor($im, $colorHex);
        $accent = $this->hexColor($im, $accentHex);
        $dark = $this->hexColor($im, '0f172a');
        $white = $this->hexColor($im, 'ffffff');

        // Circular background
        imagefilledellipse($im, 250, 220, 320, 320, $accent);
        imageellipse($im, 250, 220, 320, 320, $color);
        imageellipse($im, 250, 220, 300, 300, $color);

        // Serum Bottle Silhouette
        imagefilledrectangle($im, 205, 170, 295, 310, $color);
        imagefilledrectangle($im, 220, 200, 280, 280, $white);
        imagefilledrectangle($im, 235, 130, 265, 170, $dark);
        imagefilledellipse($im, 250, 115, 36, 36, $color);

        // SafeCell label on bottle
        imagettftext($im, 10, 0, 228, 235, $dark, $this->fontBold, 'SafeCell');
        imagettftext($im, 8, 0, 232, 255, $color, $this->fontBold, 'SERUM');

        // Category Name Below
        $words = explode(' ', $name);
        $line1 = implode(' ', array_slice($words, 0, 2));
        $line2 = implode(' ', array_slice($words, 2));

        $box1 = imagettfbbox(16, 0, $this->fontBold, $line1);
        $w1 = abs($box1[4] - $box1[0]);
        imagettftext($im, 16, 0, (int)(250 - ($w1 / 2)), 410, $dark, $this->fontBold, $line1);

        if ($line2) {
            $box2 = imagettfbbox(14, 0, $this->fontReg, $line2);
            $w2 = abs($box2[4] - $box2[0]);
            imagettftext($im, 14, 0, (int)(250 - ($w2 / 2)), 445, $color, $this->fontReg, $line2);
        }

        imagepng($im, $path);
        imagedestroy($im);
    }

    private function seedBrands(): array
    {
        DB::table('brands')->truncate();
        $brandDir = storage_path('app/public/brand');

        $brandNames = [
            1 => 'SafeCell Clinical Labs',
            2 => 'SafeCell Botanical Actives',
            3 => 'SafeCell Cellular Matrix',
            4 => 'DermRevive Cellular',
            5 => 'LuminaBio Formulations',
            6 => 'HydroPure Elixirs',
            7 => 'BioPeptide Science',
            8 => 'PureRadiance Derma',
            9 => 'CelluShield Organics',
            10 => 'AuraGen Skincare',
        ];

        $seededBrands = [];
        foreach ($brandNames as $id => $name) {
            $logoFile = "brand-{$id}.png";
            $this->createBrandLogo("$brandDir/$logoFile", $name);

            DB::table('brands')->insert([
                'id' => $id,
                'name' => $name,
                'slug' => Str::slug($name),
                'image' => $logoFile,
                'image_storage_type' => 'public',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $seededBrands[$id] = $name;
        }

        return $seededBrands;
    }

    private function createBrandLogo(string $path, string $name): void
    {
        $im = imagecreatetruecolor(500, 500);
        $bg = $this->hexColor($im, 'f8fafc');
        imagefill($im, 0, 0, $bg);

        $dark = $this->hexColor($im, '0f172a');
        $border = $this->hexColor($im, 'cbd5e1');
        $accent = $this->hexColor($im, '0284c7');
        $white = $this->hexColor($im, 'ffffff');

        // Card frame
        imagefilledrectangle($im, 30, 30, 470, 470, $white);
        imagerectangle($im, 30, 30, 470, 470, $border);
        imagerectangle($im, 38, 38, 462, 462, $border);

        // Circular Emblem
        imagefilledellipse($im, 250, 190, 130, 130, $dark);
        imagefilledellipse($im, 250, 190, 114, 114, $accent);
        imagefilledellipse($im, 250, 190, 80, 80, $white);

        // Monogram in emblem
        $initials = '';
        foreach (explode(' ', $name) as $w) {
            $initials .= strtoupper($w[0]);
            if (strlen($initials) >= 2) break;
        }
        $box = imagettfbbox(28, 0, $this->fontBold, $initials);
        $iw = abs($box[4] - $box[0]);
        imagettftext($im, 28, 0, (int)(250 - ($iw / 2)), 202, $dark, $this->fontBold, $initials);

        // Brand Name Text
        $words = explode(' ', $name);
        $w1 = $words[0] . (isset($words[1]) ? ' ' . $words[1] : '');
        $w2 = isset($words[2]) ? implode(' ', array_slice($words, 2)) : 'LABORATORY DERMA';

        $b1 = imagettfbbox(17, 0, $this->fontBold, $w1);
        $bw1 = abs($b1[4] - $b1[0]);
        imagettftext($im, 17, 0, (int)(250 - ($bw1 / 2)), 320, $dark, $this->fontBold, $w1);

        $b2 = imagettfbbox(11, 0, $this->fontBold, $w2);
        $bw2 = abs($b2[4] - $b2[0]);
        imagettftext($im, 11, 0, (int)(250 - ($bw2 / 2)), 355, $accent, $this->fontBold, $w2);

        // Subtitle badge
        imagettftext($im, 9, 0, 160, 410, $this->hexColor($im, '64748b'), $this->fontReg, 'DERMATOLOGIST VERIFIED');

        imagepng($im, $path);
        imagedestroy($im);
    }

    private function seedProducts(array $categories, array $brands): array
    {
        DB::table('products')->truncate();
        DB::table('product_stocks')->truncate();
        DB::table('translations')->truncate();
        DB::table('tags')->truncate();
        DB::table('product_tag')->truncate();

        $prodDir = storage_path('app/public/product');
        $thumbDir = storage_path('app/public/product/thumbnail');

        // Prepare 100 Serums data list
        $serumsData = $this->get100SerumsDefinitions();

        // Generate base serum images (20 unique bottle themes)
        $this->generateSerumBottleTemplates();

        $tagIds = [];
        $commonTags = [
            'serum', 'skincare', 'cellular', 'safecell', 'anti-aging',
            'hydration', 'radiance', 'vitamin-c', 'peptides', 'retinol',
            'hyaluronic-acid', 'niacinamide', 'ceramides', 'cica', 'bha-salicylic',
            'dermatologist-tested', 'glow', 'skin-barrier', 'spot-corrector', 'clean-beauty'
        ];
        foreach ($commonTags as $tag) {
            $tagId = DB::table('tags')->insertGetId([
                'tag' => $tag,
                'visit_count' => rand(15, 250),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $tagIds[$tag] = $tagId;
        }

        $seededProducts = [];
        $productId = 1;

        foreach ($serumsData as $index => $item) {
            $currentId = $productId++;
            $subcatInfo = $categories['flat_sub'][$item['subcat_index']];
            $mainCatId = $subcatInfo['main_id'];
            $subCatId = $subcatInfo['id'];
            $brandId = ($currentId % 10) + 1;

            $imageFile = "serum-prod-{$currentId}.png";
            $templateIdx = ($currentId % 20) + 1;
            copy("$prodDir/template-{$templateIdx}.png", "$prodDir/$imageFile");
            copy("$prodDir/template-{$templateIdx}.png", "$thumbDir/$imageFile");

            $slug = Str::slug($item['name']);
            $code = 'SC-SER-' . str_pad($currentId, 3, '0', STR_PAD_LEFT);
            $price = $item['price'];
            $purchasePrice = round($price * 0.45, 2);
            $stock = rand(95, 450);
            $discount = ($currentId % 3 == 0) ? rand(10, 25) : 0;
            $isFeatured = ($currentId <= 20 || $currentId % 5 == 0) ? 1 : 0;

            $detailsHtml = $this->buildSerumDetailsHtml($item);

            $productRecord = [
                'id' => $currentId,
                'added_by' => 'admin',
                'user_id' => 1,
                'shop_id' => 1,
                'name' => $item['name'],
                'slug' => $slug,
                'product_type' => 'physical',
                'category_ids' => json_encode([
                    ['id' => (string)$mainCatId, 'position' => 1],
                    ['id' => (string)$subCatId, 'position' => 2],
                ]),
                'category_id' => (string)$mainCatId,
                'sub_category_id' => (string)$subCatId,
                'sub_sub_category_id' => null,
                'brand_id' => $brandId,
                'unit' => $item['unit'] ?? '30ml Dropper Bottle',
                'min_qty' => 1,
                'refundable' => 1,
                'images' => json_encode([$imageFile]),
                'color_image' => json_encode([]),
                'thumbnail' => $imageFile,
                'thumbnail_storage_type' => 'public',
                'preview_file' => '',
                'preview_file_storage_type' => 'public',
                'featured' => (string)$isFeatured,
                'featured_status' => $isFeatured,
                'status' => 1,
                'request_status' => 1,
                'published' => 1,
                'unit_price' => $price,
                'purchase_price' => $purchasePrice,
                'tax' => '0.00',
                'tax_type' => 'percent',
                'tax_model' => 'exclude',
                'discount' => (string)$discount,
                'discount_type' => 'percent',
                'current_stock' => $stock,
                'minimum_order_qty' => 1,
                'details' => $detailsHtml,
                'free_shipping' => ($price >= 65) ? 1 : 0,
                'colors' => json_encode([]),
                'attributes' => json_encode([]),
                'choice_options' => json_encode([]),
                'variation' => json_encode([]),
                'meta_title' => $item['name'] . ' | SafeCell Serum Lab',
                'meta_description' => strip_tags(substr($detailsHtml, 0, 155)),
                'meta_image' => $imageFile,
                'code' => $code,
                'video_provider' => 'youtube',
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now(),
            ];

            DB::table('products')->insert($productRecord);

            // Stock record
            DB::table('product_stocks')->insert([
                'product_id' => $currentId,
                'variant' => '',
                'sku' => $code,
                'price' => $price,
                'qty' => $stock,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Translation record
            DB::table('translations')->insert([
                'translationable_type' => 'App\Models\Product',
                'translationable_id' => $currentId,
                'locale' => 'en',
                'key' => 'name',
                'value' => $item['name'],
            ]);

            DB::table('translations')->insert([
                'translationable_type' => 'App\Models\Product',
                'translationable_id' => $currentId,
                'locale' => 'en',
                'key' => 'description',
                'value' => $detailsHtml,
            ]);

            // Attach 3-4 relevant tags
            $selectedTags = ['serum', 'skincare', 'safecell'];
            if (isset($item['tag'])) {
                $selectedTags[] = $item['tag'];
            }
            foreach ($selectedTags as $tName) {
                if (isset($tagIds[$tName])) {
                    DB::table('product_tag')->insert([
                        'product_id' => $currentId,
                        'tag_id' => $tagIds[$tName],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $seededProducts[] = [
                'id' => $currentId,
                'name' => $item['name'],
                'price' => $price,
            ];
        }

        return $seededProducts;
    }

    private function generateSerumBottleTemplates(): void
    {
        $prodDir = storage_path('app/public/product');

        $themes = [
            1  => ['name' => 'VITAMIN C 20%', 'color' => 'd97706', 'bg' => 'fffbeb', 'sub' => 'Ferulic Radiance'],
            2  => ['name' => 'ETHYL ASCORBIC', 'color' => 'b45309', 'bg' => 'fff7ed', 'sub' => 'Luminous Glow'],
            3  => ['name' => 'NIACINAMIDE 10%', 'color' => '0284c7', 'bg' => 'f0f9ff', 'sub' => '+ Zinc 1% Pore Clear'],
            4  => ['name' => 'ALPHA ARBUTIN 2%', 'color' => '0369a1', 'bg' => 'f0fdf4', 'sub' => 'Pigment Control'],
            5  => ['name' => 'GLYCOLIC ACID 7%', 'color' => 'e11d48', 'bg' => 'fff1f2', 'sub' => 'Overnight Peeling'],
            6  => ['name' => 'PURE RETINOL 1%', 'color' => '4338ca', 'bg' => 'eef2ff', 'sub' => 'Cellular Repair'],
            7  => ['name' => 'RETINOID 2%', 'color' => '6366f1', 'bg' => 'f5f3ff', 'sub' => 'Granactive Squalane'],
            8  => ['name' => 'MULTI-PEPTIDE 1%', 'color' => '0f766e', 'bg' => 'f0fdfa', 'sub' => 'Copper Tripeptide-1'],
            9  => ['name' => 'MATRIXYL 3000', 'color' => '312e81', 'bg' => 'f8fafc', 'sub' => 'Wrinkle Defense'],
            10 => ['name' => 'BIO EGF REPAIR', 'color' => '1e293b', 'bg' => 'f1f5f9', 'sub' => 'Growth Factor Complex'],
            11 => ['name' => 'VEGAN COLLAGEN', 'color' => 'be185d', 'bg' => 'fdf2f8', 'sub' => 'Elasticity Plumper'],
            12 => ['name' => 'HYALURONIC 2%', 'color' => '0284c7', 'bg' => 'e0f2fe', 'sub' => '+ B5 Hydration Quench'],
            13 => ['name' => '7D HYALURONIC', 'color' => '0369a1', 'bg' => 'f0f9ff', 'sub' => 'Multi-Molecular Matrix'],
            14 => ['name' => '5-CERAMIDE COMPLEX', 'color' => 'd97706', 'bg' => 'fefce8', 'sub' => 'Skin Barrier Shield'],
            15 => ['name' => 'CENTELLA CICA 80%', 'color' => '059669', 'bg' => 'ecfdf5', 'sub' => 'Calming Redness Relief'],
            16 => ['name' => 'POLYGLUTAMIC ACID', 'color' => '0891b2', 'bg' => 'ecfeff', 'sub' => '48H Moisture Reservoir'],
            17 => ['name' => 'SALICYLIC BHA 2%', 'color' => '047857', 'bg' => 'f0fdf4', 'sub' => 'Deep Pore Decongest'],
            18 => ['name' => 'AZELAIC ACID 10%', 'color' => '9333ea', 'bg' => 'faf5ff', 'sub' => 'Rosacea & Tone Clarifier'],
            19 => ['name' => 'CAFFEINE 5% + EGCG', 'color' => '78350f', 'bg' => 'fffbeb', 'sub' => 'Eye Contour De-Puff'],
            20 => ['name' => 'BAKUCHIOL 2%', 'color' => '831843', 'bg' => 'fff1f2', 'sub' => 'Gentle Phyto-Retinol'],
        ];

        foreach ($themes as $id => $th) {
            $this->createSerumBottleGraphic("$prodDir/template-{$id}.png", $th['name'], $th['color'], $th['bg'], $th['sub']);
        }
    }

    private function createSerumBottleGraphic(string $path, string $activeName, string $accentHex, string $bgHex, string $subName): void
    {
        $im = imagecreatetruecolor(500, 500);
        $bg = $this->hexColor($im, $bgHex);
        imagefill($im, 0, 0, $bg);

        $color = $this->hexColor($im, $accentHex);
        $dark = $this->hexColor($im, '0f172a');
        $white = $this->hexColor($im, 'ffffff');
        $gold = $this->hexColor($im, 'f59e0b');
        $gray = $this->hexColor($im, 'cbd5e1');
        $lightGray = $this->hexColor($im, 'f1f5f9');

        // Circular background glow
        imagefilledellipse($im, 250, 260, 340, 340, $lightGray);
        imageellipse($im, 250, 260, 340, 340, $color);

        // Bottle Shadow
        imagefilledellipse($im, 250, 440, 160, 26, $this->hexColor($im, 'e2e8f0'));

        // Bottle Glass Body (Amber / Tinted)
        imagefilledrectangle($im, 175, 180, 325, 430, $dark);

        // Bottle Shoulders
        imagefilledellipse($im, 250, 180, 150, 40, $dark);

        // Glass highlight reflection
        imagefilledrectangle($im, 182, 190, 194, 420, $this->hexColor($im, '334155'));

        // Dropper Pipette Collar / Neck
        imagefilledrectangle($im, 230, 130, 270, 170, $gray);
        imagefilledrectangle($im, 233, 132, 267, 168, $white);

        // Dropper Rubber Bulb / Cap
        imagefilledellipse($im, 250, 115, 60, 50, $dark);
        imagefilledellipse($im, 250, 95, 42, 35, $white);

        // Clean White Serum Label on Bottle
        imagefilledrectangle($im, 195, 220, 305, 395, $white);
        imagefilledrectangle($im, 195, 220, 305, 232, $color);

        // Label Typography
        imagettftext($im, 11, 0, 218, 255, $dark, $this->fontBold, 'SafeCell');
        imagettftext($im, 7, 0, 210, 272, $this->hexColor($im, '64748b'), $this->fontBold, 'CELLULAR LABS');

        // Divider
        imageline($im, 210, 280, 290, 280, $color);

        // Active ingredient badge
        $b1 = imagettfbbox(9, 0, $this->fontBold, $activeName);
        $w1 = abs($b1[4] - $b1[0]);
        imagettftext($im, 9, 0, (int)(250 - ($w1 / 2)), 305, $color, $this->fontBold, $activeName);

        $b2 = imagettfbbox(7, 0, $this->fontReg, $subName);
        $w2 = abs($b2[4] - $b2[0]);
        imagettftext($im, 7, 0, (int)(250 - ($w2 / 2)), 324, $dark, $this->fontReg, $subName);

        // Bottle size & certifications
        imagettftext($im, 7, 0, 220, 355, $this->hexColor($im, '64748b'), $this->fontReg, '30 ml / 1.0 fl oz');
        imagettftext($im, 6, 0, 208, 380, $color, $this->fontBold, 'CLINICALLY PROVEN');

        // Top Corner SafeCell Brand Tag
        imagettftext($im, 11, 0, 28, 45, $dark, $this->fontBold, 'SafeCell™');
        imagettftext($im, 8, 0, 28, 62, $color, $this->fontBold, 'BIO-ACTIVE SERUM');

        imagepng($im, $path);
        imagedestroy($im);
    }

    private function buildSerumDetailsHtml(array $item): string
    {
        $name = $item['name'];
        $actives = $item['actives'] ?? 'Clinical Bio-Active Complex, Multi-Molecular Hyaluronic Acid, Stabilized Antioxidants';
        $benefits = $item['benefits'] ?? 'Firms skin texture, diminishes hyperpigmentation, restores epidermal barrier, delivers deep cellular radiance.';
        $skinTypes = $item['skin_types'] ?? 'Normal, Dry, Combination, Sensitive & Breakout-Prone Skin.';
        $howToUse = 'Apply 3-4 drops morning and evening onto clean, slightly damp facial skin and neck before creams or moisturizers. Pat gently until fully absorbed. In the morning, follow with broad-spectrum SPF.';

        return "<div class='safecell-serum-description'>
            <h3>{$name} — SafeCell Cellular Dermatological Formulation</h3>
            <p>Formulated at the SafeCell Skincare Laboratories, this highly targeted serum combines high-potency molecular bio-actives to deliver visible clinical transformation at the cellular level.</p>
            
            <h4>Clinical Benefits:</h4>
            <ul>
                <li>{$benefits}</li>
                <li>Dermatologist-tested, non-comedogenic, and hypoallergenic.</li>
                <li>Zero parabens, artificial dyes, drying alcohols, or synthetic fragrances.</li>
            </ul>

            <h4>Key Active Ingredients:</h4>
            <p><strong>{$actives}</strong></p>

            <h4>Recommended For:</h4>
            <p>{$skinTypes}</p>

            <h4>How to Use:</h4>
            <p>{$howToUse}</p>
        </div>";
    }

    private function seedPromotions(array $products): void
    {
        // 1. Deal of the Day
        DB::table('deal_of_the_days')->truncate();
        DB::table('deal_of_the_days')->insert([
            'title' => 'SafeCell Deal of the Day: 20% Off Pure Vitamin C Radiance Serum',
            'product_id' => 1,
            'discount' => 20,
            'discount_type' => 'percent',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Flash Deal
        DB::table('flash_deals')->truncate();
        DB::table('flash_deal_products')->truncate();

        $flashId = DB::table('flash_deals')->insertGetId([
            'title' => 'SafeCell Spring Cellular Glow Flash Sale',
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDays(20)->format('Y-m-d'),
            'status' => 1,
            'featured' => 1,
            'background_color' => '#0f172a',
            'text_color' => '#ffffff',
            'banner' => 'banner-hero-1.png',
            'slug' => 'safecell-spring-cellular-glow-flash-sale',
            'deal_type' => 'flash_deal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add 8 products to Flash Deal
        for ($i = 1; $i <= 8; $i++) {
            DB::table('flash_deal_products')->insert([
                'flash_deal_id' => $flashId,
                'product_id' => $i,
                'discount' => rand(15, 30),
                'discount_type' => 'percent',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedCustomersAndReviews(array $products): void
    {
        $customers = [
            [
                'id' => 1,
                'f_name' => 'Emma',
                'l_name' => 'Watson',
                'email' => 'emma.watson@safecell.com',
                'phone' => '+14155550101',
            ],
            [
                'id' => 2,
                'f_name' => 'Sophia',
                'l_name' => 'Davis',
                'email' => 'sophia.davis@safecell.com',
                'phone' => '+14155550102',
            ],
            [
                'id' => 3,
                'f_name' => 'Olivia',
                'l_name' => 'Taylor',
                'email' => 'olivia.taylor@safecell.com',
                'phone' => '+14155550103',
            ],
            [
                'id' => 4,
                'f_name' => 'Liam',
                'l_name' => 'Miller',
                'email' => 'liam.miller@safecell.com',
                'phone' => '+14155550104',
            ],
            [
                'id' => 5,
                'f_name' => 'Chloe',
                'l_name' => 'Bennett',
                'email' => 'chloe.bennett@safecell.com',
                'phone' => '+14155550105',
            ],
        ];

        foreach ($customers as $c) {
            DB::table('users')->updateOrInsert(['id' => $c['id']], [
                'name' => "{$c['f_name']} {$c['l_name']}",
                'f_name' => $c['f_name'],
                'l_name' => $c['l_name'],
                'phone' => $c['phone'],
                'email' => $c['email'],
                'password' => bcrypt('12345678'),
                'is_active' => 1,
                'is_phone_verified' => 1,
                'is_email_verified' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('reviews')->truncate();

        $reviewComments = [
            "Incredible serum! My skin texture has smoothed out dramatically in just 10 days. The cellular glow is genuine.",
            "Absorbs in seconds without leaving any sticky residue. Layers beautifully under my daily moisturizer and sunscreen.",
            "Dermatologist recommended this active formula for my post-blemish redness and it worked wonders. 10/10!",
            "My fine lines around my eyes and forehead have visibly softened. SafeCell peptides are top-notch.",
            "Truly hydrating without causing any breakouts. I have very sensitive skin and experienced zero stinging or irritation.",
            "Noticeably brighter complexion after two weeks. Faded several stubborn sun spots on my cheeks.",
            "This serum transformed my compromised skin barrier. Calmed the dry patches and flaking within three days.",
            "Luxury medical-grade skincare quality at a reasonable price point. Ordered two more backup bottles!",
            "SafeCell has become the staple of my morning skincare routine. The active ingredients are pure and effective.",
            "My skin feels plump, bouncy, and hydrated all day long. Couldn't recommend this serum more highly."
        ];

        // Seed reviews for top 40 products
        for ($pId = 1; $pId <= 40; $pId++) {
            $numReviews = rand(2, 4);
            for ($r = 0; $r < $numReviews; $r++) {
                $cId = rand(1, 5);
                DB::table('reviews')->insert([
                    'product_id' => $pId,
                    'customer_id' => $cId,
                    'delivery_man_id' => null,
                    'order_id' => rand(1001, 1099),
                    'comment' => $reviewComments[array_rand($reviewComments)],
                    'attachment' => json_encode([]),
                    'rating' => rand(4, 5),
                    'status' => 1,
                    'is_saved' => 1,
                    'created_at' => now()->subDays(rand(2, 45)),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function get100SerumsDefinitions(): array
    {
        return [
            // Subcat 0: Vitamin C & Brightening Actives (Category 1)
            [
                'name' => 'SafeCell 20% Pure Vitamin C + Ferulic Radiance Serum',
                'subcat_index' => 0,
                'price' => 58.00,
                'actives' => '20% L-Ascorbic Acid, 0.5% Ferulic Acid, 1% Alpha-Tocopherol, Hyaluronic Acid',
                'benefits' => 'Fades stubborn sun spots, boosts cellular collagen synthesis, and provides 8x environmental antioxidant defense.',
                'tag' => 'vitamin-c',
            ],
            [
                'name' => 'SafeCell Ethyl Ascorbic Acid 15% Glow Restoration Serum',
                'subcat_index' => 0,
                'price' => 49.00,
                'actives' => '15% 3-O-Ethyl Ascorbic Acid, Licorice Root Extract, Glutathione',
                'benefits' => 'Highly stable non-oxidizing Vitamin C formulation for instant luminous radiance and even skin tone.',
                'tag' => 'radiance',
            ],
            [
                'name' => 'SafeCell Kakadu Plum & THD Ascorbate Dark Spot Elixir',
                'subcat_index' => 0,
                'price' => 64.00,
                'actives' => 'Tetrahexyldecyl Ascorbate 10%, Australian Wild Kakadu Plum, Squalane',
                'benefits' => 'Lipid-soluble Vitamin C that penetrates deep dermis for stubborn hyperpigmentation and photo-damage reversal.',
                'tag' => 'vitamin-c',
            ],
            [
                'name' => 'SafeCell Golden Radiance C-Ester Antioxidant Drops',
                'subcat_index' => 0,
                'price' => 46.00,
                'actives' => 'Ascorbyl Tetraisopalmitate, CoQ10, Jojoba Golden Oil, Sea Buckthorn',
                'benefits' => 'Nourishing antioxidant oil-serum hybrid delivering golden hour glow and dermal suppleness.',
                'tag' => 'radiance',
            ],
            [
                'name' => 'SafeCell Micro-Encapsulated Vitamin C Illuminating Serum',
                'subcat_index' => 0,
                'price' => 52.00,
                'actives' => 'Encapsulated Sodium Ascorbyl Phosphate, Niacinamide, Panthenol',
                'benefits' => 'Time-released gentle brightening ideal for sensitive skin prone to reactive redness.',
                'tag' => 'vitamin-c',
            ],

            // Subcat 1: Niacinamide & Pore Refining Drops (Category 1)
            [
                'name' => 'SafeCell 10% Niacinamide + 1% Zinc Pore Refining Serum',
                'subcat_index' => 1,
                'price' => 38.00,
                'actives' => '10% High Purity Niacinamide (Vitamin B3), 1% Zinc PCA, Hyaluronic Acid',
                'benefits' => 'Visibly minimizes enlarged pores, balances excess sebum, and strengthens epidermal lipid matrix.',
                'tag' => 'niacinamide',
            ],
            [
                'name' => 'SafeCell Niacinamide 12% + NAG Clarity Complex',
                'subcat_index' => 1,
                'price' => 44.00,
                'actives' => '12% Niacinamide, 3% N-Acetyl Glucosamine (NAG), Willow Bark Extract',
                'benefits' => 'Synergistic formulation dramatically targeting post-acne pigmentation and rough congested texture.',
                'tag' => 'niacinamide',
            ],
            [
                'name' => 'SafeCell Skin Smoothing Niacinamide Barrier Drops',
                'subcat_index' => 1,
                'price' => 41.00,
                'actives' => '8% Niacinamide, Ceramide NP, Centella Asiatica, Allantoin',
                'benefits' => 'Soothes stressed skin, refines micro-texture, and fortifies moisture retention.',
                'tag' => 'niacinamide',
            ],
            [
                'name' => 'SafeCell Ultra-Calming B3 Daily Balancing Serum',
                'subcat_index' => 1,
                'price' => 36.00,
                'actives' => '5% Niacinamide, Green Tea Polyphenols, Chamomile Extract, Beta-Glucan',
                'benefits' => 'Everyday soothing fluid that normalizes cellular sebum production and cools facial flushing.',
                'tag' => 'niacinamide',
            ],
            [
                'name' => 'SafeCell Niacinamide + Willow Bark Matte Glow Fluid',
                'subcat_index' => 1,
                'price' => 42.00,
                'actives' => '10% Niacinamide, Salix Alba Bark (Natural BHA), Silica Micro-Spheres',
                'benefits' => 'All-day matte finish with underlying luminous cellular clarity; controls shine without stripping.',
                'tag' => 'niacinamide',
            ],

            // Subcat 2: Alpha Arbutin & Dark Spot Corrector (Category 1)
            [
                'name' => 'SafeCell Pure Alpha Arbutin 2% Pigment Control Serum',
                'subcat_index' => 2,
                'price' => 45.00,
                'actives' => '2% Pure Alpha Arbutin, 2% Multi-Molecular Hyaluronic Acid, Lactic Acid',
                'benefits' => 'Clinically inhibits tyrosinase activity to fade dark spots, melasma patches, and age discoloration.',
                'tag' => 'spot-corrector',
            ],
            [
                'name' => 'SafeCell Tranexamic Acid 5% + Arbutin Discoloration Defense',
                'subcat_index' => 2,
                'price' => 59.00,
                'actives' => '5% Tranexamic Acid, 2% Alpha Arbutin, 3% Niacinamide, Phenylethyl Resorcinol',
                'benefits' => 'Multi-pathway dermatological treatment for severe stubborn hyperpigmentation and sun damage.',
                'tag' => 'spot-corrector',
            ],
            [
                'name' => 'SafeCell Kojic Acid & Licorice Melanin Corrector',
                'subcat_index' => 2,
                'price' => 48.00,
                'actives' => '1% Kojic Acid Dipalmitate, Glabridin Licorice Extract, Mulberry Bio-Ferment',
                'benefits' => 'Targets uneven skin tone and post-inflammatory erythema for clear, porcelain-like complexion.',
                'tag' => 'spot-corrector',
            ],
            [
                'name' => 'SafeCell Even Complexion Intensive Tone Serum',
                'subcat_index' => 2,
                'price' => 54.00,
                'actives' => 'Alpha Arbutin, Hexylresorcinol, Resveratrol, Sodium Hyaluronate',
                'benefits' => 'Antioxidant-powered evening serum that evens discoloration while imparting youthfulness.',
                'tag' => 'spot-corrector',
            ],
            [
                'name' => 'SafeCell Targeted Spot Fading Cellular Elixir',
                'subcat_index' => 2,
                'price' => 62.00,
                'actives' => 'Liposomal Arbutin, Tetrapeptide-30, Vitamin C Glycoside, Ferulic Acid',
                'benefits' => 'Micro-targeted applicator formula that accelerates dark spot fading by up to 65% in 4 weeks.',
                'tag' => 'spot-corrector',
            ],

            // Subcat 3: Glycolic & Lactic Glow Resurfacing (Category 1)
            [
                'name' => 'SafeCell Glycolic Acid 7% Resurfacing Night Serum',
                'subcat_index' => 3,
                'price' => 42.00,
                'actives' => '7% Glycolic Acid, Tasmanian Pepperberry, Aloe Vera, Amino Acids',
                'benefits' => 'Gently dissolves dead keratinized cells overnight to reveal instant baby-soft glass skin by morning.',
                'tag' => 'radiance',
            ],
            [
                'name' => 'SafeCell Lactic Acid 10% + HA Gentle Exfoliating Elixir',
                'subcat_index' => 3,
                'price' => 39.00,
                'actives' => '10% Purified Lactic Acid, Multi-Weight Hyaluronic Acid, Tasmanian Berry Extract',
                'benefits' => 'Humectant AHA exfoliating serum that smooths bumps and texture while boosting dermal moisture.',
                'tag' => 'radiance',
            ],
            [
                'name' => 'SafeCell Mandelic Acid 10% Sensitive Skin Smoothing Drops',
                'subcat_index' => 3,
                'price' => 44.00,
                'actives' => '10% Large-Molecule Mandelic Acid, Centella Asiatica, Panthenol',
                'benefits' => 'Ultra-gentle superficial peeling for reactive, rosacea-prone, and sensitized skin types.',
                'tag' => 'radiance',
            ],
            [
                'name' => 'SafeCell Multi-Fruit AHA Radiance Peeling Serum',
                'subcat_index' => 3,
                'price' => 47.00,
                'actives' => 'Bilberry, Sugar Cane, Orange, Lemon & Sugar Maple AHA Complex, Vitamin B5',
                'benefits' => 'Natural botanical acid synergy that clears dulling build-up and stimulates micro-circulation.',
                'tag' => 'radiance',
            ],
            [
                'name' => 'SafeCell Overnight Glass Skin Glycolic Concentrate',
                'subcat_index' => 3,
                'price' => 56.00,
                'actives' => '5% Micro-Dosed Glycolic, 2% Polyhydroxy Acid (PHA), Niacinamide, Squalane',
                'benefits' => 'Zero-downtime resurfacing booster delivering luminous, reflective glass skin glow.',
                'tag' => 'radiance',
            ],

            // Subcat 4: Pure Retinol & Retinoid Complex (Category 2)
            [
                'name' => 'SafeCell 0.5% Encapsulated Retinol in Squalane',
                'subcat_index' => 4,
                'price' => 54.00,
                'actives' => '0.5% Pure Micro-Encapsulated Retinol, Plant-Derived Squalane, Vitamin E',
                'benefits' => 'Slow-release cellular retinol that reduces fine lines and photodamage with minimal flaking.',
                'tag' => 'retinol',
            ],
            [
                'name' => 'SafeCell 1% Pure Retinol High Potency Night Serum',
                'subcat_index' => 4,
                'price' => 68.00,
                'actives' => '1.0% Pure Medical-Grade Retinol, Bisabolol, Ceramides, Phospholipids',
                'benefits' => 'High-potency cellular regeneration treatment for advanced signs of aging and deep wrinkles.',
                'tag' => 'retinol',
            ],
            [
                'name' => 'SafeCell Granactive Retinoid 2% Age Defense Emulsion',
                'subcat_index' => 4,
                'price' => 48.00,
                'actives' => '2% Hydroxypinacolone Retinoate (HPR), Tasmannia Lanceolata Fruit, HA',
                'benefits' => 'Next-gen retinoid that directly binds retinoic receptors without requiring metabolic conversion.',
                'tag' => 'retinol',
            ],
            [
                'name' => 'SafeCell Retinaldehyde 0.1% Cellular Regeneration Fluid',
                'subcat_index' => 4,
                'price' => 74.00,
                'actives' => '0.1% Retinaldehyde (Retinal), Peptides, Centella, Bio-Fermented Lipids',
                'benefits' => 'Acts 11x faster than traditional retinol; visibly lifts, smooths, and promotes cellular turnover.',
                'tag' => 'retinol',
            ],
            [
                'name' => 'SafeCell Gentle Micro-Dose Retinol Smoothing Serum',
                'subcat_index' => 4,
                'price' => 46.00,
                'actives' => '0.2% Micro-Dosed Retinol, Ceramides 1, 3, 6-II, Oat Kernel Extract',
                'benefits' => 'Daily gentle retinol serum for beginners and sensitive skin; builds skin resilience effortlessly.',
                'tag' => 'retinol',
            ],

            // Subcat 5: Multi-Peptide & Copper Tripeptide (Category 2)
            [
                'name' => 'SafeCell Multi-Peptide + Copper Tripeptide-1 1% Serum',
                'subcat_index' => 5,
                'price' => 78.00,
                'actives' => '1% Pure GHK-Cu Copper Tripeptide, Matrixyl 3000, Syn-Ake, Argireline',
                'benefits' => 'Vibrant natural blue serum that activates cellular collagen synthesis and dermal healing.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell Matrixyl 3000 + Argireline Firming Complex',
                'subcat_index' => 5,
                'price' => 62.00,
                'actives' => '8% Matrixyl 3000, 10% Argireline (Acetyl Hexapeptide-8), HA Matrix',
                'benefits' => 'Known as the topical peptide alternative to cosmetic line relaxers; smooths expression creases.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell Cellular Signal Hexapeptide Wrinkle Relaxer',
                'subcat_index' => 5,
                'price' => 66.00,
                'actives' => 'Hexapeptide-11, Palmitoyl Tripeptide-5, Hyaluronic Acid, Marine Collagen',
                'benefits' => 'Restores elasticity in sagging jawline, neck, and forehead areas for a visibly sculpted look.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell Bio-Placenta 5-Peptide Youth Restorative Serum',
                'subcat_index' => 5,
                'price' => 84.00,
                'actives' => '5 Recombinant Human Peptides (EGF, IGF-1, Acidic FGF, Basic FGF, VEGF), Vitamin B9',
                'benefits' => 'Intensive biomimetic cellular anti-aging elixir that rewinds cellular aging markers.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell Tri-Copper Elasticity Stimulating Elixir',
                'subcat_index' => 5,
                'price' => 72.00,
                'actives' => 'GHK-Cu Copper Peptide, Copper PCA, Tripeptide-29, Polyglutamic Acid',
                'benefits' => 'Rebuilds foundational extracellular matrix proteins to reverse loss of dermal volume.',
                'tag' => 'peptides',
            ],

            // Subcat 6: Epidermal Growth Factor (EGF) (Category 2)
            [
                'name' => 'SafeCell Bio-identical EGF Cellular Activation Serum',
                'subcat_index' => 6,
                'price' => 89.00,
                'actives' => 'Barley-Derived Bio-identical Epidermal Growth Factor (EGF), Pure Hyaluronic Acid',
                'benefits' => 'Groundbreaking biotechnology serum that accelerates epidermal skin cell regeneration by 70%.',
                'tag' => 'cellular',
            ],
            [
                'name' => 'SafeCell Plant Oligopeptide-1 Tissue Repair Serum',
                'subcat_index' => 6,
                'price' => 78.00,
                'actives' => 'Plant-Based Oligopeptide-1, Madecassoside, Squalane, Beta-Glucan',
                'benefits' => 'Rapidly restores compromised, post-procedure, laser, or chemical-peeled facial skin.',
                'tag' => 'cellular',
            ],
            [
                'name' => 'SafeCell Cellular Rebirth EGF + Centella Concentrate',
                'subcat_index' => 6,
                'price' => 82.00,
                'actives' => 'EGF Peptides, 75% Centella Asiatica Water, Asiaticoside, Panthenol',
                'benefits' => 'Reduces inflammation while stimulating healthy new tissue formation and collagen density.',
                'tag' => 'cellular',
            ],
            [
                'name' => 'SafeCell Post-Procedure EGF Recovery Drops',
                'subcat_index' => 6,
                'price' => 75.00,
                'actives' => 'Micro-Encapsulated EGF, Trehalose, Sodium Hyaluronate, Allantoin',
                'benefits' => 'Clinical recovery formula that shortens dermatological downtime and prevents scar formation.',
                'tag' => 'cellular',
            ],
            [
                'name' => 'SafeCell Intensive Dermal Density EGF Infusion',
                'subcat_index' => 6,
                'price' => 95.00,
                'actives' => 'Tri-Growth Factor Complex (EGF + FGF + KGF), Marine Seaweed, Amino Acids',
                'benefits' => 'Medical-grade cellular booster restoring deep dermal thickness in mature thinning skin.',
                'tag' => 'cellular',
            ],

            // Subcat 7: Collagen & Elasticity Boosters (Category 2)
            [
                'name' => 'SafeCell Vegan Collagen Peptides Volumizing Serum',
                'subcat_index' => 7,
                'price' => 55.00,
                'actives' => 'Plant-Derived Micro-Collagen, Tripeptide-1, Tremella Fuciformis Mushroom',
                'benefits' => 'Instantly plumps fine dehydration lines and reinforces structural dermal integrity.',
                'tag' => 'anti-aging',
            ],
            [
                'name' => 'SafeCell Pro-Collagen Marine Elasticity Elixir',
                'subcat_index' => 7,
                'price' => 64.00,
                'actives' => 'Hydrolyzed Marine Collagen, Padina Pavonica Brown Algae, Chlorella Extract',
                'benefits' => 'Rich in essential amino acids that dramatically improve skin snap-back elasticity.',
                'tag' => 'anti-aging',
            ],
            [
                'name' => 'SafeCell Phyto-Collagen Plumping Hydration Drops',
                'subcat_index' => 7,
                'price' => 52.00,
                'actives' => 'Soy & Wheat Amino Acids, Bamboo Isoflavones, Hyaluronic Acid',
                'benefits' => 'Delivers an instant cushiony softness and firm, dewy finish to tired, lackluster skin.',
                'tag' => 'anti-aging',
            ],
            [
                'name' => 'SafeCell Cellular Firmness Sculpting Concentrate',
                'subcat_index' => 7,
                'price' => 70.00,
                'actives' => 'Palmitoyl Tetrapeptide-7, DMAE, Alpha Lipoic Acid, Niacinamide',
                'benefits' => 'Clinically lifts slackened skin along cheekbones, smile lines, and jaw contour.',
                'tag' => 'anti-aging',
            ],
            [
                'name' => 'SafeCell Deep Dermal Collagen Reinforcement Serum',
                'subcat_index' => 7,
                'price' => 76.00,
                'actives' => 'Liposomal Type I & III Collagen Precursors, Vitamin C Ester, Coenzyme Q10',
                'benefits' => 'Fortifies collagen cross-linking to prevent future sagging and age-related volume loss.',
                'tag' => 'anti-aging',
            ],

            // Subcat 8: Multi-Molecular Hyaluronic Acid 2% (Category 3)
            [
                'name' => 'SafeCell 7D Multi-Molecular Hyaluronic Acid 2% + B5',
                'subcat_index' => 8,
                'price' => 39.00,
                'actives' => '7 Molecular Weights of Hyaluronic Acid, 2% Provitamin B5 (Panthenol), Polyglutamic Acid',
                'benefits' => 'Penetrates every stratum of the epidermis to hydrate from within; plumps fine dehydration lines.',
                'tag' => 'hyaluronic-acid',
            ],
            [
                'name' => 'SafeCell Ultra-Hydrating Hyaluronic Quench Serum',
                'subcat_index' => 8,
                'price' => 34.00,
                'actives' => 'Sodium Hyaluronate Crosspolymer, Hydrolyzed Hyaluronic Acid, Aloe Bio-Ferment',
                'benefits' => 'Lightweight aquatic serum that drenches thirsty cells with continuous 72-hour moisture.',
                'tag' => 'hyaluronic-acid',
            ],
            [
                'name' => 'SafeCell Sodium Hyaluronate High-Low Density Infusion',
                'subcat_index' => 8,
                'price' => 42.00,
                'actives' => 'Ultra-Low Molecular Weight HA (3kDa), High Molecular Weight HA (1800kDa), Glycerin',
                'benefits' => 'Forms a breathable protective moisture seal on surface while hydrating deep underlying layers.',
                'tag' => 'hyaluronic-acid',
            ],
            [
                'name' => 'SafeCell AquaSurge Deep Hydration Cellular Booster',
                'subcat_index' => 8,
                'price' => 45.00,
                'actives' => 'Hyaluronic Acid, Electrolyte Mineral Complex (Magnesium, Zinc, Copper), Niacinamide',
                'benefits' => 'Replenishes vital skin minerals and optimizes cellular osmotic water balance.',
                'tag' => 'hydration',
            ],
            [
                'name' => 'SafeCell Instant Dew Plumping Hyaluronic Complex',
                'subcat_index' => 8,
                'price' => 38.00,
                'actives' => 'Sodium Acetylated Hyaluronate, Snow Mushroom Extract, Betaine',
                'benefits' => 'Super-hyaluronic formulation that holds up to 1000x its weight in water for all-day dew.',
                'tag' => 'hydration',
            ],

            // Subcat 9: Ceramide Complex & Lipid Replenishing (Category 3)
            [
                'name' => 'SafeCell 5-Ceramide Biomimetic Barrier Repair Serum',
                'subcat_index' => 9,
                'price' => 52.00,
                'actives' => 'Ceramides EOP, NS, NP, AS, AP, Cholesterol, Free Fatty Acids (3:1:1 Ratio)',
                'benefits' => 'Recreates natural human skin barrier lipids to heal damaged, over-exfoliated, or compromised skin.',
                'tag' => 'ceramides',
            ],
            [
                'name' => 'SafeCell Ceramide NP + Cholesterol Lipid Serum',
                'subcat_index' => 9,
                'price' => 48.00,
                'actives' => 'Pure Ceramide NP 2%, Plant Cholesterol, Phytosphingosine, Squalane',
                'benefits' => 'Instantly stops trans-epidermal water loss (TEWL) and prevents external allergen penetration.',
                'tag' => 'ceramides',
            ],
            [
                'name' => 'SafeCell Skin Lipid Fortress Protective Milk Serum',
                'subcat_index' => 9,
                'price' => 46.00,
                'actives' => 'Ceramide Complex, Oat Seed Extract, Jojoba Esters, Centella Asiatica',
                'benefits' => 'Silky milky texture that calms tight, stinging, dry skin and restores comfortable resilience.',
                'tag' => 'skin-barrier',
            ],
            [
                'name' => 'SafeCell Phytosphingosine Damaged Barrier Therapy',
                'subcat_index' => 9,
                'price' => 56.00,
                'actives' => 'Phytosphingosine, Ceramide 3, Phospholipids, Allantoin, Panthenol',
                'benefits' => 'Clinical lipid treatment that repairs barrier damage caused by harsh weather, retinoids, or peels.',
                'tag' => 'skin-barrier',
            ],
            [
                'name' => 'SafeCell Intensive Comfort Ceramide Healing Elixir',
                'subcat_index' => 9,
                'price' => 50.00,
                'actives' => 'Multi-Ceramide Matrix, Ectoin, Squalane, Bisabolol, Vitamin F',
                'benefits' => 'Deeply restorative night lipid serum that locks in moisture and soothes rough flaky patches.',
                'tag' => 'ceramides',
            ],

            // Subcat 10: Centella Asiatica (Cica) & Soothing (Category 3)
            [
                'name' => 'SafeCell Pure Madecassoside 95% Calming Cica Serum',
                'subcat_index' => 10,
                'price' => 44.00,
                'actives' => '95% Pure Isolated Madecassoside, Asiaticoside, Asiatic Acid, Madecassic Acid',
                'benefits' => 'High-potency isolated cica actives that rapidly extinguish redness, irritation, and inflammation.',
                'tag' => 'cica',
            ],
            [
                'name' => 'SafeCell Centella Asiatica 80% Redness Relief Drops',
                'subcat_index' => 10,
                'price' => 38.00,
                'actives' => '80% Madagascar Centella Asiatica Extract, Niacinamide, Hyaluronic Acid',
                'benefits' => 'Cooling watery serum that diffuses facial redness, broken capillaries, and thermal warmth.',
                'tag' => 'cica',
            ],
            [
                'name' => 'SafeCell Soothing Green Tea + Cica Barrier Serum',
                'subcat_index' => 10,
                'price' => 36.00,
                'actives' => 'Jeju Green Tea Hydrosol, Cica Actives, Panthenol, Allantoin',
                'benefits' => 'Rich in epigallocatechin gallate (EGCG) antioxidants; protects sensitized skin from daily pollutants.',
                'tag' => 'cica',
            ],
            [
                'name' => 'SafeCell Hypoallergenic SOS Skin Recovery Fluid',
                'subcat_index' => 10,
                'price' => 46.00,
                'actives' => 'Cica Complex, Colloidal Oat, Ectoin, Licorice Root, Squalane',
                'benefits' => 'Emergency calming drops for acute dermatitis, sunburn, windburn, or allergic flare-ups.',
                'tag' => 'cica',
            ],
            [
                'name' => 'SafeCell Calming Biome Micro-Flora Balancing Serum',
                'subcat_index' => 10,
                'price' => 49.00,
                'actives' => 'Prebiotic Inulin, Bifida Ferment Lysate, Centella, Ceramide NP',
                'benefits' => 'Optimizes healthy facial microbiome balance to naturally resist breakouts and sensitivity.',
                'tag' => 'cica',
            ],

            // Subcat 11: Polyglutamic Acid Deep Moisture Lock (Category 3)
            [
                'name' => 'SafeCell Polyglutamic Acid 3% Mega-Hydrator Serum',
                'subcat_index' => 11,
                'price' => 42.00,
                'actives' => '3% Fermented Polyglutamic Acid (PGA), 2% Hyaluronic Acid, Marine Actives',
                'benefits' => 'Holds 4x more moisture than hyaluronic acid; creates a weightless water-reservoir on skin.',
                'tag' => 'hydration',
            ],
            [
                'name' => 'SafeCell Dual Action PGA + Beta Glucan Moisture Lock',
                'subcat_index' => 11,
                'price' => 45.00,
                'actives' => 'Polyglutamic Acid, Oat Beta-Glucan, Tremella Mushroom Extract',
                'benefits' => 'Penetrates deeply while sealing the epidermal surface against moisture evaporation.',
                'tag' => 'hydration',
            ],
            [
                'name' => 'SafeCell Hydro-Gel Matrix Intensive Drench Drops',
                'subcat_index' => 11,
                'price' => 40.00,
                'actives' => 'PGA, Sea Kelp Bio-Ferment, Sodium PCA, Betaine',
                'benefits' => 'Instantly transforms parched, tight, or tired skin into a dewy, supple canvas.',
                'tag' => 'hydration',
            ],
            [
                'name' => 'SafeCell Glacial Water & Moisture Surge Elixir',
                'subcat_index' => 11,
                'price' => 48.00,
                'actives' => 'Alaskan Glacial Mineral Water, Polyglutamic Acid, Deep Sea Algae',
                'benefits' => 'Infuses essential micro-elements into epidermal cells for vibrant, revitalized bounce.',
                'tag' => 'hydration',
            ],
            [
                'name' => 'SafeCell Long-Lasting 48H Hydration Barrier Shield',
                'subcat_index' => 11,
                'price' => 52.00,
                'actives' => 'PGA, Multi-Weight HA, Squalane, Saccharide Isomerate',
                'benefits' => 'Locks in moisture for up to 48 hours even in low-humidity or dry air-conditioned spaces.',
                'tag' => 'hydration',
            ],

            // Subcat 12: Salicylic Acid (BHA) 2% Pore Clearing (Category 4)
            [
                'name' => 'SafeCell Salicylic Acid 2% Deep Pore Decongesting Serum',
                'subcat_index' => 12,
                'price' => 36.00,
                'actives' => '2% Pharmaceutical Grade Salicylic Acid, Tea Tree Hydrosol, Zinc PCA',
                'benefits' => 'Oil-soluble BHA penetrates inside pores to dissolve sebum plugs, blackheads, and prevent acne.',
                'tag' => 'bha-salicylic',
            ],
            [
                'name' => 'SafeCell Micro-Exfoliating BHA Blackhead Defense Drops',
                'subcat_index' => 12,
                'price' => 38.00,
                'actives' => '2% Encapsulated Salicylic Acid, Niacinamide, Hyaluronic Acid',
                'benefits' => 'Time-released gentle pore clearing that prevents flaking while clearing stubborn blackheads.',
                'tag' => 'bha-salicylic',
            ],
            [
                'name' => 'SafeCell Clarifying Beta-Hydroxy Clearing Solution',
                'subcat_index' => 12,
                'price' => 40.00,
                'actives' => '2% Salicylic Acid, Willow Bark Bio-Actives, Witch Hazel, Allantoin',
                'benefits' => 'Clarifies congested T-zone areas and tightens the visible diameter of enlarged pores.',
                'tag' => 'bha-salicylic',
            ],
            [
                'name' => 'SafeCell Non-Drying BHA Gentle Blemish Control',
                'subcat_index' => 12,
                'price' => 35.00,
                'actives' => '1.5% Salicylic Acid, Centella Asiatica, Panthenol, Glycerin',
                'benefits' => 'Blemish-fighting serum designed for sensitive, easily irritated or dehydrated acne-prone skin.',
                'tag' => 'bha-salicylic',
            ],
            [
                'name' => 'SafeCell Salicylic + Willow Bark T-Zone Balancing Serum',
                'subcat_index' => 12,
                'price' => 39.00,
                'actives' => 'Salicylic Acid, White Willow Bark, Zinc Gluconate, Niacinamide',
                'benefits' => 'Balances hyperactive oil glands and maintains a clean, refined, velvety-smooth matte complexion.',
                'tag' => 'bha-salicylic',
            ],

            // Subcat 13: Azelaic Acid 10% Redness Calming (Category 4)
            [
                'name' => 'SafeCell Azelaic Acid 10% Redness & Rosacea Calming Serum',
                'subcat_index' => 13,
                'price' => 45.00,
                'actives' => '10% High-Purity Micronized Azelaic Acid, Centella Asiatica, Allantoin',
                'benefits' => 'Clinically approved active for calming persistent rosacea, facial erythema, and inflammatory bumps.',
                'tag' => 'spot-corrector',
            ],
            [
                'name' => 'SafeCell Micro-Milled Azelaic Clarifying Booster',
                'subcat_index' => 13,
                'price' => 42.00,
                'actives' => '10% Azelaic Acid, Niacinamide 2%, Licorice Extract, Squalane',
                'benefits' => 'Velvety serum that glides smoothly without pilling; clears textural roughness and dullness.',
                'tag' => 'spot-corrector',
            ],
            [
                'name' => 'SafeCell Post-Acne Mark Fading Azelaic Formulation',
                'subcat_index' => 13,
                'price' => 48.00,
                'actives' => '10% Azelaic Acid, 2% Tranexamic Acid, Alpha-Arbutin, HA',
                'benefits' => 'Targets red and brown post-inflammatory acne marks (PIE and PIH) for clean, clear skin.',
                'tag' => 'spot-corrector',
            ],
            [
                'name' => 'SafeCell Azelaic + Niacinamide Dual Tone Clarifier',
                'subcat_index' => 13,
                'price' => 46.00,
                'actives' => '10% Potassium Azeloyl Diglycinate, 5% Niacinamide, Zinc PCA',
                'benefits' => 'Water-soluble azelaic derivative that combines intense redness relief with potent oil control.',
                'tag' => 'spot-corrector',
            ],
            [
                'name' => 'SafeCell Soothing Azelaic Gentle Skin Corrector',
                'subcat_index' => 13,
                'price' => 44.00,
                'actives' => '8% Azelaic Acid, Colloidal Oatmeal, Bisabolol, Panthenol',
                'benefits' => 'Ultra-soothing formulation for hyper-reactive skin prone to redness, stinging, and breakouts.',
                'tag' => 'spot-corrector',
            ],

            // Subcat 14: Tea Tree & Zinc PCA Sebum Balancing (Category 4)
            [
                'name' => 'SafeCell Australian Tea Tree 5% + Zinc PCA Serum',
                'subcat_index' => 14,
                'price' => 34.00,
                'actives' => '5% Pure Terpinen-4-ol (Active Tea Tree), 1.5% Zinc PCA, Green Tea',
                'benefits' => 'Purifies blemishes overnight, inhibits acne-causing bacteria, and reduces skin shine.',
                'tag' => 'bha-salicylic',
            ],
            [
                'name' => 'SafeCell Botanical Anti-Blemish Purifying Drops',
                'subcat_index' => 14,
                'price' => 36.00,
                'actives' => 'Tea Tree Hydrosol, Rosemary Actives, Thyme Extract, Salicylic Acid',
                'benefits' => 'Botanical antimicrobial serum that keeps pores crystal clear and calms angry pimples.',
                'tag' => 'bha-salicylic',
            ],
            [
                'name' => 'SafeCell Sebum Control Zinc + Green Tea Clarifying Fluid',
                'subcat_index' => 14,
                'price' => 38.00,
                'actives' => '2% Zinc PCA, 80% Camellia Sinensis Leaf Water, Niacinamide',
                'benefits' => 'Light refreshing fluid that controls oily sheen all day without leaving skin stripped or tight.',
                'tag' => 'bha-salicylic',
            ],
            [
                'name' => 'SafeCell Rapid Spot Diminishing Botanical Concentrate',
                'subcat_index' => 14,
                'price' => 32.00,
                'actives' => 'Tea Tree Extract, Sulfur, Zinc Oxide, Centella, Witch Hazel',
                'benefits' => 'Targeted blemish concentrate that visibly shrinks active pimples in under 24 hours.',
                'tag' => 'bha-salicylic',
            ],
            [
                'name' => 'SafeCell Oil-Balancing Herbaceous Clarifying Serum',
                'subcat_index' => 14,
                'price' => 37.00,
                'actives' => 'Eucalyptus Leaf Extract, Zinc Lactate, Sage, Hyaluronic Acid',
                'benefits' => 'Maintains healthy pH balance and smooth velvety micro-texture in acne-prone combination skin.',
                'tag' => 'bha-salicylic',
            ],

            // Subcat 15: Gentle AHA+BHA Peeling Formulations (Category 4)
            [
                'name' => 'SafeCell AHA 10% + BHA 2% Weekly Cellular Peeling Elixir',
                'subcat_index' => 15,
                'price' => 48.00,
                'actives' => '10% AHA (Glycolic + Lactic), 2% BHA (Salicylic), Tasmanian Berry, Hyaluronic Acid',
                'benefits' => 'Vibrant crimson peeling serum that exfoliates multiple dermal layers for incredible skin glow.',
                'tag' => 'radiance',
            ],
            [
                'name' => 'SafeCell LHA + PHA Gentle Refining Solution',
                'subcat_index' => 15,
                'price' => 44.00,
                'actives' => 'Capryloyl Salicylic Acid (LHA), Gluconolactone (PHA), Centella Asiatica',
                'benefits' => 'Micro-peeling for sensitive and dry skin types; sweeps away dullness with zero irritation.',
                'tag' => 'radiance',
            ],
            [
                'name' => 'SafeCell Multi-Acid Radiance Resurfacing Fluid',
                'subcat_index' => 15,
                'price' => 52.00,
                'actives' => 'Glycolic, Salicylic, Tartaric, Citric & Mandelic Acids, Aloe Vera',
                'benefits' => 'Comprehensive acid cocktail unclogging pores, smoothing bumps, and brightening complexion.',
                'tag' => 'radiance',
            ],
            [
                'name' => 'SafeCell 5-Minute Flash Micro-Peel Active Drops',
                'subcat_index' => 15,
                'price' => 46.00,
                'actives' => 'Apple Fruit Enzymes, 5% Lactic Acid, 1% Salicylic Acid, Panthenol',
                'benefits' => 'Express flash peeling treatment for instant weekend radiance before major events or photos.',
                'tag' => 'radiance',
            ],
            [
                'name' => 'SafeCell Clarifying Fruit Acid Pore Renewal Serum',
                'subcat_index' => 15,
                'price' => 42.00,
                'actives' => 'Papaya & Pineapple Enzymes, Willow Bark, Glycolic Acid, Niacinamide',
                'benefits' => 'Enzyme-assisted acid exfoliator that dissolves stubborn dead cellular debris gently and evenly.',
                'tag' => 'radiance',
            ],

            // Subcat 16: Caffeine 5% + EGCG Eye Contour (Category 5)
            [
                'name' => 'SafeCell Caffeine Solution 5% + EGCG Eye Awakening Serum',
                'subcat_index' => 16,
                'price' => 38.00,
                'actives' => '5% Pure Micro-Circulation Caffeine, Epigallocatechin Gallatyl Glucoside (EGCG), HA',
                'benefits' => 'De-puffs under-eye fluid retention and diminishes dark shadow circles in minutes.',
                'tag' => 'anti-aging',
            ],
            [
                'name' => 'SafeCell Peptides + Caffeine Under-Eye Puffiness Defense',
                'subcat_index' => 16,
                'price' => 46.00,
                'actives' => 'Caffeine 5%, Acetyl Tetrapeptide-5, Matrixyl, Hyaluronic Acid',
                'benefits' => 'Dual action formulation tackling both morning eye bags and chronic structural dark circles.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell Dark Circle Brightening Eye Contour Fluid',
                'subcat_index' => 16,
                'price' => 44.00,
                'actives' => 'Caffeine, Chrysin, N-Hydroxysuccinimide, Vitamin K, Niacinamide',
                'benefits' => 'Clinically breaks down hemoglobin pigment leaks that cause purple and blue under-eye circles.',
                'tag' => 'radiance',
            ],
            [
                'name' => 'SafeCell Micro-Circulation Eye Bag Firming Serum',
                'subcat_index' => 16,
                'price' => 48.00,
                'actives' => 'Green Coffee Extract, Horse Chestnut (Aescin), Peptides, Squalane',
                'benefits' => 'Stimulates lymph drainage under the ocular contour to tighten saggy, swollen eye bags.',
                'tag' => 'anti-aging',
            ],
            [
                'name' => 'SafeCell Cooling Rollerball Caffeine Eye Elixir',
                'subcat_index' => 16,
                'price' => 42.00,
                'actives' => 'Caffeine, Cucumber Fruit Extract, Aloe Vera, Peptides, Panthenol',
                'benefits' => 'Stainless steel applicator that instantly cools by 3°C, awakening tired and strained eyes.',
                'tag' => 'anti-aging',
            ],

            // Subcat 17: Matrixyl 3000 Wrinkle Relaxer (Category 5)
            [
                'name' => 'SafeCell Matrixyl 3000 Concentrated Wrinkle Eraser',
                'subcat_index' => 17,
                'price' => 64.00,
                'actives' => '10% Matrixyl 3000 (Palmitoyl Tripeptide-1 + Palmitoyl Tetrapeptide-7), HA',
                'benefits' => 'Doubles collagen production and smooths crow’s feet, smile creases, and frown lines.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell Argireline Peptide 10% Forehead Line Relaxer',
                'subcat_index' => 17,
                'price' => 58.00,
                'actives' => '10% Argireline (Acetyl Hexapeptide-8), Marine Collagen, Hyaluronic Acid',
                'benefits' => 'Topical neuromodulating peptide that softens repetitive muscle contractions causing forehead furrows.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell Syn-Ake Viper Peptide Lifting Concentrate',
                'subcat_index' => 17,
                'price' => 72.00,
                'actives' => '4% Syn-Ake (Dipeptide Diaminobutyroyl Benzylamide Diacetate), Peptides',
                'benefits' => 'Mimics natural peptide smoothing to instantly relax stubborn tension lines around the eyes.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell High-Density Dermal Smoothing Serum',
                'subcat_index' => 17,
                'price' => 68.00,
                'actives' => 'Matrixyl Synthe 6, Palmitoyl Tripeptide-38, Hyaluronic Acid, Squalane',
                'benefits' => 'Fills in wrinkle depth from within by rebuilding 6 major constituents of skin matrix.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell Clinical Anti-Crease Targeted Eye & Neck Serum',
                'subcat_index' => 17,
                'price' => 65.00,
                'actives' => 'Matrixyl 3000, Copper Peptides, Edelweiss Meristem Stem Cells, Niacinamide',
                'benefits' => 'Targets delicate neck rings and crepey chest and eye skin for taut, smooth firmness.',
                'tag' => 'peptides',
            ],

            // Subcat 18: Natural Bakuchiol Retinol Alternative (Category 5)
            [
                'name' => 'SafeCell 2% Natural Bakuchiol Rejuvenating Serum',
                'subcat_index' => 18,
                'price' => 56.00,
                'actives' => '2% Pure Plant-Derived Bakuchiol, Squalane, Rosehip Seed Oil, Vitamin E',
                'benefits' => 'The gentle 100% natural retinol alternative; stimulates collagen without photo-sensitivity or irritation.',
                'tag' => 'clean-beauty',
            ],
            [
                'name' => 'SafeCell Plant-Based Retinol Alternative Night Oil-Serum',
                'subcat_index' => 18,
                'price' => 60.00,
                'actives' => '2% Bakuchiol, Cacay Oil, Sea Buckthorn, Meadowfoam Seed Oil',
                'benefits' => 'Luxurious botanical night elixir providing cellular renewal for ultra-sensitive skin.',
                'tag' => 'clean-beauty',
            ],
            [
                'name' => 'SafeCell Squalane + Bakuchiol Sensitive Age Defense',
                'subcat_index' => 18,
                'price' => 54.00,
                'actives' => 'Bakuchiol, 100% Olive Squalane, Bisabolol, Ceramides',
                'benefits' => 'Lightweight hydrating facial oil-serum that deeply replenishes moisture and smooths lines.',
                'tag' => 'clean-beauty',
            ],
            [
                'name' => 'SafeCell Pregnancy-Safe Botanical Wrinkle Smoothing Drops',
                'subcat_index' => 18,
                'price' => 58.00,
                'actives' => 'Pure Bakuchiol, Blue Tansy Oil, Jojoba, Vitamin C Ester',
                'benefits' => 'Safe for pregnancy and nursing; delivers retinoid-like results without systemic contraindications.',
                'tag' => 'clean-beauty',
            ],
            [
                'name' => 'SafeCell Silky Glow Bakuchiol Age Reset Fluid',
                'subcat_index' => 18,
                'price' => 52.00,
                'actives' => 'Bakuchiol, Niacinamide, Snow Mushroom, Centella Asiatica',
                'benefits' => 'Daytime safe antioxidant and smoothing fluid that gives a glass-like finish under makeup.',
                'tag' => 'clean-beauty',
            ],

            // Subcat 19: Multi-Peptide Scalp & Hair Density (Category 5)
            [
                'name' => 'SafeCell Multi-Peptide Hair & Scalp Density Serum',
                'subcat_index' => 19,
                'price' => 49.00,
                'actives' => 'Redensyl, Procapil, Capixyl, Baicapil, Caffeine, L-Arginine',
                'benefits' => 'High-potency scalp peptide treatment that stimulates stem cells for thicker, fuller, denser hair.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell Biotin + Caffeine Follicle Stimulating Serum',
                'subcat_index' => 19,
                'price' => 42.00,
                'actives' => 'Biotin (Vitamin B7), 3% Caffeine, Saw Palmetto Extract, Niacinamide',
                'benefits' => 'Blocks scalp DHT binding while energizing dormant follicles to extend the anagen growth cycle.',
                'tag' => 'cellular',
            ],
            [
                'name' => 'SafeCell Redensyl 3% + Procapil Hair Rejuvenation Drops',
                'subcat_index' => 19,
                'price' => 55.00,
                'actives' => '3% Redensyl, 2% Procapil, Pea Sprout Extract, Zinc, Glycine',
                'benefits' => 'Clinical hair density booster showing up to 28,000 new hairs in independent 84-day trials.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell Copper Tripeptide Scalp Vitality Concentrate',
                'subcat_index' => 19,
                'price' => 58.00,
                'actives' => '1% GHK-Cu Copper Tripeptide, Rosemary Oil, Hyaluronic Acid, Panthenol',
                'benefits' => 'Enlarges hair follicle size and revitalizes scalp cellular micro-circulation.',
                'tag' => 'peptides',
            ],
            [
                'name' => 'SafeCell Botanical Root-Strengthening Cellular Scalp Elixir',
                'subcat_index' => 19,
                'price' => 46.00,
                'actives' => 'Rosemary Leaf Hydrosol, Peppermint Actives, Biotinoyl Tripeptide-1, Keratin',
                'benefits' => 'Non-greasy, water-based daily leave-in scalp serum for fortifying thinning roots.',
                'tag' => 'clean-beauty',
            ],
        ];
    }
}

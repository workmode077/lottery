<?php
namespace App\Traits;

use App\Models\BannerAndMetaTag;

trait MetaDataTrait
{
    /**
     * Get meta data in same style as getHomeBannerData()
     *
     * @param string $language Language code (default: 'en')
     * @return array
     */
    protected function getMetaData($language,$page = 'home')
    {
        $seo = BannerAndMetaTag::where('page', $page)->first();

        // Return default values if no SEO record found
        if (!$seo) {
            return $this->getDefaultMetaData($language);
        }

        return [
            'last_updated' => optional($seo->updated_at)->toIso8601String() ?? now()->toIso8601String(),
            'seo' => [
                'title' => $this->getLocalizedText(
                    $seo,
                    'meta_title',
                    $this->getDefaultMetaTitle($language),
                    $language
                ),
                'description' => $this->getLocalizedText(
                    $seo,
                    'meta_description',
                    $this->getDefaultMetaDescription($language),
                    $language
                ),
                'keywords' => $this->getLocalizedText(
                    $seo,
                    'meta_keywords',
                    $this->getDefaultMetaKeywords($language),
                    $language
                ),
                 'other_meta_tags' => $seo->other_meta_tags ?? '',
                'og_title' => $this->getLocalizedText(
                    $seo,
                    'meta_title',
                    $this->getDefaultOgTitle($language),
                    $language
                ),
                'og_description' => $this->getLocalizedText(
                    $seo,
                    'meta_description',
                    $this->getDefaultOgDescription($language),
                    $language
                ),
                'og_image' => $seo->og_image ?? $this->getDefaultOgImage(),
                'canonical_url' => $seo->canonical_url ?? $this->getDefaultCanonicalUrl(),
            ]
        ];
    }

    /**
     * Get default meta data structure
     */
    protected function getDefaultMetaData($language = 'en')
    {
        return [
            'last_updated' => now()->toIso8601String(),
            'seo' => [
                'title' => $this->getDefaultMetaTitle($language),
                'description' => $this->getDefaultMetaDescription($language),
                'keywords' => $this->getDefaultMetaKeywords($language),
                'og_title' => $this->getDefaultOgTitle($language),
                'og_description' => $this->getDefaultOgDescription($language),
                'og_image' => $this->getDefaultOgImage(),
                'canonical_url' => $this->getDefaultCanonicalUrl(),
            ]
        ];
    }

    // Default value methods
    protected function getDefaultMetaTitle($language)
    {
        return $language === 'ar'
            ? 'عنوان افتراضي'
            : 'Default Title';
    }

    protected function getDefaultMetaDescription($language)
    {
        return $language === 'ar'
            ? 'وصف افتراضي'
            : 'Default meta description';
    }

    protected function getDefaultMetaKeywords($language)
    {
        return $language === 'ar'
            ? 'كلمات, مفتاحية, افتراضية'
            : 'default, meta, keywords';
    }

    protected function getDefaultOgTitle($language)
    {
        return $language === 'ar'
            ? 'عنوان OG افتراضي'
            : 'Default OG Title';
    }

    protected function getDefaultOgDescription($language)
    {
        return $language === 'ar'
            ? 'وصف OG افتراضي'
            : 'Default OG Description';
    }

    protected function getDefaultOgImage()
    {
        return asset('backend/images/yangwang-logo.png');
    }

    protected function getDefaultCanonicalUrl()
    {
        return url('/');
    }
}

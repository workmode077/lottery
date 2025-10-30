<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sitemap = Sitemap::create();

        $this->addStaticRoutes($sitemap);

        // Blog::query()->get()->each(function (Blog $blog) use ($sitemap) {
        //     $sitemap->add(
        //         Url::create(route('blog.detail', ['slug' => $blog->slug]))
        //             ->setPriority(0.8)
        //             ->setLastModificationDate(now())
        //     );
        // });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully.');
    }

    /**
     * Add static routes to the sitemap.
     *
     * @param Sitemap $sitemap
     * @return void
     */
    protected function addStaticRoutes(Sitemap $sitemap)
    {
        collect([
            'home',
            'about',
            'contact',
        ])->each(function ($route) use ($sitemap) {
            $sitemap->add(
                Url::create(route($route))
                    ->setPriority(1)
                    ->setLastModificationDate(now())
            );
        });
    }
}

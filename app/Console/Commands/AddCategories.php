<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use App\Models\Category;



class AddCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add updated Categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch all unique categories from the articles table
        $uniqueCategories = Article::query()
            ->distinct()
            ->pluck('category'); // Assuming 'category' is the column name

        $this->info('Found ' . $uniqueCategories->count() . ' unique categories.');

        // Iterate through each unique category and insert it into the categories table
        $uniqueCategories->each(function ($categoryName) {
            if ($categoryName) {
                // Use `firstOrCreate` to avoid duplicates
                Category::firstOrCreate(
                    ['name' => $categoryName],
                );
            }
        });

        $this->info('Categories added successfully.');

    }
}

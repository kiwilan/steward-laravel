<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TagCleanCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag:clean
                            {pivot : Pivot table name.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean taggables pivot table.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();
        $taggables = $this->argument('pivot') ?: 'taggables';

        $taggables = DB::table($taggables)->get();

        foreach ($taggables as $row) {
            $model = $row->taggable_type::find($row->taggable_id);

            if ($model === null) {
                DB::table($taggables)->where('id', $row->id)->delete();
                $this->warn("Deleted taggable entry: {$row->taggable_type} #{$row->taggable_id}");
            }

            $tag = DB::table('tags')->find($row->tag_id);

            if ($tag === null) {
                DB::table($taggables)->where('id', $row->id)->delete();
                $this->warn("Deleted taggable entry: Tag #{$row->id}");
            }
        }

        return Command::SUCCESS;
    }
}

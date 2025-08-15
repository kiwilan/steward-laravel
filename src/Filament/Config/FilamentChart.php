<?php

namespace Kiwilan\Steward\Filament\Config;

use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Kiwilan\Steward\Enums\PublishStatusEnum;
use Kiwilan\Steward\Filament\Config\FilamentChart\ChartByDay;
use Kiwilan\Steward\Filament\Config\FilamentChart\ChartByMonth;

class FilamentChart
{
    /** @var string[] */
    public array $labels = [];

    /** @var int[] */
    public array $stats = [];

    public function __construct(
    ) {}

    public static function chartBy(string $table, string $field, ?string $limit_year = null, bool $published = false): FilamentChart
    {
        $is_sqlite = config('database.default') === 'sqlite';
        if ($is_sqlite) {
            $models_db = DB::table($table)
                ->selectRaw("
                count(id) as total,
                strftime('%b %Y', {$field}) as year
            ");
        } else {
            $models_db = DB::table($table)
                ->selectRaw("
                count(id) as total,
                date_format({$field}, '%Y') as year
            ");
        }

        if ($published) {
            $models_db = $models_db->where('status', '=', PublishStatusEnum::published->value);
        }

        if ($limit_year) {
            $models_db = $models_db->whereYear($field, '>=', $limit_year);
        }

        $models_db = $models_db->groupBy('year')
            ->get()
            ->keyBy('year');

        $models_db = $models_db->toArray();
        ksort($models_db);

        $stats = [];
        $labels = [];

        foreach ($models_db as $year => $stat) {
            array_push($labels, $year);
            array_push($stats, $stat->total);
        }

        $chart_helper = new FilamentChart;
        $chart_helper->labels = $labels;
        $chart_helper->stats = $stats;

        return $chart_helper;
    }

    public static function chartByField(string $table, string $field, ?string $limit_year = null, bool $published = false): self
    {
        $models_db = DB::table($table)
            ->selectRaw("
                count(id) as total,
                {$field} as year
            ");

        if ($published) {
            $models_db = $models_db->where('status', '=', PublishStatusEnum::published->value);
        }

        if ($limit_year) {
            $models_db = $models_db->whereYear($field, '>=', $limit_year);
        }

        $models_db = $models_db->groupBy('year')
            ->get()
            ->keyBy('year');

        $models_db = $models_db->toArray();
        ksort($models_db);

        $stats = [];
        $labels = [];

        foreach ($models_db as $year => $stat) {
            array_push($labels, $year);
            array_push($stats, $stat->total);
        }

        $chart_helper = new FilamentChart;
        $chart_helper->labels = $labels;
        $chart_helper->stats = $stats;

        return $chart_helper;
    }

    /**
     * Create a new chart by month on one year.
     *
     * @param  string  $table  The table name
     */
    public static function statsByMonth(string $table): ChartByMonth
    {
        return ChartByMonth::make($table);
    }

    /**
     * Create a new chart by day on one week.
     *
     * @param  string  $table  The table name
     */
    public static function statsByDay(string $table): ChartByDay
    {
        return ChartByDay::make($table);
    }

    public static function getStatsByYear()
    {
        $presentation_years = DB::table('references')
            ->selectRaw('
                presentation_year
            ')
            ->get()
            ->map(fn ($row) => $row->presentation_year)
            ->sort();
        $current_year = date('Y');
        $limit_year = $current_year - 20;

        $models_db = DB::table('references')
            ->selectRaw('
                count(id) as total,
                presentation_year as period
            ')
            ->where('status', '=', PublishStatusEnum::published->value)
            // ->whereYear('published_at', '=', $year)
            ->groupBy('period')
            ->get()
            ->keyBy('period');

        $periods = collect([]);

        foreach (CarbonPeriod::create("{$limit_year}-01-01", '1 year', now()->subYear().'-12-01') as $period) {
            $periods->push($period->format('Y'));
        }

        $res = $periods->map(function ($period) use ($models_db) {
            return $models_db->get($period)->total ?? 0;
        });

        $chart_helper = new FilamentChart;
        $chart_helper->labels = $periods->toArray();
        $chart_helper->stats = $res->toArray();

        return $chart_helper;
        // $stats = Cache::remember(
        //     'statsByMonth',
        //     // Clears cache at the start of next month
        //     now()->addMonth()->startOfMonth()->startOfDay(),
        //     fn () => $this->getStatsByMonth()
        // );
    }
}

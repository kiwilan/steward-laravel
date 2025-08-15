<?php

namespace Kiwilan\Steward\Filament\Config\FilamentChart;

use Carbon\CarbonPeriod;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Docs: https://genijaho.dev/blog/generate-monthly-chart-data-with-eloquent-carbon.
 */
class ChartByDay
{
    /**
     * @param  array{column: string, operator: ?string, value: ?string}[]  $where
     */
    protected function __construct(
        protected string $table,
        protected string $field = 'created_at',
        protected bool $autoYear = false,
        protected ?int $year = null,
        protected string $label = '',
        protected array $where = [],
        protected ?string $id = null,
        protected bool $withCache = false,
        protected ?Collection $data = null,
        protected ?int $startMonth = null,
        protected ?int $endMonth = null,
    ) {}

    /**
     * Create a new chart instance.
     *
     * @param  string  $table  The table name
     */
    public static function make(string $table): self
    {
        return new self($table);
    }

    /**
     * Set the label for the chart.
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the year for the chart, defaults to current year.
     */
    public function year(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Set the field for the chart, defaults to `created_at`.
     */
    public function field(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Set the cache for the chart.
     */
    public function withCache(): self
    {
        $this->withCache = true;

        return $this;
    }

    /**
     * Set the where clause for the query.
     *
     * @param  array{column: string, operator: ?string, value: ?string}[]  $where
     */
    public function where(array $where): self
    {
        $this->where = $where;

        return $this;
    }

    /**
     * Get the chart data.
     */
    public function get()
    {
        $this->id = uniqid();
        $this->id = "statsByMonth_{$this->id}";

        if (! $this->year || $this->year === now()->year) {
            $this->autoYear = true;
            $this->year = now()->year;
        }

        if ($this->autoYear) {
            $currentMonth = now()->month;
            $this->startMonth = $currentMonth - 11;
            $this->endMonth = $currentMonth;
        } else {
            $this->startMonth = 1;
            $this->endMonth = 12;
        }

        $this->data = $this->setData();

        return [
            'datasets' => [
                [
                    'label' => $this->label,
                    'data' => $this->data,
                    'fill' => 'start',
                ],
            ],
            'labels' => $this->generateMonthLabels(),
        ];
    }

    /**
     * Get the chart data.
     */
    private function setData(): Collection
    {
        if ($this->withCache) {
            $cache = Cache::get($this->id);

            if ($cache) {
                return $cache;
            }
        }

        $is_sqlite = config('database.default') === 'sqlite';
        if ($is_sqlite) {
            // date_format('.$this->field.", '%b %Y') as period
            // strftime("%b %Y", '.$this->field.') as period
            $query = DB::table($this->table)
                ->selectRaw('
                count(id) as total,
                strftime("%b %Y", '.$this->field.') as period
            ');
        } else {
            $query = DB::table($this->table)
                ->selectRaw('
                count(id) as total,
                date_format('.$this->field.", '%b %Y') as period
            ");
        }

        if (! empty($this->where)) {
            foreach ($this->where as $value) {
                $column = $value['column'];
                $operator = $value['operator'] ?? '=';
                $value = $value['value'] ?? null;
                $query = $query->where($column, $operator, $value);
            }
        }

        // Get data only for the last 11 months and the current month
        $query = $query->where(function ($query) {
            $query->whereYear($this->field, '=', $this->autoYear ? $this->year - 1 : $this->year)
                ->orWhere(function ($query) {
                    $query->whereYear($this->field, '=', $this->year)
                        ->whereMonth($this->field, '>=', $this->startMonth)
                        ->whereMonth($this->field, '<=', $this->endMonth);
                });
        });

        $group = $query->groupBy('period');
        $data = $group->get()->keyBy('period');

        $periods = collect([]);
        $this->carbonPeriod(fn ($period) => $periods->push($period->format('M Y')));
        $map = $periods->map(fn ($period) => $data->get($period)->total ?? 0);

        if ($this->withCache) {
            Cache::remember(
                $this->id,
                // Clears cache at the start of next month
                now()->addMonth()->startOfMonth()->startOfDay(),
                fn () => $map,
            );
        }

        return $map;
    }

    /**
     * Get the chart data.
     *
     * @param  Closure(\Carbon\CarbonInterface|null $period): void  $closure
     */
    private function carbonPeriod(Closure $closure): void
    {
        if ($this->autoYear) {
            $paramStart = now()->subMonths(12)->startOfMonth();
            $paramEnd = now()->startOfMonth();
        } else {
            $paramStart = "{$this->year}-01-01";
            $paramEnd = "{$this->year}-12-01";
        }

        foreach (CarbonPeriod::create($paramStart, '1 month', $paramEnd) as $period) {
            $closure($period);
        }
    }

    /**
     * Generate month labels based on the specified range.
     *
     * @return string[]
     */
    private function generateMonthLabels(): array
    {
        $months = collect([]);

        $this->carbonPeriod(fn ($period) => $months->push($period->format('M Y')));

        return $months->toArray();
    }
}

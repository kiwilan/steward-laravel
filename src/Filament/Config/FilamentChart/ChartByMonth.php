<?php

namespace Kiwilan\Steward\Filament\Config\FilamentChart;

use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Docs: https://genijaho.dev/blog/generate-monthly-chart-data-with-eloquent-carbon.
 */
class ChartByMonth
{
    /**
     * @param  array{column: string, operator: string, value: string}[]  $where
     */
    protected function __construct(
        protected string $table,
        protected string $field = 'created_at',
        protected ?int $year = null,
        protected string $label = '',
        protected array $where = [],
        protected ?Collection $data = null,
    ) {
    }

    /**
     * Create a new chart instance.
     *
     * @param  string  $table The table name
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
     * Set the where clause for the query.
     *
     * @param  array{column: string, operator: string, value: string}[]  $where
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
        if (! $this->year) {
            $this->year = now()->year;
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
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    /**
     * Get the chart data.
     */
    private function setData(): Collection
    {
        $query = DB::table($this->table)
            ->selectRaw('
                count(id) as total,
                date_format('.$this->field.", '%b %Y') as period
            ")
        ;

        if (! empty($this->where)) {
            foreach ($this->where as $value) {
                $column = $value['column'];
                $operator = $value['operator'] ?? '=';
                $value = $value['value'] ?? null;
                $query = $query->where($column, $operator, $value);
            }
        }

        $query = $query->whereYear($this->field, '=', $this->year);
        $group = $query->groupBy('period');
        $data = $group->get()->keyBy('period');

        $periods = collect([]);

        foreach (CarbonPeriod::create("{$this->year}-01-01", '1 month', "{$this->year}-12-01") as $period) {
            $periods->push($period->format('M Y'));
        }

        // $stats = Cache::remember(
        //     'statsByMonth',
        //     // Clears cache at the start of next month
        //     now()->addMonth()->startOfMonth()->startOfDay(),
        //     fn () => $this->getStatsByMonth()
        // );

        return $periods->map(fn ($period) => $data->get($period)->total ?? 0);
    }
}

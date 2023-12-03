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
        protected string $field,
        protected int $year,
        protected string $label = '',
        protected array $where = [],
        protected ?Collection $data = null,
    ) {
    }

    /**
     * Create a new chart instance.
     *
     * @param  string  $table The table name
     * @param  string  $field The date field - defaults to `created_at`
     * @param  int  $year The year to get stats for - defaults to current year
     */
    public static function make(string $table, string $field = 'created_at', int $year = null): self
    {
        if (! $year) {
            $year = now()->year;
        }

        return new self($table, $field, $year);
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

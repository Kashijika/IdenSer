<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'symbol',
        'price',
        'volume',
        'change_percent',
        'high',
        'low',
        'open',
        'close',
        'trade_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:4',
        'change_percent' => 'decimal:2',
        'high' => 'decimal:4',
        'low' => 'decimal:4',
        'open' => 'decimal:4',
        'close' => 'decimal:4',
        'trade_date' => 'datetime',
    ];

    /**
     * Get trading data for chart display.
     */
    public static function getChartData($symbol = null, $days = 30)
    {
        $query = self::query()
            ->where('trade_date', '>=', now()->subDays($days))
            ->orderBy('trade_date', 'asc');

        if ($symbol) {
            $query->where('symbol', $symbol);
        }

        return $query->get();
    }

    /**
     * Get limited trading data for employees (anonymized).
     */
    public static function getLimitedData($days = 7)
    {
        return self::query()
            ->select('symbol', 'price', 'change_percent', 'trade_date')
            ->where('trade_date', '>=', now()->subDays($days))
            ->whereIn('symbol', ['AAPL', 'GOOGL', 'MSFT']) // Limited symbols
            ->orderBy('trade_date', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get market summary.
     */
    public static function getMarketSummary()
    {
        $latest = self::query()
            ->selectRaw('symbol, price, change_percent, volume, MAX(trade_date) as latest_date')
            ->groupBy('symbol', 'price', 'change_percent', 'volume')
            ->orderBy('volume', 'desc')
            ->limit(8)
            ->get();

        return $latest;
    }

    /**
     * Scope for specific symbol.
     */
    public function scopeSymbol($query, $symbol)
    {
        return $query->where('symbol', $symbol);
    }

    /**
     * Scope for recent data.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('trade_date', '>=', now()->subDays($days));
    }
}
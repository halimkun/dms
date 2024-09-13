<?php

namespace App\Http\Controllers\Collection;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PenjabController;
use App\Http\Controllers\RegPeriksaController;
use App\Models\RegPeriksa;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class RegPeriksaCollection extends Controller
{

    protected $regPeriksaController;
    protected $dokterCollection;
    protected $penjab;

    public function __construct()
    {
        $this->regPeriksaController = new RegPeriksaController();
        $this->dokterCollection = new DokterCollection();
        $this->penjab = new PenjabController();
    }

    function getAll(Request $request)
    {
        return collect($this->regPeriksaController->getAll($request))->where('stts', 'Sudah');
    }

    function getRegByStatusLanjut(Request $request) : array
    {
        $regCollection = $this->getAll($request);
        $kunjungan = $regCollection->groupBy('status_lanjut')->mapWithKeys(function ($item, $key) {
            return [$key => $item->count()];
        })->toArray();
        $totalCount = array_sum($kunjungan);
        $igd = $regCollection->where('kd_poli', 'IGDK')->count();
        return array_merge($kunjungan, ['Total' => $totalCount, 'UGD' => $igd]);
    }

    public function getByYear($year = ''): Collection
    {
        $penjab = $this->penjab->getAllPenjab();
        return $this->regPeriksaController->getByYear($year)->where('stts', 'Sudah')
            ->groupBy('status_lanjut')->map((function ($item) use ($penjab) {
                return $item->groupBy(function ($item) {
                    return Carbon::parse($item->tgl_registrasi)->format('m');
                })->mapWithKeys(function ($item, $key) use ($penjab) {
                    $keysMonth = Carbon::parse($item->first()->tgl_registrasi)->translatedFormat('F');
                    return [
                        $keysMonth => $item->groupBy('kd_pj')->map(function ($items, $keys) use ($penjab) {
                            $pngJawab = $penjab;
                            return [
                                'jumlah' => $items->count(),
                                'penjab' => $pngJawab->where('kd_pj', $keys)->first()->png_jawab,
                                'kd_pj' => $pngJawab->where('kd_pj', $keys)->first()->kd_pj,
                            ];
                        })->values()
                    ];
                });
            }));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DiagnosaPasien;
use App\Models\RegPeriksa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DiagnosaPasienController extends Controller
{
	public function index()
	{
		$date = new Carbon('this month');
		return view(
			'dashboard.content.rekammedis.list_diagnosa',
			[
				'title' => 'Data Rekam Medis',
				'bigTitle' => 'Rekam Medis',
				'month' => $date->startOfMonth()->translatedFormat('d F Y') . ' s/d ' . $date->now()->translatedFormat('d F Y'),
				'dateStart' => $date->startOfMonth()->toDateString(),
				'dateNow' => $date->now()->toDateString(),
			]
		);
	}

	public function json(Request $request)
	{

		$data = '';
		$start = new Carbon('this month');
		if ($request->ajax()) {
			$data = DiagnosaPasien::select('*', DB::raw('count(kd_penyakit) as jumlah'))
				->where('prioritas', 1)
				->where('status', 'like', '%' . $request->status . '%')
				->where('kd_penyakit', 'not like', 'r%')
				->where('kd_penyakit', 'not like', 'z%')
				->whereNotIn('kd_penyakit', ['O80', 'O82'])
				->groupBy('kd_penyakit')
				->orderBy('jumlah', 'desc')
				->limit(10);
			if ($request->tgl_pertama || $request->tgl_kedua) {
				$data = $data->whereHas('regPeriksa', function ($query) use ($request) {
					$query->whereBetween('tgl_registrasi', [$request->tgl_pertama, $request->tgl_kedua]);
				})
					->whereHas('regPeriksa.dokter.spesialis', function ($query) use ($request) {
						$query->where('nm_sps', 'like', '%' . $request->spesialis . '%');
					})
					->whereHas('regPeriksa.penjab', function ($query) use ($request) {
						$query->where('png_jawab', 'like', '%' . $request->pembiayaan . '%');
					})->get();
			} else {
				$data = $data
					->whereHas('regPeriksa', function ($query) use ($start) {
						$query->whereBetween('tgl_registrasi', [$start->startOfMonth()->toDateString(), $start->now()->toDateString()]);
					})
					->get();
			}
		}
		return DataTables::of($data)
			->editColumn('nm_penyakit', function ($data) {
				return $data->penyakit->nm_penyakit;
			})
			->editColumn('pembiayaan', function ($data) use ($request) {
				if ($request->pembiayaan) {
					return $data->regPeriksa->penjab->png_jawab;
				} else {
					return 'Semua Pembiayaan';
				}
			})
			->editColumn('status', function ($data) use ($request) {
				if ($request->status) {
					return ($data->status == 'Ralan' ? 'Rawat Jalan' : 'Rawat Inap');
				} else {
					return 'Ralan & Ranap';
				}
			})
			->make(true);
	}

	public function pasienTb()
	{
		$tanggal = new Carbon('this month');
		return view(
			'dashboard.content.rekammedis.list_pasien_tb',
			[
				'title' => 'Data Pasien TB',
				'bigTitle' => 'Pasien TB',
				'month' => $tanggal->startOfMonth()->translatedFormat('d F Y') . ' s/d ' . $tanggal->now()->translatedFormat('d F Y'),
				'dateStart' => $tanggal->startOfMonth()->toDateString(),
				'dateNow' => $tanggal->now()->toDateString(),
			]
		);
	}

	public function jsonPasienTb(Request $request)
	{
		$regPeriksa = new RegPeriksa();

		$query = $regPeriksa->select('*')
			->whereHas('diagnosa', function ($query) {
				$query->whereHas('penyakit', function ($q) {
					$q->where('nm_penyakit', 'like', '%Tuberculosis%');
				});
			})
			->groupBy('no_rkm_medis')
			->orderBy('tgl_registrasi', 'ASC');

		if($request->year){
			$data = $query->year($request->year)->get();
		}

		if($request->month){
			$data = $query->month($request->month, $request->year)->get();
		}



		if(!$request->year && !$request->month){
			$data = $query->month(date('m'), date('Y'))->get();
		}

		return DataTables::of($data)
			->editColumn('tgl_registrasi', function ($data){
				return $data->tgl_registrasi;
			})
			->editColumn('nm_pasien', function ($data) {
				return $data->pasien->nm_pasien;
			})
			->editColumn('tgl_lahir', function ($data) {
				return Carbon::parse($data->pasien->tgl_lahir)->translatedFormat('d F Y');
			})
			->editColumn('no_ktp', function ($data) {
				return $data->pasien->no_ktp;
			})
			->editColumn('no_tlp', function ($data) {
				return $data->pasien->no_tlp;
			})
			->editColumn('umurdaftar', function ($data) {
				return $data->umurdaftar . ' ' . $data->sttsumur;
			})
			->editColumn('kd_penyakit', function ($data) {
				return $data->diagnosaWithoutZ->map(function ($item) {
					return $item->penyakit->kd_penyakit;
				})->implode(';<br/> ');
			})
			->editColumn('nm_penyakit', function ($data) {
//				return $data->diagnosaTb;
				return $data->diagnosaWithoutZ->map(function ($item) {
					return $item->penyakit->nm_penyakit;
				})->implode(';<br/>');
			})
			->addColumn('status', function ($data) {
				return $data->diagnosa->map(function ($item) {
					return $item->penyakit;
				})->filter(function ($item) {
					return str_contains($item->kd_penyakit, 'Z') === true;
				})->count() == 0 ? 'Baru' : 'Kontrol';
			})
			->editColumn('alamat', function ($data) {
				return $data->pasien->alamat . ", "
					. $data->pasien->kelurahan->nm_kel . ", "
					. $data->pasien->kecamatan->nm_kec . ", "
					. $data->pasien->kabupaten->nm_kab;
			})
			->editColumn('nm_poli', function ($data) {
				return $data->poli->nm_poli;
			})
			->editColumn('jk', function ($data) {
				return ($data->pasien->jk == 'L' ? 'Laki-Laki' : 'Perempuan');
			})
			->rawColumns(['nm_penyakit', 'kd_penyakit'])
			->make(true);
	}
}

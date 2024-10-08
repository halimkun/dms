@extends('dashboard.layouts.main')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-teal">
                <div class="card-header">
                    <p class="card-title border-bottom-0">{{ $title }} </p>
                    <div class="card-tools mr-4" id="bulan">
                        <span><strong>{{ $month }}</strong></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive text-sm">
                                <table class="table table-bordered table-striped table-hover table-sm"
                                       id="table-pasien-tb" style="width: 100%" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th>Tgl. Registrasi</th>
                                        <th>No. Rawat</th>
                                        <th>No. RM</th>
                                        <th>Nama</th>
                                        <th>Tgl. Lahir</th>
                                        <th>Umur</th>
                                        <th>NIK</th>
                                        <th>JK</th>
                                        <th>Alamat</th>
                                        <th>No. Telp</th>
                                        <th>ICD-10</th>
                                        <th>Penyakit</th>
                                        <th>Status TB</th>
                                        <th>Poli</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radioBlnPasienTb"
                                       id="radioBlnPasienTb1" checked="">
                                <label class="form-check-label" for="radioBlnPasienTb1">Data Bulanan</label>
                            </div>
                            <div class="input-group w-auto">

                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control monthPicker" id="bulanPasienTb"
                                       name="bulanPasienTb" data-toggle="datetimepicker" autocomplete="off"/>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radioBlnPasienTb"
                                       id="radioBlnPasienTb2">
                                <label class="form-check-label" for="radioBlnPasienTb2">Data Tahunan</label>
                            </div>
                            <div class="input-group w-auto">
                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control yearPicker" id="tahunPasienTb"
                                       name="tahunPasienTb" data-toggle="datetimepicker" autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <x-tb.card-grafik-demografi-tb/>
        </div>
        <div class="col-6">
            <x-tb.card-grafik-demografi-kelurahan-tb/>
        </div>
        <div class="col-12">
            <x-tb.datatable.skrining/>
        </div>
        <div class="col-12">
            <x-tb.card-grafik-skrining-tb/>
        </div>
        <div class="col-6">
            <x-tb.card-grafik-capaian-skrining-tb/>
        </div>
        <div class="col-6">
            <x-tb.card-grafik-skrining-by-poli/>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const radioBlnPasienTb = $('input[name="radioBlnPasienTb"]');
        const bulanPasienTb = $('#bulanPasienTb')
        const tahunPasienTb = $('#tahunPasienTb')

        $(document).ready(function () {
            renderTablePasienTb();
            radioBlnPasienTb.trigger('change')
        });

        radioBlnPasienTb.on('change', (e) => {
            const isRadioChecked = $('#radioBlnPasienTb1').is(':checked');
            if (isRadioChecked) {
                $('#bulanPasienTb').prop('disabled', false);
                $('#tahunPasienTb').prop('disabled', true);
            } else {
                $('#bulanPasienTb').prop('disabled', true);
                $('#tahunPasienTb').prop('disabled', false);
            }
        })

        bulanPasienTb.on('change.datetimepicker', (e) => {
            const value = e.currentTarget.value;
            const month = value.split('-')[1];
            const year = value.split('-')[0];
            $('#bulan').html(`<span><strong>${formatBulanTahun(value)}</strong></span>`) //set bulan
            renderTablePasienTb(year, month)
        })

        tahunPasienTb.on('change.datetimepicker', (e) => {
            const value = e.currentTarget.value;
            renderTablePasienTb(value)
        })


        function renderTablePasienTb(year = '', month = '') {
            $('#table-pasien-tb').DataTable({
                ajax: {
                    url: 'pasientb/json',
                    data: {
                        month: month,
                        year: year,
                    }
                },
                processing: true,
                serverSide: true,
                destroy: true,
                scrollY: '40vh',
                scrollX: true,
                deferRender: true,
                ordering: false,
                dom: '<"top"lBf>rt<"bottom"ip><"clear">',
                initComplete: function (settings, json) {
                    toastr.success('Data telah dimuat', 'Berhasil');
                },
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i> <span class="sr-only">Loading...</span>',
                    zeroRecords: "Tidak Ditemukan Data",
                    infoEmpty: "",
                    info: "Menampilkan sebanyak _START_ ke _END_ dari _TOTAL_ data",
                    loadingRecords: "Sedang memuat ...",
                    infoFiltered: "(Disaring dari _MAX_ total baris)",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    paginate: {
                        "first": "Awal",
                        "last": "Akhir",
                        "next": ">",
                        "previous": "<"
                    },
                    search: 'Cari Penyakit: ',
                },
                lengthMenu: [
                    [50, 100, 200, 250, 500, -1],
                    ['50', '100', '200', '250', '500', 'Semua']
                ],
                buttons: [{
                    extend: 'copy',
                    text: '<i class="fas fa-copy"></i> Salin',
                    className: 'btn btn-info',
                    title: 'laporan_pasien_tb{{ date('dmy') }}'
                },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-info',
                        title: 'laporan_pasien_tb{{ date('dmy') }}'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-info',
                        title: 'laporan_pasien_tb{{ date('dmy') }}'
                    },
                ],
                columns: [{
                    data: 'tgl_registrasi',
                    name: 'tgl_registrasi'
                },
                    {
                        data: 'no_rawat',
                        name: 'no_rawat'
                    },
                    {
                        data: 'no_rkm_medis',
                        name: 'no_rkm_medis'
                    },
                    {
                        data: 'nm_pasien',
                        name: 'nm_pasien'
                    },

                    {
                        data: 'tgl_lahir',
                        name: 'tgl_lahir'
                    },
                    {
                        data: 'umurdaftar',
                        name: 'umurdaftar'
                    },
                    {
                        data: 'no_ktp',
                        name: 'no_ktp'
                    },
                    {
                        data: 'jk',
                        name: 'jk'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'no_tlp',
                        name: 'no_tlp'
                    },
                    {
                        data: 'kd_penyakit',
                        name: 'kd_penyakit'
                    },
                    {
                        data: 'nm_penyakit',
                        name: 'nm_penyakit',
                        render: function (data, type, row) {
                            return data;
                        }

                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'nm_poli',
                        name: 'nm_poli'
                    },
                ],
            });
        }

    </script>
@endpush

@foreach ($dokter as $dr => $value)
    <div class="col-lg-6 col-md-12 col-sm-12 d-none" id="cardDokter{{ $value['kd_dokter'] }}">
        <x-card>
            <x-card.header>
                <x.card.title>Kunjungan Poliklinik</x.card.title>
            </x-card.header>
            <x-card.body>
                <canvas id="grafikDokter{{ $value['kd_dokter'] }}" style="height: 40vh; max-height: 60vh"></canvas>
            </x-card.body>
            <x-card.footer>
                <div class="row">
                    <div class="col-lg-4">

                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            </div>
                            <input type="text" id="tahunGrafikSkrining{{ $value['kd_dokter'] }}" class="form-control monthPicker"
                                data-toggle="datetimepicker" aria-describedby="tahunGrafikSkrining{{ $value['kd_dokter'] }}"
                                data-target="#tahunGrafikSkrining{{ $value['kd_dokter'] }}"
                                autocomplete="off" />
                            <button type="button" class="btn btn-primary" onclick="getGrafikKunjunganDokter('{{ $value['kd_dokter'] }}')">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>


                </div>

            </x-card.footer>

        </x-card>
    </div>
@endforeach

@push('scripts')
    <script>
        let chartKunjunganInstance = [];
     


        function getGrafikKunjunganDokter(kd_dokter) {
            const valueInput = document.getElementById(`tahunGrafikSkrining${kd_dokter}`).value;
            const element = document.getElementById(`grafikDokter${kd_dokter}`);
            const card = document.getElementById(`cardDokter${kd_dokter}`);
            const ctx = element.getContext('2d');
            const month = valueInput.split('-')[1];
            const year = valueInput.split('-')[0];


            getGrafikKunjunganByDokter(year, month, kd_dokter).done((response) => {
                destroyChartByDokter(kd_dokter)
                chartKunjunganInstance.push(new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: response.tanggal,
                        datasets: [{
                            label: response.dokter.nm_dokter,
                            backgroundColor: 'rgb(58,161,98)',
                            borderColor: 'rgb(39,121,72)',
                            borderWidth: 1,
                            data: response.jumlah
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: false,
                                min: 0,
                                max: 80,
                            },
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                color: 'grey',
                                formatter: function(value) {
                                    return value;

                                }
                            }
                        },
                    }
                }))
            })
        }


        function getGrafikKunjunganByDokter(year, month, dokter) {
            return $.get(`${url}/grafik/kunjungan/poliklinik/${year}/${month}/${dokter}`).done((response) => {
                console.log(response);
            })
        }


        function renderGrafikKunjunganDokter() {
            const data = @json($dataGrafik);

            data.forEach((item, index) => {
                const element = document.getElementById(`grafikDokter${item.dokter.kd_dokter}`);
                const card = document.getElementById(`cardDokter${item.dokter.kd_dokter}`);
                const ctx = element.getContext('2d');
                card.classList.remove("d-none");

                Chart.register(ChartDataLabels);
                chartKunjunganInstance.push(new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: item.tanggal,
                        datasets: [{
                            label: item.dokter.nm_dokter,
                            backgroundColor: 'rgb(58,161,98)',
                            borderColor: 'rgb(39,121,72)',
                            borderWidth: 1,
                            data: item.jumlah
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: false,
                                min: 0, // Set minimum value of the y-axis
                                max: 80,
                            },
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            datalabels: {
                                anchor: 'end', // Position the label relative to the end of the bar
                                align: 'top', // Align the label at the top
                                color: 'grey',
                                formatter: function(value) {
                                    return value; // Format the label text (optional)
                                }
                            }
                        },
                    }
                }))


            })
        }

        function destroyChartByDokter(kd_dokter) {
            const index = chartKunjunganInstance.findIndex(chart => chart.canvas.id === `grafikDokter${kd_dokter}`);
            if (index !== -1) {
                chartKunjunganInstance[index].destroy();
                chartKunjunganInstance.splice(index, 1); // Remove the destroyed chart from the array
            }
        }


        $(document).ready(() => {

            renderGrafikKunjunganDokter()
        })
    </script>
@endpush

<?php

namespace App\View\Components\Beranda\KunjunganTahunan;

use Illuminate\View\Component;

class CardGrafikRalan extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.beranda.kunjungan-tahunan.card-grafik-ralan');
    }
}

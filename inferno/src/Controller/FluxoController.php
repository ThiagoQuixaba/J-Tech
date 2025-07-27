<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Fluxo Controller
 *
 * @property \App\Model\Table\FluxoTable $Fluxo
 */
class FluxoController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Fluxo->find();
        $fluxo = $this->paginate($query);

        $this->set(compact('fluxo'));
    }
}

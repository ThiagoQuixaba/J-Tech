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

    /**
     * View method
     *
     * @param string|null $id Fluxo id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $fluxo = $this->Fluxo->get($id, contain: []);
        $this->set(compact('fluxo'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $fluxo = $this->Fluxo->newEmptyEntity();
        if ($this->request->is('post')) {
            $fluxo = $this->Fluxo->patchEntity($fluxo, $this->request->getData());
            if ($this->Fluxo->save($fluxo)) {
                $this->Flash->success(__('The fluxo has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fluxo could not be saved. Please, try again.'));
        }
        $this->set(compact('fluxo'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Fluxo id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $fluxo = $this->Fluxo->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $fluxo = $this->Fluxo->patchEntity($fluxo, $this->request->getData());
            if ($this->Fluxo->save($fluxo)) {
                $this->Flash->success(__('The fluxo has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fluxo could not be saved. Please, try again.'));
        }
        $this->set(compact('fluxo'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Fluxo id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $fluxo = $this->Fluxo->get($id);
        if ($this->Fluxo->delete($fluxo)) {
            $this->Flash->success(__('The fluxo has been deleted.'));
        } else {
            $this->Flash->error(__('The fluxo could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

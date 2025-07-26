<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Fornecedor Controller
 *
 * @property \App\Model\Table\FornecedorTable $Fornecedor
 */
class FornecedorController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Fornecedor->find();
        $fornecedor = $this->paginate($query);

        $this->set(compact('fornecedor'));
    }

    /**
     * View method
     *
     * @param string|null $id Fornecedor id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $fornecedor = $this->Fornecedor->get($id, contain: []);
        $this->set(compact('fornecedor'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $fornecedor = $this->Fornecedor->newEmptyEntity();
        if ($this->request->is('post')) {
            $fornecedor = $this->Fornecedor->patchEntity($fornecedor, $this->request->getData());
            if ($this->Fornecedor->save($fornecedor)) {
                $this->Flash->success(__('The fornecedor has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fornecedor could not be saved. Please, try again.'));
        }
        $this->set(compact('fornecedor'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Fornecedor id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $fornecedor = $this->Fornecedor->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $fornecedor = $this->Fornecedor->patchEntity($fornecedor, $this->request->getData());
            if ($this->Fornecedor->save($fornecedor)) {
                $this->Flash->success(__('The fornecedor has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fornecedor could not be saved. Please, try again.'));
        }
        $this->set(compact('fornecedor'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Fornecedor id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $fornecedor = $this->Fornecedor->get($id);
        if ($this->Fornecedor->delete($fornecedor)) {
            $this->Flash->success(__('The fornecedor has been deleted.'));
        } else {
            $this->Flash->error(__('The fornecedor could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

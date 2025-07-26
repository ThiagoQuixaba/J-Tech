<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\ORM\TableRegistry;

/**
 * Produtos Controller
 *
 * @property \App\Model\Table\ProdutosTable $Produtos
 */
class ProdutosController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Produtos->find();
        $produtos = $this->paginate($query);

        $this->set(compact('produtos'));
    }

    /**
     * View method
     *
     * @param string|null $id Produto id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $produto = $this->Produtos->get($id, contain: []);
        $this->set(compact('produto'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void 
     */
    public function add()
    {
        $fluxosTable = TableRegistry::getTableLocator()->get('Fluxo');
        $produto = $this->Produtos->newEmptyEntity();

        if ($this->request->is('post')) {
            $produto = $this->Produtos->patchEntity($produto, $this->request->getData());
            if ($this->Produtos->save($produto)) {
                $fluxo = $fluxosTable->newEmptyEntity();

                $fluxo->lote = $produto->lote; 
                $fluxo->tipo = 'Entrada';
                $fluxo->data = date('Y-m-d'); 

                $fluxosTable->save($fluxo);
                $this->Flash->success(__('Produto salvo com sucesso.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Erro ao salvar o produto.'));
        }

        $categorias = $this->Produtos->Categorias->find('list', ['keyField' => 'nome', 'valueField' => 'nome'])->toArray();

        $fornecedores = $this->Produtos->Fornecedores->find('list', ['keyField' => 'cnpj', 'valueField' => 'cnpj'])->toArray();

        $this->set(compact('produto', 'categorias', 'fornecedores'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Produto id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $produto = $this->Produtos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $produto = $this->Produtos->patchEntity($produto, $this->request->getData());
            if ($this->Produtos->save($produto)) {
                $this->Flash->success(__('The produto has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The produto could not be saved. Please, try again.'));
        }
        
        $categorias = $this->Produtos->Categorias->find('list', ['keyField' => 'nome', 'valueField' => 'nome'])->toArray();

        $fornecedores = $this->Produtos->Fornecedores->find('list', ['keyField' => 'cnpj', 'valueField' => 'cnpj'])->toArray();

        $this->set(compact('produto', 'categorias', 'fornecedores'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Produto id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $produto = $this->Produtos->get($id);
        if ($this->Produtos->delete($produto)) {
            $this->Flash->success(__('The produto has been deleted.'));
        } else {
            $this->Flash->error(__('The produto could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

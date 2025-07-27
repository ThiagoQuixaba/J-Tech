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
            $data = $this->request->getData();
            $produto = $this->Produtos->patchEntity($produto, $data, ['validate' => 'add']);

            if ($this->Produtos->save($produto)) {
                $fluxo = $fluxosTable->newEmptyEntity();

                $fluxo->lote = '<ID: ' . $produto->lote . '>'; 
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
            $data = $this->request->getData();

            if (isset($data['quantidade']) && (int)$data['quantidade'] === 0) {
                $fluxosTable = TableRegistry::getTableLocator()->get('Fluxo');

                $fluxo = $fluxosTable->newEmptyEntity();
                $fluxo->lote = '<ID: ' . $produto->lote . '>';
                $fluxo->tipo = 'Saida';
                $fluxo->data = date('Y-m-d');
                $fluxosTable->save($fluxo);

                $this->Produtos->delete($produto);

                $this->Flash->success(__('Produto com quantidade 0 foi removido.'));

                return $this->redirect(['action' => 'index']);
            }

            $produto = $this->Produtos->patchEntity($produto, $data);

            if ($this->Produtos->save($produto)) {
                $this->Flash->success(__('O produto foi salvo.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('O produto não pôde ser salvo. Por favor, tente novamente.'));
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

        $fluxosTable = TableRegistry::getTableLocator()->get('Fluxo');
        $fluxo = $fluxosTable->newEmptyEntity();

        $fluxo->lote = '<ID: ' . $produto->lote . '>'; 
        $fluxo->tipo = 'Saida';
        $fluxo->data = date('Y-m-d'); 

        $fluxosTable->save($fluxo);

        if ($this->Produtos->delete($produto)) {
            $this->Flash->success(__('Produto deletado com sucesso.'));
        } else {
            $this->Flash->error(__('Erro ao deletar o produto.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}

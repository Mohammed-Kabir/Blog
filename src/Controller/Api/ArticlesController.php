<?php

namespace App\Controller\Api;

use App\Controller\Api\AppController;

class ArticlesController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash'); // Include the FlashComponent
        // Rest-API
        $this->loadComponent('RequestHandler');
    }

    public function index()
    {
        $this->set('articles', $this->Articles->find()->all());
        // Rest-API
        $this->viewBuilder()->setOption('serialize', ['articles']);
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function view($id)
    {
        $article = $this->Articles->get($id);
        $this->set(compact('article'));
        // Rest-API
        $this->viewBuilder()->setOption('serialize', ['article']);
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function add()
    {
        $article = $this->Articles->newEmptyEntity();
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        }
        $this->set('article', $article);

        // Just added the categories list to be able to choose
        // one category for an article
        $categories = $this->Articles->Categories->find('treeList')->all();
        $this->set(compact('categories'));

        // Rest-API
        $this->viewBuilder()->setOption('serialize', ['article']);
    }
    
    public function edit($id = null)
    {
        $article = $this->Articles->get($id);
        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }

        $this->set('article', $article);
        // Rest-API
        $this->viewBuilder()->setOption('serialize', ['article']);
    }

    public function delete($id)
    {
        $this->request->allowMethod(['delete']);

        $article = $this->Articles->get($id);
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The article with id: {0} has been deleted.', h($id)));
            return $this->redirect(['action' => 'index']);
        }
        // Rest-API
        $this->viewBuilder()->setOption('serialize', ['article']);
    }
}

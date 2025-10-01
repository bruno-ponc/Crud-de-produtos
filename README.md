# Crud-de-produtos

#  Mini Sistema de Gestão de Produtos

Este projeto é um **mini sistema de gestão de produtos**, utilizando **PHP, MySQL, JavaScript (AJAX)** e **Bootstrap**.
O objetivo é demonstrar conceitos de **Orientação a Objetos, Relacionamento entre Objetos, Armazenamento em Banco de Dados** e **Autenticação de Usuários**.

---

## Funcionalidades

1. **Cadastro de Usuários**

   * Registro de novos usuários com **armazenamento seguro de senha (hash SHA-256 com salt)**.
   * Login e autenticação de sessão.

2. **Gestão de Produtos**

   * Cadastro de produtos (com nome e preço).
   * Associação dos produtos com seu fornecedor.
   * Listagem dos produtos em tabela dinâmica com AJAX.
   * **Remoção de produtos** cadastrados.

3. **Gestão de Fornecedores**

   * Cadastro e listagem de fornecedores.
   * Relacionamento com produtos.

4. **Cesta (Carrinho de Compras)**

   * O usuário pode selecionar produtos via **checkbox**.
   * Adicionar os produtos à cesta (1 unidade por produto).
   * Visualizar a cesta, com:

     * Produtos selecionados.
     * Quantidade de itens.
     * Valor total da compra.

5. **Interface**

   * Menus de navegação para acessar:

     * Usuários
     * Produtos
     * Fornecedores
     * Cesta
   * Interface responsiva com **Bootstrap 5**.

---

## Estrutura do Banco de Dados

* **usuários** → guarda login, salt e senha hash.
* **fornecedores** → lista de fornecedores.
* **produtos** → catálogo de produtos, ligados a fornecedores.
* **cestas** → carrinho de compras do usuário.
* **itens_cesta** → produtos vinculados a uma cesta.

![Image](https://github.com/user-attachments/assets/0e0bdb0a-652a-4b5f-aa2f-88ca977e03ef)

---

## Tecnologias Utilizadas

* **PHP 8+**
* **MySQL**
* **Bootstrap 5**
* **JavaScript (AJAX / Fetch API)**
* **HTML5 / CSS3**

---

## Como Executar o Projeto

1. Clone ou copie o projeto para o diretório do seu servidor local (ex: `htdocs` no XAMPP).
2. Copie o script do arquivo `SQL.txt` no seu MySQL para criar as tabelas.
3. Ajuste o arquivo `db.php` com os dados de conexão do seu banco.
4. Acesse no navegador:

   ```
   http://localhost/mini_gestao/index.php
   ```

---

## Estrutura de Arquivos

```
mini_gestao/
│── index.php          # Login e cadastro de usuários
│── produtos.php       # Tela de produtos
│── cesta.php          # Tela da cesta de compras
│── api.php            # API (AJAX) para CRUD
│── db.php             # Conexão com banco de dados
│── assets/
│    ├── app.js        # Lógica JavaScript e AJAX
│── SQL.txt            # Script do banco de dados

```

---

## Exemplos Visuais

Figma Esboço 
<img width="1360" height="574" alt="image" src="https://github.com/user-attachments/assets/1541786d-1b5b-42c6-af57-6baa7fcc0313" />
<img width="1364" height="575" alt="image" src="https://github.com/user-attachments/assets/0df71c14-6104-49fe-a4ad-d21655cbf3a7" />
<img width="1363" height="570" alt="image" src="https://github.com/user-attachments/assets/a84240ac-5572-40fb-a795-aee41151ecb9" />


# 🏰 Fortress Inventory System

> Um sistema de gestão de estoque (ERP) robusto e eficiente desenvolvido em PHP, focado no controle de produtos, fornecedores e geração de relatórios gerenciais.

![Status do Projeto](https://img.shields.io/badge/Status-Em_Desenvolvimento-yellow)
![PHP Version](https://img.shields.io/badge/PHP-8.0+-777BB4)
![Database](https://img.shields.io/badge/MySQL-MariaDB-003545)

## 📋 Sobre o Projeto

O **Fortress Inventory** é uma aplicação web desenvolvida para auxiliar pequenas empresas no controle de estoque. O sistema permite o cadastro de produtos, categorias e fornecedores, além de monitorar níveis de estoque e gerar relatórios em PDF para facilitar a tomada de decisão.

Este projeto foi desenvolvido como parte de estudos acadêmicos em Engenharia de Software, focando em arquitetura MVC (Model-View-Controller) simplificada e manipulação de dados com segurança.

## ✨ Funcionalidades Principais

* **🔐 Autenticação Segura:** Sistema de login para acesso restrito.
* **📦 Gestão de Produtos:** CRUD completo (Criar, Ler, Atualizar, Deletar) de produtos com suporte a imagens e códigos SKU.
* **📊 Dashboard:** Visão geral com métricas rápidas do sistema.
* **⚠️ Alertas de Estoque:** Identificação visual automática de produtos com estoque baixo (≤ 5 unidades).
* **📄 Relatórios em PDF:** Módulo avançado para exportação de dados usando a biblioteca `Dompdf`, com filtros por:
    * Categoria
    * Fornecedor
    * Estoque Baixo
* **🗂️ Organização:** Cadastro de Categorias e Fornecedores.

## 🚀 Tecnologias Utilizadas

* **Backend:** PHP (Vanilla/Nativo)
* **Banco de Dados:** MySQL / MariaDB
* **Frontend:** HTML5, CSS3, JavaScript
* **PDF Engine:** [Dompdf](https://github.com/dompdf/dompdf) via Composer
* **Servidor Local:** XAMPP (Apache)

## 📂 Estrutura do Projeto

```text
/fortress-inventory
├── actions/           # Lógica de processamento (Back-end)
│   ├── gerar_relatorio.php
│   └── ...
├── config/            # Arquivos de configuração
│   └── db.php         # Conexão com o Banco de Dados
├── css/               # Estilos da aplicação
├── includes/          # Componentes reutilizáveis (Menu, Auth)
├── vendor/            # Dependências (Composer/Dompdf)
├── uploads/           # Imagens dos produtos
├── dashboard.php      # Página inicial
├── produtos.php       # Listagem de produtos
├── relatorios.php     # Interface de relatórios
└── index.php          # Tela de Login

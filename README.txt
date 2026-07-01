=====================================
MedStock Solutions — SIBDAS
Sistema de Gestão de Inventário Hospitalar de Equipamentos Médicos
=====================================

Nome do projeto: MedStock Solutions
Nome do estudante: Diogo Almeida
Número do estudante: 1231184
Unidade curricular: Sistemas de Informação e Base de Dados Aplicados à Saúde
Curso: LEBIOM — Licenciatura em Engenharia Biomédica
Instituição: ISEP — Instituto Superior de Engenharia do Porto
Ano letivo: 2025/2026

=====================================
DESCRIÇÃO
=====================================

Aplicação web para gestão do inventário hospitalar de equipamentos médicos,
desenvolvida com PHP, MySQL, Bootstrap 5, jQuery e DataTables.

O sistema inclui duas componentes:
- Área Pública: página institucional do MedStock Solutions, com informação
  sobre o sistema, serviços, métricas e contactos, gerida dinamicamente
  através do backoffice.
- Área Privada: aplicação de gestão do inventário hospitalar com autenticação
  real por perfil (Administrador e Técnico).

Funcionalidades principais:
- Gestão completa de equipamentos médicos (CRUD com wizard multi-step)
- Dashboard com KPIs, gráficos interativos e alertas de gestão em tempo real
- Gestão de localizações com hierarquia Edifício > Piso > Serviço > Sala
- Gestão de fornecedores, documentação, garantias e contratos
- Exportação de fichas de equipamentos em CSV, JSON e PDF
- Geração de etiquetas com QR Code para identificação física
- Registo de acessos (logins e logouts) na base de dados
- Backoffice para gestão de conteúdos da área pública
- Filtros interativos no dashboard com navegação direta para equipamentos
- Perfis de acesso condicionais (Administrador / Técnico)

=====================================
INSTALAÇÃO E EXECUÇÃO
=====================================

1. Requisitos:
   - Laragon (ou XAMPP) com Apache e PHP 8.x
   - MySQL 8.x
   - Browser moderno (Google Chrome, Firefox ou Edge)
   - Ligação à internet (para QR Codes e CDN do Bootstrap)

2. Instalação:
   - Instalar o Laragon caso ainda não esteja instalado (https://laragon.org)
   - Copiar a pasta medstock-solutions para:
     C:\laragon\www\sibdas\1231184\
   - Resultado final: C:\laragon\www\sibdas\1231184\medstock-solutions\

3. Configuração da base de dados:
   - Servidor remoto: vsgate-s1.dei.isep.ipp.pt:10464
   - Base de dados: db1231184
   - As credenciais estão definidas em config/config.php
   - A base de dados já está configurada e acessível no servidor do ISEP.
   - Para recriar a base de dados localmente, importar o ficheiro:
       medstock_db.sql
     via HeidiSQL, phpMyAdmin ou linha de comandos MySQL.

4. Execução da aplicação:
   - Abrir o Laragon e clicar em "Start All"
   - Confirmar que o Apache e o MySQL aparecem ativos
   - Abrir o browser e aceder a:

       Área pública:
       http://127.0.0.1/sibdas/1231184/medstock-solutions/public/index.php

       Área privada (login):
       http://127.0.0.1/sibdas/1231184/medstock-solutions/login/login.php

5. Notas importantes:
   - O Laragon tem de estar em execução para a aplicação funcionar
   - O ficheiro config/config.php contém as credenciais da BD e as chaves
     de encriptação — não deve ser partilhado em ambientes de produção
   - O BASE_URL está definido como: /sibdas/1231184/medstock-solutions
   - Os ficheiros PDF de documentação estão em: assets/docs/
   - Os QR Codes são gerados via API externa (api.qrserver.com) e requerem
     ligação à internet

=====================================
CREDENCIAIS DE ACESSO
=====================================

Perfil: Administrador
   Email:    admin@medstock.pt
   Password: admin123
   Acesso:   Todas as funcionalidades — equipamentos, localizações,
             fornecedores, documentação, garantias, dashboard,
             gestão de conteúdos da área pública e registo de acessos.

Perfil: Técnico
   Email:    tecnico@medstock.pt
   Password: tecnico123
   Acesso:   Equipamentos, localizações, documentação, garantias
             e dashboard. Sem acesso à gestão de conteúdos e
             à página de fornecedores.

=====================================
ESTRUTURA DE DIRETÓRIOS
=====================================

medstock-solutions/
├── assets/
│   ├── bootstrap/          — Framework Bootstrap 5
│   ├── css/
│   │   └── 1231184.css     — Estilos personalizados
│   ├── datatables/         — Biblioteca DataTables
│   ├── docs/               — Documentos PDF dos equipamentos
│   │   ├── certificados/
│   │   ├── contratos/
│   │   ├── declaracoes/
│   │   ├── faturas/
│   │   ├── manuais/
│   │   └── outros/
│   ├── fontawesome/        — Ícones Font Awesome
│   ├── fonts/              — Fontes (Titillium Web, Inter)
│   ├── img/                — Imagens e logótipo
│   ├── jQuery/             — Biblioteca jQuery
│   └── js/
│       └── 1231184.js      — Scripts JavaScript centralizados
├── config/
│   └── config.php          — Configuração da BD e constantes
├── login/
│   ├── login.php           — Página de login
│   └── logout.php          — Processamento do logout
├── private/
│   ├── includes/
│   │   ├── footer.php      — Rodapé reutilizável
│   │   ├── funcoes.php     — Funções PHP reutilizáveis
│   │   ├── header.php      — Cabeçalho reutilizável
│   │   └── sidebar.php     — Sidebar condicional por perfil
│   ├── processa_login.php  — Processamento do login (PDO + bcrypt)
│   └── views/
│       ├── conteudo/       — Backoffice da área pública
│       ├── dashboard/      — Dashboard com KPIs e gráficos
│       ├── documentacao/   — Listagem de documentos
│       ├── equipamentos/   — CRUD de equipamentos + APIs AJAX
│       │   └── api/        — Endpoints PHP (get, atualizar, remover, exportar)
│       ├── fornecedores/   — Gestão de fornecedores
│       ├── garantias/      — Listagem de garantias e contratos
│       └── localizacoes/   — Gestão de localizações hierárquicas
└── public/
    └── index.php           — Área pública (página institucional)

=====================================
TESTES PRINCIPAIS
=====================================

1. AUTENTICAÇÃO
   - Testar login com cada um dos 2 perfis acima
   - Testar login com email errado (mensagem: "email não registado")
   - Testar login com password errada (mensagem: "password incorreta")
   - Testar logout (destrói a sessão e redireciona para o login)
   - Testar acesso direto a páginas privadas sem login (deve redirecionar)
   - Verificar registo de eventos na tabela log_acessos (via HeidiSQL)

2. EQUIPAMENTOS (CRUD completo)
   - Clicar em "+ Novo Equipamento" e usar o botão "Preencher Demo"
     para preenchimento automático dos campos de texto
   - Completar manualmente os campos de dropdown (localização, fabricante)
   - Guardar e verificar que o equipamento aparece na listagem
   - Clicar em "Ver" e testar as exportações CSV, JSON e PDF
   - Clicar em "Etiqueta" para gerar etiqueta com QR Code
   - Testar o modal "Editar" — alterar dados e guardar
   - Testar o botão "Remover" (soft delete — marca como Abatido)

3. DASHBOARD
   - Verificar KPIs: total, ativos, manutenção, calibração, inativos
   - Clicar nos sub-valores do card "Inventário Global" e verificar
     que redireciona para a lista com o filtro aplicado
   - Clicar no card "Suporte de Vida" e verificar filtro
   - Clicar numa fatia do gráfico "Equipamentos por Categoria"
   - Clicar numa barra do gráfico "Equipamentos por Serviço"
   - Verificar alertas de gestão (garantias expiradas, doc. em falta)

4. LOCALIZAÇÕES
   - Criar nova localização (Edifício > Piso > Serviço > Sala)
   - Editar e eliminar (soft delete com confirmação)

5. FORNECEDORES (apenas Administrador)
   - Criar, editar e eliminar fornecedor
   - Verificar validação de NIF e email

6. DOCUMENTAÇÃO
   - Consultar documentos associados a equipamentos
   - Testar o botão "Descarregar" nos documentos existentes

7. GARANTIAS
   - Verificar listagem de garantias e contratos
   - Verificar badges de estado (Ativa, Expirada, A expirar)

8. GESTÃO DE CONTEÚDOS (apenas Administrador)
   - Editar textos da área pública (hero, serviços, contactos, etc.)
   - Verificar que as alterações refletem na área pública

9. SEGURANÇA
   - Passwords armazenadas com password_hash / password_verify (bcrypt)
   - PDO com prepared statements em todas as queries
   - IDs encriptados com AES-256-CBC nos URLs e atributos data-id
   - Emails encriptados com AES_ENCRYPT / AES_DECRYPT no MySQL
   - Registo de tentativas de login e logout em log_acessos
   - Controlo de acesso por perfil (sidebar + redirect_if_not_profile)

=====================================
TECNOLOGIAS UTILIZADAS
=====================================

Linguagens e frameworks:
- PHP 8.x (backend)
- HTML5 + CSS3 (estrutura e estilos)
- JavaScript (interatividade e AJAX)
- MySQL 8.x (base de dados relacional)

Bibliotecas e componentes frontend:
- Bootstrap 5 (layout responsivo e componentes UI)
- jQuery 3.x (manipulação do DOM e AJAX)
- DataTables (tabelas interativas com paginação e pesquisa)
- Chart.js (gráficos do dashboard)
- Font Awesome (ícones vetoriais)

Ferramentas de desenvolvimento:
- Visual Studio Code (editor de código)
- Laragon (ambiente de desenvolvimento local)
- HeidiSQL (administração da base de dados)
- Git (controlo de versões)

Segurança e acesso a dados:
- PDO com prepared statements (acesso seguro à base de dados)
- OpenSSL AES-256-CBC (encriptação de IDs nos URLs)
- AES_ENCRYPT / AES_DECRYPT MySQL (encriptação de emails na BD)
- password_hash / password_verify PHP (armazenamento seguro de passwords)

=====================================
NOTAS ADICIONAIS
=====================================

- O ficheiro config/config.php contém credenciais da BD e chaves de
  encriptação. Em produção, este ficheiro não deve estar no repositório.
- A coluna email da tabela utilizadores é do tipo BLOB por usar
  AES_ENCRYPT ao nível do MySQL.
- O sistema utiliza soft delete (estado = 'Abatido') nos equipamentos,
  preservando o histórico de todos os registos.
- O JavaScript está centralizado em assets/js/1231184.js, exceto blocos
  PHP-dependentes que estão inline nas respetivas páginas.
- O histórico de movimentações é registado automaticamente em
  historico_movimentacoes sempre que um equipamento muda de serviço.
- O registo de acessos (log_acessos) é automático — não requer ação
  do utilizador. Os registos podem ser consultados via HeidiSQL.